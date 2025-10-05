<?php

namespace App\Services;

use App\Models\Tracking;
use App\Models\TrackingEvent;
use App\Support\RouteFinder;
use Carbon\Carbon;

class TimelineService
{
public function generateFor(Tracking $t): void
{
    $route = RouteFinder::find($t->origin_state, $t->destination_state, $t->country_type);

    $days = max(1, $t->start_date->diffInDays($t->expected_delivery_date));

    // Load correct hub map based on country type
    $hubs = match ($t->country_type) {
        1 => config('hubs_us'),
        2 => config('hubs_ca'),
        3 => config('hubs_mx'),
        default => [],
    };

    // Prefer actual frontend cities; fallback to hub map
    $originCity = $t->origin_city ?? ($hubs[$t->origin_state] ?? 'Origin');
    $destinationCity = $t->destination_city ?? ($hubs[$t->destination_state] ?? 'Destination');

    // Picked up
    TrackingEvent::create([
        'tracking_id' => $t->id,
        'event_date' => $t->start_date->copy()->setTime(9, 0),
        'location_city' => $originCity,
        'location_state' => $t->origin_state,
        'status' => 'picked_up',
        'message' => "Shipment picked up in {$originCity}, {$t->origin_state}",
    ]);

    // Transit stops
    $midStates = array_slice($route, 1, -1);
    $slots = max(0, $days - 2);

    foreach ($midStates as $i => $state) {
        $date = $t->start_date->copy()->addDays(1 + floor(($i + 1) / count($midStates) * $slots));
        $city = $hubs[$state] ?? 'Transit Hub';

        TrackingEvent::create([
            'tracking_id' => $t->id,
            'event_date' => $date->copy()->setTime(10, 0),
            'location_city' => $city,
            'location_state' => $state,
            'status' => 'arrived_at_facility',
            'message' => "Arrived at facility in {$city}, {$state}",
        ]);

        TrackingEvent::create([
            'tracking_id' => $t->id,
            'event_date' => $date->copy()->setTime(14, 0),
            'location_city' => $city,
            'location_state' => $state,
            'status' => 'in_transit',
            'message' => "Departed facility in transit through {$city}, {$state}",
        ]);
    }

    // Out for delivery
    TrackingEvent::create([
        'tracking_id' => $t->id,
        'event_date' => $t->expected_delivery_date->copy()->setTime(8, 0),
        'location_city' => $destinationCity,
        'location_state' => $t->destination_state,
        'status' => 'out_for_delivery',
        'message' => "Out for delivery in {$destinationCity}, {$t->destination_state}",
    ]);

    // Delivered
    TrackingEvent::create([
        'tracking_id' => $t->id,
        'event_date' => $t->expected_delivery_date->copy()->setTime(18, 30),
        'location_city' => $destinationCity,
        'location_state' => $t->destination_state,
        'status' => 'delivered',
        'message' => "Delivered in {$destinationCity}, {$t->destination_state}",
    ]);

    $t->update(['status' => 'in_transit']);
}

    public function applyDelay(Tracking $t, Carbon $delayedUntil): void
    {
        $now = Carbon::now();
        $futureEvents = $t->events()->where('event_date', '>', $now)->orderBy('event_date')->get();

        if ($futureEvents->isEmpty()) return;

        $firstFuture = $futureEvents->first();
        $anchor = $delayedUntil->copy()->setTime(9, 0);
        $shift = $anchor->diff($firstFuture->event_date); // CarbonInterval

        foreach ($futureEvents as $event) {
            $event->event_date = $event->event_date->add($shift);
            $event->save();
        }

        // Update expected_delivery_date
        $t->expected_delivery_date = $t->expected_delivery_date->add($shift);
        $t->save();
    }
}