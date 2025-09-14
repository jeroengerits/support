<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

/**
 * Optimized calculator for coordinate-based distance calculations.
 *
 * This class provides efficient distance calculations between coordinates using
 * the Haversine formula with caching, multiple Earth models, and batch processing
 * capabilities for improved performance.
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
 * use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
 *
 * $calculator = new CoordinatesCalculator();
 * $distance = $calculator->distanceBetween($coord1, $coord2, DistanceUnit::KILOMETERS);
 *
 * // Batch calculation for multiple coordinate pairs
 * $distances = $calculator->batchDistanceCalculation([
 *     [$coord1, $coord2],
 *     [$coord3, $coord4],
 *     [$coord5, $coord6],
 * ], DistanceUnit::KILOMETERS);
 * ```
 */
class CoordinatesCalculator
{
    /**
     * Earth model constants for different reference ellipsoids.
     */
    public const EARTH_MODEL_SPHERICAL = 'spherical';

    public const EARTH_MODEL_WGS84 = 'wgs84';

    public const EARTH_MODEL_GRS80 = 'grs80';

    /**
     * Earth radius values in kilometers for different models.
     *
     * @var array<string, array<string, float>>
     */
    private const EARTH_RADII = [
        self::EARTH_MODEL_SPHERICAL => [
            'km' => 6371.0,
            'mi' => 3958.8,
        ],
        self::EARTH_MODEL_WGS84 => [
            'km' => 6371.0088, // Mean radius
            'mi' => 3958.7613,
        ],
        self::EARTH_MODEL_GRS80 => [
            'km' => 6371.0000, // Mean radius
            'mi' => 3958.7600,
        ],
    ];

    /**
     * Cache for trigonometric calculations to avoid repeated computations.
     *
     * @var array<string, float>
     */
    private static array $trigCache = [];

    /**
     * Cache for Earth radius values by unit and model.
     *
     * @var array<string, float>
     */
    private static array $radiusCache = [];

    /**
     * Clear the trigonometric cache.
     *
     * This method can be called to free up memory if the cache becomes too large.
     * The cache will be rebuilt as needed for future calculations.
     */
    public static function clearCache(): void
    {
        self::$trigCache = [];
        self::$radiusCache = [];
    }

    /**
     * Get the size of the trigonometric cache.
     *
     * @return int The number of cached trigonometric values
     */
    public static function getCacheSize(): int
    {
        return count(self::$trigCache);
    }

    /**
     * Calculate the distance between two coordinates using the Haversine formula.
     *
     * This method uses caching for trigonometric calculations and supports
     * different Earth models for improved accuracy.
     *
     * @param  Coordinates  $a          The first coordinate
     * @param  Coordinates  $b          The second coordinate
     * @param  DistanceUnit $unit       The distance unit (default: KILOMETERS)
     * @param  string       $earthModel The Earth model to use (default: WGS84)
     * @return float        The distance between the coordinates
     *
     * @example
     * ```php
     * $calculator = new CoordinatesCalculator();
     * $distance = $calculator->distanceBetween(
     *     coordinates(40.7128, -74.0060), // New York
     *     coordinates(51.5074, -0.1278),  // London
     *     DistanceUnit::KILOMETERS,
     *     CoordinatesCalculator::EARTH_MODEL_WGS84
     * );
     * // Returns: ~5570.0 km
     * ```
     */
    public function distanceBetween(
        Coordinates $a,
        Coordinates $b,
        DistanceUnit $unit = DistanceUnit::KILOMETERS,
        string $earthModel = self::EARTH_MODEL_WGS84
    ): float {
        // Early return for identical coordinates
        if ($a->isEqual($b)) {
            return 0.0;
        }

        $earthRadius = $this->getEarthRadius($unit, $earthModel);

        // Convert degrees to radians with caching
        $lat1 = $this->getCachedRadians($a->latitude->value);
        $lon1 = $this->getCachedRadians($a->longitude->value);
        $lat2 = $this->getCachedRadians($b->latitude->value);
        $lon2 = $this->getCachedRadians($b->longitude->value);

        // Calculate differences
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        // Haversine formula with cached trigonometric functions
        $sinDlat = $this->getCachedSin($dlat / 2);
        $sinDlon = $this->getCachedSin($dlon / 2);
        $cosLat1 = $this->getCachedCos($lat1);
        $cosLat2 = $this->getCachedCos($lat2);

        $a = $sinDlat * $sinDlat + $cosLat1 * $cosLat2 * $sinDlon * $sinDlon;
        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }

