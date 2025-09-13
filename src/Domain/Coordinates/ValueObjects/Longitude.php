<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\ValueObjects;

use JeroenGerits\Support\Contract\Equatable;
use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException;

/**
 * Longitude value object.
 *
 * Represents a longitude value in degrees, constrained to the range
 * of -180 to 180 degrees. Provides methods for hemisphere determination
 * and meridian identification.
 */
class Longitude implements Equatable
{
    /**
     * The longitude value in degrees.
     */
    private float $value;

    /**
     * Create a new longitude instance.
     *
     * @param float $value The longitude value in degrees
     *
     * @throws InvalidLongitudeException If value is outside valid range
     */
    public function __construct(float $value)
    {
        if ($value < -180.0 || $value > 180.0) {
            throw new InvalidLongitudeException("Longitude must be between -180 and 180 degrees, got: {$value}");
        }

        $this->value = $value;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function isEastern(): bool
    {
        return $this->value > 0;
    }

    public function isWestern(): bool
    {
        return $this->value < 0;
    }

    public function isPrimeMeridian(): bool
    {
        return $this->value === 0.0;
    }

    public function isInternationalDateLine(): bool
    {
        return abs($this->value) === 180.0;
    }

    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return abs($this->value - $other->value) < PHP_FLOAT_EPSILON;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
