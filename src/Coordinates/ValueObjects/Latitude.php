<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use Exception;
use JeroenGerits\Support\Contract\Equatable;

class Latitude implements \Stringable, Equatable
{
    /**
     * @throws Exception
     */
    public function __construct(public float $value)
    {
        if ($value < -90.0 || $value > 90.0) {
            throw new Exception('Latitude must be between -90 and 90');
        }
    }

    /**
     * Get the string representation of the latitude.
     *
     * @return string The latitude as a string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Convert the latitude value to a string.
     *
     * @return string The latitude value as a string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Check if this latitude is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the latitudes are equal
     */
    public function isEqual(Equatable $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->value === $other->value;
    }
}