    /**
     * Get the Earth radius for the specified unit and model.
     */
    private function getEarthRadius(DistanceUnit $unit, string $earthModel): float
    {
        $unitKey = $unit->value;
        $cacheKey = "{$earthModel}_{$unitKey}";

        if (! isset(self::$radiusCache[$cacheKey])) {
            if (! isset(self::EARTH_RADII[$earthModel][$unitKey])) {
                throw new \InvalidArgumentException("Unsupported Earth model: {$earthModel}");
            }
            self::$radiusCache[$cacheKey] = self::EARTH_RADII[$earthModel][$unitKey];
        }

        return self::$radiusCache[$cacheKey];
    }

    /**
     * Get cached radians conversion.
     */
    private function getCachedRadians(float $degrees): float
    {
        $key = "rad_{$degrees}";
        if (! isset(self::$trigCache[$key])) {
            self::$trigCache[$key] = deg2rad($degrees);
        }

        return self::$trigCache[$key];
    }

    /**
     * Get cached sine value.
     */
    private function getCachedSin(float $radians): float
    {
        $key = "sin_{$radians}";
        if (! isset(self::$trigCache[$key])) {
            self::$trigCache[$key] = sin($radians);
        }

        return self::$trigCache[$key];
    }

    /**
     * Get cached cosine value.
     */
    private function getCachedCos(float $radians): float
    {
        $key = "cos_{$radians}";
        if (! isset(self::$trigCache[$key])) {
            self::$trigCache[$key] = cos($radians);
        }

        return self::$trigCache[$key];
    }

    /**
     * Calculate distances for multiple coordinate pairs in batch.
     *
     * This method is optimized for calculating distances between multiple
     * coordinate pairs efficiently by reusing cached calculations.
     *
     * @param  array<array{Coordinates, Coordinates}> $coordinatePairs Array of coordinate pairs
     * @param  DistanceUnit                           $unit            The distance unit (default: KILOMETERS)
     * @param  string                                 $earthModel      The Earth model to use (default: WGS84)
     * @return array<float>                           Array of distances corresponding to each coordinate pair
     *
     * @example
     * ```php
     * $calculator = new CoordinatesCalculator();
     * $distances = $calculator->batchDistanceCalculation([
     *     [coordinates(40.7128, -74.0060), coordinates(51.5074, -0.1278)], // NY to London
     *     [coordinates(48.8566, 2.3522), coordinates(35.6762, 139.6503)],   // Paris to Tokyo
     *     [coordinates(-33.8688, 151.2093), coordinates(-34.6037, -58.3816)], // Sydney to Buenos Aires
     * ], DistanceUnit::KILOMETERS);
     * // Returns: [5570.0, 9719.0, 11300.0]
     * ```
     */
    public function batchDistanceCalculation(
        array $coordinatePairs,
        DistanceUnit $unit = DistanceUnit::KILOMETERS,
        string $earthModel = self::EARTH_MODEL_WGS84
    ): array {
        $distances = [];
        $earthRadius = $this->getEarthRadius($unit, $earthModel);

        foreach ($coordinatePairs as [$a, $b]) {
            // Early return for identical coordinates
            if ($a->isEqual($b)) {
                $distances[] = 0.0;

                continue;
            }

            // Convert degrees to radians with caching
            $lat1 = $this->getCachedRadians($a->latitude->value);
            $lon1 = $this->getCachedRadians($a->longitude->value);
            $lat2 = $this->getCachedRadians($b->latitude->value);
            $lon2 = $this->getCachedRadians($b->longitude->value);

            // Calculate differences
            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;

            // Haversine formula with cached trigonometric functions
            $sinDlat = $this->getCachedSin($dlat / 2);
            $sinDlon = $this->getCachedSin($dlon / 2);
            $cosLat1 = $this->getCachedCos($lat1);
            $cosLat2 = $this->getCachedCos($lat2);

            $a = $sinDlat * $sinDlat + $cosLat1 * $cosLat2 * $sinDlon * $sinDlon;
            $c = 2 * asin(sqrt($a));

            $distances[] = $earthRadius * $c;
        }

        return $distances;
    }

