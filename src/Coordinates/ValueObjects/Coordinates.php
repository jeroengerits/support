<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\Equatable;
use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use Stringable;

/**
 * Value object representing geographic coordinates.
 */
class Coordinates implements Equatable, Stringable
{
    /**
     * @param Latitude  $latitude  The latitude value object
     * @param Longitude $longitude The longitude value object
     */
    public function __construct(public Latitude $latitude, public Longitude $longitude) {}

    /**
     * @return string The coordinates as "latitude,longitude"
     */
    public function __toString(): string
    {
        return "{$this->latitude},{$this->longitude}";
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
     * @param  Coordinates  $target The target coordinates
     * @param  DistanceUnit $unit   The unit of distance to return
     * @return float        The distance between the two coordinates
     */
    public function distanceBetween(Coordinates $target, DistanceUnit $unit = DistanceUnit::KILOMETERS): float
    {
        return (new CoordinatesCalculator)
            ->distanceBetween($this, $target, $unit);
    }
}
