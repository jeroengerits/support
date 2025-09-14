<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

/**
 * Calculator for coordinate-based distance calculations using Haversine formula.
 */
class CoordinatesCalculator
{
    /** Earth model constants. */
    public const EARTH_MODEL_SPHERICAL = 'spherical';

    public const EARTH_MODEL_WGS84 = 'wgs84';

    public const EARTH_MODEL_GRS80 = 'grs80';

    /** @var array<string, array<string, float>> */
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

    /** @var array<string, float> */
    private static array $trigCache = [];

    /** @var array<string, float> */
    private static array $radiusCache = [];

    /** Clear the trigonometric cache. */
    public static function clearCache(): void
    {
        self::$trigCache = [];
        self::$radiusCache = [];
    }

    /**
     * Get the size of the trigonometric cache.
     *
     * @return int The number of cached trigonometric values
     *
     * @example
     * ```php
     * // Check cache size
     * $size = CoordinatesCalculator::getCacheSize();
     * echo "Cache contains {$size} entries";
     *
     * // Monitor cache growth
     * $calculator = new CoordinatesCalculator();
     * $calculator->distanceBetween(
     *     coordinates(40.7128, -74.0060),
     *     coordinates(51.5074, -0.1278)
     * );
     * $newSize = CoordinatesCalculator::getCacheSize();
     * echo "Cache grew by " . ($newSize - $size) . " entries";
     * ```
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
        $haversineValue = $this->calculateHaversineValue($a, $b);

        return $earthRadius * $haversineValue;
    }

    /**
     * @param  DistanceUnit $unit       The distance unit
     * @param  string       $earthModel The Earth model to use
     * @return float        The Earth radius in the specified unit
     *
     * @throws \InvalidArgumentException When the Earth model is not supported
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

    /** Cache key prefixes for different trigonometric functions. */
    private const string RADIANS_CACHE_PREFIX = 'rad_';

    private const string SINE_CACHE_PREFIX = 'sin_';

    private const string COSINE_CACHE_PREFIX = 'cos_';

    /**
     * @param  float $degrees The degrees value to convert to radians
     * @return float The radians value
     */
    private function getCachedRadians(float $degrees): float
    {
        $key = self::RADIANS_CACHE_PREFIX.$degrees;
        if (! isset(self::$trigCache[$key])) {
            self::$trigCache[$key] = deg2rad($degrees);
        }

        return self::$trigCache[$key];
    }

    /**
     * @param  float $radians The radians value to calculate sine for
     * @return float The sine value
     */
    private function getCachedSin(float $radians): float
    {
        $key = self::SINE_CACHE_PREFIX.$radians;
        if (! isset(self::$trigCache[$key])) {
            self::$trigCache[$key] = sin($radians);
        }

        return self::$trigCache[$key];
    }

    /**
     * @param  float $radians The radians value to calculate cosine for
     * @return float The cosine value
     */
    private function getCachedCos(float $radians): float
    {
        $key = self::COSINE_CACHE_PREFIX.$radians;
        if (! isset(self::$trigCache[$key])) {
            self::$trigCache[$key] = cos($radians);
        }

        return self::$trigCache[$key];
    }

    /**
     * Calculate distances for multiple coordinate pairs in batch.
     *
     * @param  array<array{Coordinates, Coordinates}> $coordinatePairs Array of coordinate pairs
     * @param  DistanceUnit                           $unit            The distance unit (default: KILOMETERS)
     * @param  string                                 $earthModel      The Earth model to use (default: WGS84)
     * @return array<float>                           Array of distances corresponding to each coordinate pair
     */
    public function batchDistanceCalculation(
        array $coordinatePairs,
        DistanceUnit $unit = DistanceUnit::KILOMETERS,
        string $earthModel = self::EARTH_MODEL_WGS84
    ): array {
        $distances = [];
        $earthRadius = $this->getEarthRadius($unit, $earthModel);

        foreach ($coordinatePairs as [$a, $b]) {
            if ($a->isEqual($b)) {
                $distances[] = 0.0;

                continue;
            }

            $haversineValue = $this->calculateHaversineValue($a, $b);
            $distances[] = $earthRadius * $haversineValue;
        }

        return $distances;
    }

    /**
     * Calculate the Haversine value for two coordinates.
     *
     * This method extracts the core Haversine formula calculation to eliminate
     * code duplication between single and batch distance calculations.
     *
     * @param  Coordinates $a The first coordinate
     * @param  Coordinates $b The second coordinate
     * @return float       The Haversine calculation result
     */
    private function calculateHaversineValue(Coordinates $a, Coordinates $b): float
    {
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

        $haversineA = $sinDlat * $sinDlat + $cosLat1 * $cosLat2 * $sinDlon * $sinDlon;

        return 2 * asin(sqrt($haversineA));
    }
}