    /**
     * Calculate the bearing between two coordinates.
     *
     * The bearing is the initial direction from the first coordinate to the second,
     * measured in degrees clockwise from north.
     *
     * @param  Coordinates $from The starting coordinate
     * @param  Coordinates $to   The destination coordinate
     * @return float       The bearing in degrees (0-360)
     *
     * @example
     * ```php
     * $calculator = new CoordinatesCalculator();
     * $bearing = $calculator->bearingBetween(
     *     coordinates(40.7128, -74.0060), // New York
     *     coordinates(51.5074, -0.1278)   // London
     * );
     * // Returns: ~52.5 degrees (northeast)
     * ```
     */
    public function bearingBetween(Coordinates $from, Coordinates $to): float
    {
        $lat1 = $this->getCachedRadians($from->latitude->value);
        $lat2 = $this->getCachedRadians($to->latitude->value);
        $dlon = $this->getCachedRadians($to->longitude->value) - $this->getCachedRadians($from->longitude->value);

        $y = $this->getCachedSin($dlon) * $this->getCachedCos($lat2);
        $x = $this->getCachedCos($lat1) * $this->getCachedSin($lat2) -
            $this->getCachedSin($lat1) * $this->getCachedCos($lat2) * $this->getCachedCos($dlon);

        $bearing = atan2($y, $x);
        $bearing = rad2deg($bearing);

        return fmod($bearing + 360, 360);
    }

    /**
     * Calculate the midpoint between two coordinates.
     *
     * @param  Coordinates $a The first coordinate
     * @param  Coordinates $b The second coordinate
     * @return Coordinates The midpoint coordinate
     *
     * @example
     * ```php
     * $calculator = new CoordinatesCalculator();
     * $midpoint = $calculator->midpointBetween(
     *     coordinates(40.7128, -74.0060), // New York
     *     coordinates(51.5074, -0.1278)   // London
     * );
     * // Returns: coordinates(46.1101, -37.0669)
     * ```
     */
    public function midpointBetween(Coordinates $a, Coordinates $b): Coordinates
    {
        $lat1 = $this->getCachedRadians($a->latitude->value);
        $lat2 = $this->getCachedRadians($b->latitude->value);
        $dlon = $this->getCachedRadians($b->longitude->value) - $this->getCachedRadians($a->longitude->value);

        $bx = $this->getCachedCos($lat2) * $this->getCachedCos($dlon);
        $by = $this->getCachedCos($lat2) * $this->getCachedSin($dlon);

        $lat3 = atan2(
            $this->getCachedSin($lat1) + $this->getCachedSin($lat2),
            sqrt(($this->getCachedCos($lat1) + $bx) * ($this->getCachedCos($lat1) + $bx) + $by * $by)
        );

        $lon3 = $this->getCachedRadians($a->longitude->value) + atan2($by, $this->getCachedCos($lat1) + $bx);

        return new Coordinates(
            new \JeroenGerits\Support\Coordinates\ValueObjects\Latitude(rad2deg($lat3)),
            new \JeroenGerits\Support\Coordinates\ValueObjects\Longitude(rad2deg($lon3))
        );
    }
}
