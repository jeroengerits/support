<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use Stringable;

/**
 * Value object representing geographic coordinates with built-in distance calculations.
 */
class Coordinates implements Equatable, Stringable
{
    /** Cache key prefixes for different trigonometric functions. */
    private const string RADIANS_CACHE_PREFIX = 'rad_';

    private const string SINE_CACHE_PREFIX = 'sin_';

    private const string COSINE_CACHE_PREFIX = 'cos_';

    /** @var array<string, float> */
    private static array $trigCache = [];

    /** @var array<string, float> */
    private static array $radiusCache = [];

    /**
     * @param Latitude  $latitude  The latitude value object
     * @param Longitude $longitude The longitude value object
     */
    public function __construct(public Latitude $latitude, public Longitude $longitude) {}

    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * @param float $latitude  The latitude value in decimal degrees
     * @param float $longitude The longitude value in decimal degrees
     *
     * @throws InvalidCoordinatesException When latitude or longitude values are invalid
     */
    public static function create(float $latitude, float $longitude): self
    {
        return new self(
            latitude: new Latitude($latitude),
            longitude: new Longitude($longitude)
        );
    }

    /**
     * Clear the trigonometric cache.
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
     * Calculate distances for multiple coordinate pairs in batch.
     *
     * @param  array<array{Coordinates, Coordinates}> $coordinatePairs Array of coordinate pairs
     * @param  DistanceUnit                           $unit            The distance unit (default: KILOMETERS)
     * @param  EarthModel                             $earthModel      The Earth model to use (default: WGS84)
     * @return array<float>                           Array of distances corresponding to each coordinate pair
     */
    public static function batchDistanceCalculation(
        array $coordinatePairs,
        DistanceUnit $unit = DistanceUnit::KILOMETERS,
        EarthModel $earthModel = EarthModel::WGS84
    ): array {
        $distances = [];
        $earthRadius = self::getEarthRadius($unit, $earthModel);

        foreach ($coordinatePairs as [$a, $b]) {
            if ($a->isEqual($b)) {
                $distances[] = 0.0;

                continue;
            }

            $haversineValue = $a->calculateHaversineValue($b);
            $distances[] = $earthRadius * $haversineValue;
        }

        return $distances;
    }

    /**
     * @return string The coordinates as "latitude,longitude"
     */
    public function __toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
    }

    /**
     * Calculate the distance to another set of coordinates.
     *
     * @param  Coordinates  $target     The target coordinates
     * @param  DistanceUnit $unit       The unit of distance to return
     * @param  EarthModel   $earthModel The Earth model to use (default: WGS84)
     * @return float        The distance between the two coordinates
     */
    public function distanceTo(
        Coordinates $target,
        DistanceUnit $unit = DistanceUnit::KILOMETERS,
        EarthModel $earthModel = EarthModel::WGS84
    ): float {
        // Early return for identical coordinates
        if ($this->isEqual($target)) {
            return 0.0;
        }

        $earthRadius = $this->getEarthRadius($unit, $earthModel);
        $haversineValue = $this->calculateHaversineValue($target);

        return $earthRadius * $haversineValue;
    }

    /**
     * @param  Equatable $other The other object to compare
     * @return bool      True if the coordinates are equal
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->longitude->isEqual($other->longitude) && $this->latitude->isEqual($other->latitude);
    }

    /**
     * @param  DistanceUnit $unit       The distance unit
     * @param  EarthModel   $earthModel The Earth model to use
     * @return float        The Earth radius in the specified unit
     */
    private static function getEarthRadius(DistanceUnit $unit, EarthModel $earthModel): float
    {
        $unitKey = $unit->value;
        $cacheKey = "{$earthModel->value}_{$unitKey}";

        if (! isset(self::$radiusCache[$cacheKey])) {
            self::$radiusCache[$cacheKey] = $earthModel->getRadius($unit);
        }

        return self::$radiusCache[$cacheKey];
    }

    /**
     * Calculate the Haversine value for two coordinates.
     *
     * @param  Coordinates $target The target coordinate
     * @return float       The Haversine calculation result
     */
    private function calculateHaversineValue(Coordinates $target): float
    {
        // Convert degrees to radians with caching
        $lat1 = $this->getCachedRadians($this->latitude->value);
        $lon1 = $this->getCachedRadians($this->longitude->value);
        $lat2 = $this->getCachedRadians($target->latitude->value);
        $lon2 = $this->getCachedRadians($target->longitude->value);

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
}
