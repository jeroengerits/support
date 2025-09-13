<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\ValueObjects;

use JeroenGerits\Support\Contract\Equatable;
use JeroenGerits\Support\Domain\Coordinates\Enums\DistanceUnit;

/**
 * Geographic coordinates value object.
 *
 * Represents a point on Earth using latitude and longitude values.
 * Provides methods for distance calculation, hemisphere determination,
 * and various coordinate-based operations.
 */
class Coordinates implements Equatable
{
    /**
     * The latitude component.
     */
    private Latitude $latitude;

    /**
     * The longitude component.
     */
    private Longitude $longitude;

    /**
     * Create a new coordinates instance.
     *
     * @param Latitude  $latitude  The latitude value
     * @param Longitude $longitude The longitude value
     */
    public function __construct(Latitude $latitude, Longitude $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Get the latitude component.
     *
     * @return Latitude The latitude value
     */
    public function latitude(): Latitude
    {
        return $this->latitude;
    }

    /**
     * Get the longitude component.
     *
     * @return Longitude The longitude value
     */
    public function longitude(): Longitude
    {
        return $this->longitude;
    }

    public function distanceTo(Coordinates $other, DistanceUnit $unit = DistanceUnit::KILOMETERS): float
    {
        $lat1 = deg2rad($this->latitude->value());
        $lon1 = deg2rad($this->longitude->value());
        $lat2 = deg2rad($other->latitude->value());
        $lon2 = deg2rad($other->longitude->value());

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * asin(sqrt($a));

        $earthRadius = 6371; // Earth's radius in kilometers
        $distance = $earthRadius * $c;

        return $distance * $unit->conversionFactor();
    }

    public function value(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude->value(),
            'longitude' => $this->longitude->value(),
        ];
    }

    public function isNorthern(): bool
    {
        return $this->latitude->value() > 0;
    }

    public function isSouthern(): bool
    {
        return $this->latitude->value() < 0;
    }

    public function isEastern(): bool
    {
        return $this->longitude->value() > 0;
    }

    public function isWestern(): bool
    {
        return $this->longitude->value() < 0;
    }

    public function isEquator(): bool
    {
        return $this->latitude->value() === 0.0;
    }

    public function isPrimeMeridian(): bool
    {
        return $this->longitude->value() === 0.0;
    }

    public function isInternationalDateLine(): bool
    {
        return abs($this->longitude->value()) === 180.0;
    }

    public function isGreenwichMeridian(): bool
    {
        return $this->longitude->value() === 0.0;
    }

    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->latitude->isEqual($other->latitude) &&
            $this->longitude->isEqual($other->longitude);
    }

    public function __toString(): string
    {
        return $this->latitude->value().','.$this->longitude->value();
    }
}
