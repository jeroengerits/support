<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use Stringable;

class Coordinates implements Equatable, Stringable
{
    /**
     * Create a new Coordinates instance.
     *
     * @param Latitude  $latitude  The latitude value
     * @param Longitude $longitude The longitude value
     */
    public function __construct(public Latitude $latitude, public Longitude $longitude) {}

    /**
     * Get the string representation of the coordinates.
     *
     * @return string The coordinates as "latitude,longitude"
     */
    public function __toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
    }

    /**
     * Check if this coordinates object is equal to another.
     *
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
     * Calculate the distance between two coordinates.
     *
     * @param  mixed        $latitude  The latitude value or Coordinates object
     * @param  mixed        $longitude The longitude value (optional when $latitude is a Coordinates object)
     * @param  DistanceUnit $unit      The unit of distance to return
     * @return float        The distance between the two coordinates
     */
    public function distanceBetween(mixed $latitude, mixed $longitude = null, DistanceUnit $unit = DistanceUnit::KILOMETERS): float
    {
        // If first parameter is a Coordinates object, use it directly
        if ($latitude instanceof Coordinates) {
            return (new CoordinatesCalculator)
                ->distanceBetween($this, $latitude, $unit);
        }

        return (new CoordinatesCalculator)
            ->distanceBetween($this, CoordinatesFactory::createCoordinates($latitude, $longitude), $unit);
    }

    /**
     * Calculate the distance between two coordinates in miles.
     *
     * @param  mixed $latitude  The latitude value or Coordinates object
     * @param  mixed $longitude The longitude value (optional when $latitude is a Coordinates object)
     * @return float The distance between the two coordinates in miles
     */
    public function distanceBetweenInMiles(mixed $latitude, mixed $longitude = null): float
    {
        // If first parameter is a Coordinates object, use it directly
        if ($latitude instanceof Coordinates) {
            return (new CoordinatesCalculator)
                ->distanceBetween($this, $latitude, DistanceUnit::MILES);
        }

        return (new CoordinatesCalculator)
            ->distanceBetween($this, CoordinatesFactory::createCoordinates($latitude, $longitude), DistanceUnit::MILES);
    }

    /**
     * Calculate the distance between two coordinates in kilometers.
     *
     * @param  mixed $latitude  The latitude value or Coordinates object
     * @param  mixed $longitude The longitude value (optional when $latitude is a Coordinates object)
     * @return float The distance between the two coordinates in kilometers
     */
    public function distanceBetweenInKilometers(mixed $latitude, mixed $longitude = null): float
    {
        // If first parameter is a Coordinates object, use it directly
        if ($latitude instanceof Coordinates) {
            return (new CoordinatesCalculator)
                ->distanceBetween($this, $latitude, DistanceUnit::KILOMETERS);
        }

        return (new CoordinatesCalculator)
            ->distanceBetween($this, CoordinatesFactory::createCoordinates($latitude, $longitude), DistanceUnit::KILOMETERS);
    }
}
