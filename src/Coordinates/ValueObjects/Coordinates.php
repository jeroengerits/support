<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Cache\CacheFactory;
use JeroenGerits\Support\Cache\Contracts\CacheAdapter;
use JeroenGerits\Support\Cache\ValueObjects\TimeToLive;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Shared\Contracts\Equatable;
use Stringable;

/**
 * Value object representing geographic coordinates with built-in distance calculations.
 *
 * This class provides immutable coordinate objects with high-performance distance
 * calculations using the Haversine formula. It supports multiple Earth models and
 * distance units with built-in caching for optimal performance.
 *
 * @example
 * ```php
 * $amsterdam = Coordinates::create(52.3676, 4.9041);
 * $london = Coordinates::create(51.5074, -0.1278);
 * $distance = $amsterdam->distanceTo($london); // 357.2 km
 * ```
 */
class Coordinates implements Equatable, Stringable
{
    // Cache key prefixes for different trigonometric calculations
    private const string RADIANS_CACHE_PREFIX = 'rad_';

    private const string SINE_CACHE_PREFIX = 'sin_';

    private const string COSINE_CACHE_PREFIX = 'cos_';

    // Haversine formula constants
    private const float HAVERSINE_DIVISION_FACTOR = 2.0;

    private const float HAVERSINE_MULTIPLICATION_FACTOR = 2.0;

    // Distance calculation constants
    private const float ZERO_DISTANCE = 0.0;

    /** @var CacheAdapter|null The cache adapter */
    private static ?CacheAdapter $cache = null;

    /**
     * Create a new Coordinates instance.
     *
     * @param Latitude  $latitude  The latitude value object (-90° to +90°)
     * @param Longitude $longitude The longitude value object (-180° to +180°)
     */
    public function __construct(
        public readonly Latitude $latitude,
        public readonly Longitude $longitude
    ) {}

    /**
     * Create a new Coordinates instance from latitude and longitude values.
     *
     * This is the recommended way to create coordinates as it provides
     * automatic validation and clear error messages.
     *
     * @param  float $latitude  The latitude value in decimal degrees (-90° to +90°)
     * @param  float $longitude The longitude value in decimal degrees (-180° to +180°)
     * @return self  The new Coordinates instance
     *
     * @throws InvalidCoordinatesException When latitude or longitude values are invalid
     *
     * @example
     * ```php
     * $coordinates = Coordinates::create(40.7128, -74.0060); // New York
     * ```
     */
    public static function create(float $latitude, float $longitude): self
    {
        return new self(
            latitude: new Latitude($latitude),
            longitude: new Longitude($longitude)
        );
    }

    /**
     * Set a custom cache adapter (useful for testing).
     *
     * @param CacheAdapter|null $cache The cache adapter to use
     */
    public static function setCache(?CacheAdapter $cache): void
    {
        self::$cache = $cache;
    }

    /**
     * Clear all caches and reset to default.
     *
     * This method clears the cache and resets it to the default configuration.
     * Use this when memory usage becomes a concern or when you want to
     * reset the cache for testing purposes.
     */
    public static function clearCache(): void
    {
        self::getCache()->clear();
    }

    /**
     * Get the current cache adapter.
     *
     * @return CacheAdapter The cache adapter
     */
    private static function getCache(): CacheAdapter
    {
        if (! self::$cache instanceof \JeroenGerits\Support\Cache\Contracts\CacheAdapter) {
            self::$cache = CacheFactory::createArrayCache(
                namespace: 'coordinates',
                maxItems: 5000
            );
        }

        return self::$cache;
    }

    /**
     * Get cache statistics.
     *
     * @return \JeroenGerits\Support\Cache\ValueObjects\CacheStats Current cache statistics
     */
    public static function getCacheStats(): \JeroenGerits\Support\Cache\ValueObjects\CacheStats
    {
        return self::getCache()->getStats();
    }

    /**
     * Get the current size of the cache.
     *
     * @return int The number of cached items
     *
     * @deprecated Use getCacheStats()->getItems() instead
     */
    public static function getCacheSize(): int
    {
        return self::getCache()->getStats()->getItems();
    }

    /**
     * Get the current size of the Earth radius cache.
     *
     * @return int The number of cached Earth radius values
     *
     * @deprecated Use getCacheStats()->getItems() instead
     */
    public static function getEarthRadiusCacheSize(): int
    {
        return self::getCache()->getStats()->getItems();
    }

