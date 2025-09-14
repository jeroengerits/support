<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Shared\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use Stringable;

/**
 * Value object representing geographic coordinates with built-in distance calculations.
 *
 * This class provides immutable coordinate objects with high-performance distance
 * calculations using the Haversine formula. It supports multiple Earth models and
 * distance units.
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
    // Haversine formula constants
    private const float HAVERSINE_DIVISION_FACTOR = 2.0;

    private const float HAVERSINE_MULTIPLICATION_FACTOR = 2.0;

    // Distance calculation constants
    private const float ZERO_DISTANCE = 0.0;

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
     * Calculate distances for multiple coordinate pairs in batch.
     *
     * This method efficiently processes multiple coordinate pairs at once,
     * reusing Earth radius values for optimal performance.
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
        $earthRadius = self::getEarthRadius($unit, $earthModel);

        foreach ($coordinatePairs as [$source, $target]) {
            $distances[] = self::calculateDistanceBetween($source, $target, $earthRadius);
        }

        return $distances;
    }

    /**
     * Get the Earth radius for the specified unit and model.
     *
     * @param  DistanceUnit $unit       The distance unit
     * @param  EarthModel   $earthModel The Earth model to use
     * @return float        The Earth radius in the specified unit
     */
    private static function getEarthRadius(DistanceUnit $unit, EarthModel $earthModel): float
    {
        return $earthModel->getRadius($unit);
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
            'lat1' => self::getRadians($source->latitude->value),
            'lon1' => self::getRadians($source->longitude->value),
            'lat2' => self::getRadians($target->latitude->value),
            'lon2' => self::getRadians($target->longitude->value),
        ];
    }

    /**
     * Get the radians value for degrees.
     *
     * @param  float $degrees The degrees value to convert to radians
     * @return float The radians value
     */
    private static function getRadians(float $degrees): float
    {
        return deg2rad($degrees);
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
            'sinDlat' => self::getSin($differences['dlat'] / self::HAVERSINE_DIVISION_FACTOR),
            'sinDlon' => self::getSin($differences['dlon'] / self::HAVERSINE_DIVISION_FACTOR),
            'cosLat1' => self::getCos($radians['lat1']),
            'cosLat2' => self::getCos($radians['lat2']),
        ];
    }

    /**
     * Get the sine value for radians.
     *
     * @param  float $radians The radians value to calculate sine for
     * @return float The sine value
     */
    private static function getSin(float $radians): float
    {
        return sin($radians);
    }

    /**
     * Get the cosine value for radians.
     *
     * @param  float $radians The radians value to calculate cosine for
     * @return float The cosine value
     */
    private static function getCos(float $radians): float
    {
        return cos($radians);
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
     * between two points on Earth.
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

        $earthRadius = self::getEarthRadius($unit, $earthModel);

        return self::calculateDistanceBetween($this, $target, $earthRadius);
    }
}
