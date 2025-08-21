<?php

namespace App\Support;

class RouteFinder
{
    public static function find(string $origin, string $destination, int $countryType): array
    {
        $map = $countryType === 1
            ? config('state_neighbors_us')
            : config('state_neighbors_ca');

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