    /**
     * Calculate distances for multiple coordinate pairs in batch.
     *
     * This method efficiently processes multiple coordinate pairs at once,
     * reusing cached calculations and Earth radius values for optimal performance.
     *
     * @param  array<array{Coordinates, Coordinates}> $coordinatePairs Array of coordinate pairs
     * @param  DistanceUnit                           $unit            The distance unit (default: KILOMETERS)
     * @param  EarthModel                             $earthModel      The Earth model to use (default: WGS84)
     * @return array<float>                           Array of distances corresponding to each coordinate pair
     *
     * @example
     * ```php
     * $pairs = [
     *     [Coordinates::create(40.7128, -74.0060), Coordinates::create(51.5074, -0.1278)],
     *     [Coordinates::create(52.3676, 4.9041), Coordinates::create(48.8566, 2.3522)]
     * ];
     * $distances = Coordinates::batchDistanceCalculation($pairs);
     * ```
     */
    public static function batchDistanceCalculation(
        array $coordinatePairs,
        DistanceUnit $unit = DistanceUnit::KILOMETERS,
        EarthModel $earthModel = EarthModel::WGS84
    ): array {
        $distances = [];
        $earthRadius = self::getCachedEarthRadius($unit, $earthModel);

        foreach ($coordinatePairs as [$source, $target]) {
            $distances[] = self::calculateDistanceBetween($source, $target, $earthRadius);
        }

        return $distances;
    }

