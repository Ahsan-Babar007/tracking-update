<?php

namespace App\Support;

class RouteFinder
{
    public static function find(string $origin, string $destination, int $countryType): array
    {
        
        $map = match ($countryType) {
            1 => config('state_neighbors_us'),
            2 => config('state_neighbors_ca'),
            3 => config('state_neighbors_mx'),
            default => [],
        };

        if (!isset($map[$origin]) || !isset($map[$destination])) {
            return [$origin, $destination]; // fallback
        }

        $queue = [[$origin]];
        $visited = [$origin => true];

        while ($queue) {
            $path = array_shift($queue);
            $node = end($path);

            if ($node === $destination) {
                return $path; // e.g., ['TX','AR','MO','IN','OH']
            }

            foreach ($map[$node] ?? [] as $neighbor) {
                if (!isset($visited[$neighbor])) {
                    $visited[$neighbor] = true;
                    $new = $path;
                    $new[] = $neighbor;
                    $queue[] = $new;
                }
            }
        }

        return [$origin, $destination]; // no path found
    }
}