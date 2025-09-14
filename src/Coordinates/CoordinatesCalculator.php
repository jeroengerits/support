<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

class CoordinatesCalculator
{
    public static function calculateDistance(Coordinates $a, Coordinates $b, DistanceUnit $unit = DistanceUnit::KILOMETERS): float
    {
        // Early return for identical coordinates
        if ($a->isEqual($b)) {
            return 0.0;
        }

        $earthRadius = match ($unit) {
            DistanceUnit::KILOMETERS => 6371.0,
            DistanceUnit::MILES => 3958.8,
        };

        // Convert degrees to radians
        $lat1 = deg2rad($a->latitude->value);
        $lon1 = deg2rad($a->longitude->value);
        $lat2 = deg2rad($b->latitude->value);
        $lon2 = deg2rad($b->longitude->value);

        // Calculate differences
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        // Haversine formula
        $sinDlat = sin($dlat / 2);
        $sinDlon = sin($dlon / 2);
        $cosLat1 = cos($lat1);
        $cosLat2 = cos($lat2);

        $a = $sinDlat * $sinDlat + $cosLat1 * $cosLat2 * $sinDlon * $sinDlon;
        $c = 2 * asin(sqrt($a));

        // Calculate distance
        return $earthRadius * $c;
    }
}
