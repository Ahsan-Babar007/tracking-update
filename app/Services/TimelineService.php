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
        $hubs = $this->getHubs($t->country_type);

        // Day 0: Picked up
        $pickupLocation = $hubs[$t->origin_state] ?? $t->origin_state;

        TrackingEvent::create([
            'tracking_id'   => $t->id,
            'event_date'    => $t->start_date->copy()->setTime(9, 0),
            'location_city' => $hubs[$t->origin_state] ?? $t->origin_city ?? 'Origin',
            'location_state'=> $t->origin_state,
            'status'        => 'picked_up',
            'message'       => "Shipment picked up in {$pickupLocation}, {$t->origin_state}",
        ]);

        // Mid hops (optimized with arrive/depart for realism)
        $midStates = array_slice($route, 1, -1);
        $slots = max(0, $days - 2);
        foreach ($midStates as $i => $state) {
            $dayOffset = floor(($i + 1) / count($midStates) * $slots);
            $date = $t->start_date->copy()->addDays(1 + $dayOffset);

            // Arrived at facility (morning)
            $city = $hubs[$state] ?? $state;
            TrackingEvent::create([
                'tracking_id' => $t->id,
                'event_date' => $date->copy()->setTime(10, 0),
                'location_city' => $hubs[$state] ?? 'Transit Hub',
                'location_state' => $state,
                'status' => 'arrived_at_facility',
                'message' => "Arrived at facility in {$city}, {$state}",
            ]);

            // In transit / departed (afternoon)
            $city = $hubs[$state] ?? $state;
            TrackingEvent::create([
                'tracking_id' => $t->id,
                'event_date' => $date->copy()->setTime(14, 0),
                'location_city' => $hubs[$state] ?? 'Transit Hub',
                'location_state' => $state,
                'status' => 'in_transit',
                'message' => "Departed facility in transit through {$city}, {$state}",
            ]);
        }

        // Out for delivery (morning of last day)
        $city = $hubs[$t->destination_state] ?? $t->destination_city ?? 'Destination';
        TrackingEvent::create([
            'tracking_id'   => $t->id,
            'event_date'    => $t->expected_delivery_date->copy()->setTime(8, 0),
            'location_city' => $city,
            'location_state'=> $t->destination_state,
            'status'        => 'out_for_delivery',
            'message'       => "Out for delivery in {$city}, {$t->destination_state}",
        ]);

        // Delivered (evening of last day)
        $city = $hubs[$t->destination_state] ?? $t->destination_state;
        TrackingEvent::create([
            'tracking_id' => $t->id,
            'event_date' => $t->expected_delivery_date->copy()->setTime(18, 30),
            'location_city' => $hubs[$t->destination_state] ?? $t->destination_city ?? 'Destination',
            'location_state' => $t->destination_state,
            'status' => 'delivered',
            'message' => "Delivered in {$city}, {$t->destination_state}",
        ]);

        $t->update(['status' => 'in_transit']);
    }

    private function getHubs(int $countryType): array
    {
        return $countryType === 1 ? config('hubs_us') : ($countryType === 2 ? config('hubs_ca') : config('hubs_mx'));
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
