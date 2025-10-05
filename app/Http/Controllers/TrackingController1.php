<?php

namespace App\Http\Controllers;

use App\Models\Tracking;
use App\Services\TimelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TrackingController1 extends Controller
{
public function create(Request $request, TimelineService $timeline)
{
    $countryType = $request->input('country_type');

    $stateConfig = match ((int) $countryType) {
        1 => config('state_neighbors_us'),
        2 => config('state_neighbors_ca'),
        3 => config('state_neighbors_mx'),
    };

    $data = $request->validate([
        'carrier' => 'required|string|in:UPS,USPS,Canada Post,MEXICO',
        'country_type' => 'required|integer|in:1,2,3',
        'origin_state' => [
            'required',
            'string',
            Rule::in(array_keys($stateConfig)),
        ],
        'destination_state' => [
            'required',
            'string',
            Rule::in(array_keys($stateConfig)),
        ],
        'start_date' => 'required|date',
        'expected_delivery_date' => 'required|date|after_or_equal:start_date',
        'destination_city' => 'nullable|string',
        'origin_zip' => 'nullable|string',
        'destination_zip' => 'nullable|string',
    ]);

    $trackingNumber = $this->generateTrackingNumber();

    $tracking = Tracking::create(array_merge($data, [
        'tracking_number' => $trackingNumber,
        'status' => 'created',
    ]));

    $timeline->generateFor($tracking);

    return response()->json([
        'tracking_number' => $trackingNumber,
        'message' => 'Tracking created successfully'
    ], 201);
}


    public function show(string $trackingNumber)
    {
        $now = Carbon::now('America/New_York');
        // $tracking = Tracking::with('events')->where('tracking_number', $trackingNumber)->firstOrFail();
        
        // Eager load only necessary columns from events, ordered by date
        $tracking = Tracking::with(['events' => function ($query) use ($now) {
            $query->where('event_date', '<=', $now)
                ->orderBy('event_date', 'asc')
                ->select('id', 'tracking_id', 'event_date', 'status', 'message', 'location_city', 'location_state');
        }])
        ->where('tracking_number', $trackingNumber)
        ->firstOrFail();

        // Update status to delivered if conditions met
        if (
            $tracking->status === 'in_transit' &&
            is_null($tracking->delay_reason) &&
            $now->greaterThanOrEqualTo($tracking->expected_delivery_date)
        ) {
            $tracking->update(['status' => 'delivered']);
        }

        // Revert to in_transit if status is delivered but expected date not reached
        if (
            $tracking->status === 'delivered' &&
            is_null($tracking->delay_reason) &&
            $now->lessThan($tracking->expected_delivery_date)
        ) {
            $tracking->update(['status' => 'in_transit']);
        }

        // Format events once
        $events = $tracking->events->transform(fn($e) => [
            'date' => $e->event_date->format('Y-m-d H:i:s'),
            'location' => trim("{$e->location_city}, {$e->location_state}", ', '),
            'status' => $e->status,
            'message' => $e->message,
        ]);

        return response()->json([
            'tracking_number' => $tracking->tracking_number,
            'carrier' => $tracking->carrier,
            'country_type' => $tracking->country_type,
            'origin' => [
                'state' => $tracking->origin_state, 
                'city' => $tracking->origin_city
            ],
            'destination' => [
                'state' => $tracking->destination_state, 
                'city' => $tracking->destination_city
            ],
            'status' => $tracking->status,
            'expected_delivery_date' => optional($tracking->expected_delivery_date)->toDateString(),
            'delayed_until' => optional($tracking->delayed_until)->toDateString(),
            'delay_reason' => $tracking->delay_reason,
            'events' => $events,
        ]);
    }



    public function delay(Request $request, string $trackingNumber, TimelineService $timeline)
    {
        $data = $request->validate([
            'delayed_until' => 'required|date|after:now',
            'reason' => 'nullable|string|max:255',
        ]);

        $t = Tracking::where('tracking_number', $trackingNumber)->firstOrFail();

        $t->update([
            'delayed_until' => $data['delayed_until'],
            'delay_reason' => $data['reason'] ?? null,
            'status' => 'delayed',
        ]);

        $now = Carbon::now();
        $lastPast = $t->events()->where('event_date', '<=', $now)->latest('event_date')->first();
        $location_city = $lastPast
        ? $lastPast->location_city
        : (
            $t->origin_city
            ?? match ($t->country_type) {
                1 => config('hubs_us')[$t->origin_state] ?? 'Origin',
                2 => config('hubs_ca')[$t->origin_state] ?? 'Origin',
                3 => config('hubs_mx')[$t->origin_state] ?? 'Origin',
                default => 'Origin',
            }
        );

        $location_state = $lastPast ? $lastPast->location_state : $t->origin_state;

        \App\Models\TrackingEvent::create([
            'tracking_id' => $t->id,
            'event_date' => $now,
            'location_city' => $location_city,
            'location_state' => $location_state,
            'status' => 'delay',
            'message' => $t->delay_reason ? "Delay: {$t->delay_reason}" : "Shipment delayed until {$t->delayed_until->toDateString()}",
        ]);

        $timeline->applyDelay($t, Carbon::parse($data['delayed_until']));

        return response()->json([
            'tracking_number' => $t->tracking_number,
            'message' => "Tracking delayed until {$t->delayed_until->toDateString()}",
            'reason' => $t->delay_reason
        ]);
    }

    private function generateTrackingNumber(): string
    {
        $prefix = 'CPS';
        $date = now()->format('ymd');

        do {
            $rand = strtoupper(Str::random(8)); // longer random for more uniqueness
            $number = $prefix . $date . $rand;
            $exists = Tracking::where('tracking_number', $number)->exists();
        } while ($exists);

        return $number;
    }
}