    /**
     * Get the string representation of the coordinates.
     *
     * @return string The coordinates in "latitude,longitude" format
     */
    public function __toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
    }

    /**
     * Calculate the distance to another set of coordinates.
     *
     * Uses the Haversine formula to calculate the great-circle distance
     * between two points on Earth. The calculation is optimized with
     * caching for repeated calculations.
     *
     * @param  Coordinates  $target     The target coordinates
     * @param  DistanceUnit $unit       The unit of distance to return (default: KILOMETERS)
     * @param  EarthModel   $earthModel The Earth model to use (default: WGS84)
     * @return float        The distance between the two coordinates
     *
     * @example
     * ```php
     * $amsterdam = Coordinates::create(52.3676, 4.9041);
     * $london = Coordinates::create(51.5074, -0.1278);
     * $distance = $amsterdam->distanceTo($london); // 357.2 km
     * ```
     */
    public function distanceTo(
        Coordinates $target,
        DistanceUnit $unit = DistanceUnit::KILOMETERS,
        EarthModel $earthModel = EarthModel::WGS84
    ): float {
        if ($this->isEqual($target)) {
            return self::ZERO_DISTANCE;
        }

        $earthRadius = self::getCachedEarthRadius($unit, $earthModel);

        return self::calculateDistanceBetween($this, $target, $earthRadius);
    }

    /**
     * Check if this coordinates object is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the coordinates are equal, false otherwise
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->latitude->isEqual($other->latitude)
            && $this->longitude->isEqual($other->longitude);
    }

    /**
     * Calculate the distance between two coordinates using the provided Earth radius.
     *
     * @param  Coordinates $source      The source coordinates
     * @param  Coordinates $target      The target coordinates
     * @param  float       $earthRadius The Earth radius in the desired unit
     * @return float       The distance between the coordinates
     */
    private static function calculateDistanceBetween(
        Coordinates $source,
        Coordinates $target,
        float $earthRadius
    ): float {
        if ($source->isEqual($target)) {
            return self::ZERO_DISTANCE;
        }

        $haversineValue = self::calculateHaversineValue($source, $target);

        return $earthRadius * $haversineValue;
    }

    /**
     * Get the Earth radius for the specified unit and model with caching.
     *
     * @param  DistanceUnit $unit       The distance unit
     * @param  EarthModel   $earthModel The Earth model to use
     * @return float        The Earth radius in the specified unit
     */
    private static function getCachedEarthRadius(DistanceUnit $unit, EarthModel $earthModel): float
    {
        $cache = self::getCache();
        $key = "{$earthModel->value}_{$unit->value}";

        $cached = $cache->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $radius = $earthModel->getRadius($unit);
        $cache->set($key, $radius, TimeToLive::fromDays(30)->seconds); // Earth radius never changes

        return $radius;
    }

    /**
     * Calculate the Haversine value for two coordinates.
     *
     * The Haversine formula calculates the great-circle distance between
     * two points on a sphere given their latitudes and longitudes.
     *
     * @param  Coordinates $source The source coordinates
     * @param  Coordinates $target The target coordinates
     * @return float       The Haversine calculation result
     */
    private static function calculateHaversineValue(Coordinates $source, Coordinates $target): float
    {
        $radians = self::convertCoordinatesToRadians($source, $target);
        $differences = self::calculateCoordinateDifferences($radians);
        $trigonometricValues = self::calculateTrigonometricValues($radians, $differences);

        return self::applyHaversineFormula($trigonometricValues);
    }

    /**
     * Convert coordinate values to radians with caching.
     *
     * @param  Coordinates                                               $source The source coordinates
     * @param  Coordinates                                               $target The target coordinates
     * @return array{lat1: float, lon1: float, lat2: float, lon2: float} Radian values
     */
    private static function convertCoordinatesToRadians(Coordinates $source, Coordinates $target): array
    {
        return [
            'lat1' => self::getCachedRadians($source->latitude->value),
            'lon1' => self::getCachedRadians($source->longitude->value),
            'lat2' => self::getCachedRadians($target->latitude->value),
            'lon2' => self::getCachedRadians($target->longitude->value),
        ];
    }

    /**
     * Calculate the differences between coordinate values.
     *
     * @param  array{lat1: float, lon1: float, lat2: float, lon2: float} $radians Radian values
     * @return array{dlat: float, dlon: float}                           Coordinate differences
     */
    private static function calculateCoordinateDifferences(array $radians): array
    {
        return [
            'dlat' => $radians['lat2'] - $radians['lat1'],
            'dlon' => $radians['lon2'] - $radians['lon1'],
        ];
    }

    /**
     * Calculate trigonometric values needed for the Haversine formula.
     *
     * @param  array{lat1: float, lon1: float, lat2: float, lon2: float}             $radians     Radian values
     * @param  array{dlat: float, dlon: float}                                       $differences Coordinate differences
     * @return array{sinDlat: float, sinDlon: float, cosLat1: float, cosLat2: float} Trigonometric values
     */
    private static function calculateTrigonometricValues(array $radians, array $differences): array
    {
        return [
            'sinDlat' => self::getCachedSin($differences['dlat'] / self::HAVERSINE_DIVISION_FACTOR),
            'sinDlon' => self::getCachedSin($differences['dlon'] / self::HAVERSINE_DIVISION_FACTOR),
            'cosLat1' => self::getCachedCos($radians['lat1']),
            'cosLat2' => self::getCachedCos($radians['lat2']),
        ];
    }

    /**
     * Apply the Haversine formula to calculate the angular distance.
     *
     * @param  array{sinDlat: float, sinDlon: float, cosLat1: float, cosLat2: float} $trigValues Trigonometric values
     * @return float                                                                 The angular distance in radians
     */
    private static function applyHaversineFormula(array $trigValues): float
    {
        $haversineA = $trigValues['sinDlat'] * $trigValues['sinDlat']
            + $trigValues['cosLat1'] * $trigValues['cosLat2']
            * $trigValues['sinDlon'] * $trigValues['sinDlon'];

        return self::HAVERSINE_MULTIPLICATION_FACTOR * asin(sqrt($haversineA));
    }

    /**
     * Get the radians value for degrees with caching.
     *
     * @param  float $degrees The degrees value to convert to radians
     * @return float The radians value
     */
    private static function getCachedRadians(float $degrees): float
    {
        $cache = self::getCache();
        $key = self::RADIANS_CACHE_PREFIX.$degrees;

        $cached = $cache->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $radians = deg2rad($degrees);
        $cache->set($key, $radians, TimeToLive::fromHours(24)->seconds);

        return $radians;
    }

    /**
     * Get the sine value for radians with caching.
     *
     * @param  float $radians The radians value to calculate sine for
     * @return float The sine value
     */
    private static function getCachedSin(float $radians): float
    {
        $cache = self::getCache();
        $key = self::SINE_CACHE_PREFIX.$radians;

        $cached = $cache->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $sine = sin($radians);
        $cache->set($key, $sine, TimeToLive::fromHours(24)->seconds);

        return $sine;
    }

    /**
     * Get the cosine value for radians with caching.
     *
     * @param  float $radians The radians value to calculate cosine for
     * @return float The cosine value
     */
    private static function getCachedCos(float $radians): float
    {
        $cache = self::getCache();
        $key = self::COSINE_CACHE_PREFIX.$radians;

        $cached = $cache->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $cosine = cos($radians);
        $cache->set($key, $cosine, TimeToLive::fromHours(24)->seconds);

        return $cosine;
    }
}
