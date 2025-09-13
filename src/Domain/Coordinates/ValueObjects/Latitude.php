<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\ValueObjects;

use JeroenGerits\Support\Contract\Equatable;
use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException;

/**
 * Latitude value object.
 *
 * Represents a latitude value in degrees, constrained to the range
 * of -90 to 90 degrees. Provides methods for hemisphere determination
 * and coordinate validation.
 */
class Latitude implements Equatable
{
    /**
     * The latitude value in degrees.
     */
    private float $value;

    /**
     * Create a new latitude instance.
     *
     * @param float $value The latitude value in degrees
     *
     * @throws InvalidLatitudeException If value is outside valid range
     */
    public function __construct(float $value)
    {
        if ($value < -90.0 || $value > 90.0) {
            throw new InvalidLatitudeException("Latitude must be between -90 and 90 degrees, got: {$value}");
        }

        $this->value = $value;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function isNorthern(): bool
    {
        return $this->value > 0;
    }

    public function isSouthern(): bool
    {
        return $this->value < 0;
    }

    public function isEquator(): bool
    {
        return $this->value === 0.0;
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
