<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\ValueObject;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLatitudeException;

final readonly class Latitude implements ValueObject
{
    private const float MIN_LATITUDE = -90.0;

    private const float MAX_LATITUDE = 90.0;

    /**
     * Create a new Latitude instance.
     *
     * @param float $value The latitude value in decimal degrees
     *
     * @throws InvalidLatitudeException When the value is outside the valid range
     *
     * @example
     * $latitude = new Latitude(40.7128); // Valid
     * $latitude = new Latitude(91.0);    // Throws InvalidLatitudeException
     */
    public function __construct(
        public float $value
    ) {
        if ($value < self::MIN_LATITUDE || $value > self::MAX_LATITUDE) {
            throw InvalidLatitudeException::outOfRange($value);
        }
    }

    /**
     * Create a latitude from a string value.
     *
     * @param  string $value The latitude as a string
     * @return self   New Latitude instance
     *
     * @throws InvalidLatitudeException When the string is not numeric or out of range
     *
     * @example
     * $latitude = Latitude::fromString('40.7128'); // Valid
     * $latitude = Latitude::fromString('invalid'); // Throws InvalidLatitudeException
     */
    public static function fromString(string $value): self
    {
        if (! is_numeric($value)) {
            throw InvalidLatitudeException::invalidString($value);
        }

        return new self((float) $value);
    }

    /**
     * Create a latitude from a float value.
     *
     * @param  float $value The latitude as a float
     * @return self  New Latitude instance
     *
     * @throws InvalidLatitudeException When the value is out of range
     *
     * @example
     * $latitude = Latitude::fromFloat(40.7128); // Valid
     * $latitude = Latitude::fromFloat(91.0);    // Throws InvalidLatitudeException
     */
    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    /**
     * Get the latitude value.
     *
     * @return float The latitude value in decimal degrees
     *
     * @example
     * $latitude = new Latitude(40.7128);
     * echo $latitude->value(); // 40.7128
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * Check if this latitude equals another latitude.
     *
     * @param  Latitude $latitude The latitude to compare with
     * @return bool     True if the latitudes are equal
     *
     * @example
     * $lat1 = new Latitude(40.7128);
     * $lat2 = new Latitude(40.7128);
     * $lat1->equals($lat2); // true
     */
    public function equals(ValueObject $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->value === $other->value;
    }

    /**
     * Convert the latitude to a string representation.
     *
     * @return string The latitude as a string
     *
     * @example
     * $latitude = new Latitude(40.7128);
     * echo (string) $latitude; // "40.7128"
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Convert the latitude to an array representation.
     *
     * @return array{latitude: float} The latitude as an associative array
     *
     * @example
     * $latitude = new Latitude(40.7128);
     * $latitude->toArray(); // ['latitude' => 40.7128]
     */
    public function toArray(): array
    {
        return ['latitude' => $this->value];
    }

    /**
     * Check if this latitude is in the northern hemisphere.
     *
     * @return bool True if the latitude is greater than 0
     *
     * @example
     * $latitude = new Latitude(40.7128);
     * $latitude->isNorthern(); // true
     */
    public function isNorthern(): bool
    {
        return $this->value > 0.0;
    }

    /**
     * Check if this latitude is in the southern hemisphere.
     *
     * @return bool True if the latitude is less than 0
     *
     * @example
     * $latitude = new Latitude(-40.7128);
     * $latitude->isSouthern(); // true
     */
    public function isSouthern(): bool
    {
        return $this->value < 0.0;
    }

    /**
     * Check if this latitude is at the equator.
     *
     * @return bool True if the latitude is exactly 0
     *
     * @example
     * $latitude = new Latitude(0.0);
     * $latitude->isEquator(); // true
     */
    public function isEquator(): bool
    {
        return $this->value === 0.0;
    }
}
