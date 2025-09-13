<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\ValueObjects;

use JeroenGerits\Support\Contracts\ValueObject;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLongitudeException;

final readonly class Longitude implements ValueObject
{
    private const float MIN_LONGITUDE = -180.0;

    private const float MAX_LONGITUDE = 180.0;

    /**
     * Create a new Longitude instance.
     *
     * @param float $value The longitude value in decimal degrees
     *
     * @throws InvalidLongitudeException When the value is outside the valid range
     *
     * @example
     * $longitude = new Longitude(-74.0060); // Valid
     * $longitude = new Longitude(181.0);    // Throws InvalidLongitudeException
     */
    public function __construct(
        public float $value
    ) {
        if ($value < self::MIN_LONGITUDE || $value > self::MAX_LONGITUDE) {
            throw InvalidLongitudeException::outOfRange($value);
        }
    }

    /**
     * Create a longitude from a string value.
     *
     * @param  string $value The longitude as a string
     * @return self   New Longitude instance
     *
     * @throws InvalidLongitudeException When the string is not numeric or out of range
     *
     * @example
     * $longitude = Longitude::fromString('-74.0060'); // Valid
     * $longitude = Longitude::fromString('invalid'); // Throws InvalidLongitudeException
     */
    public static function fromString(string $value): self
    {
        if (! is_numeric($value)) {
            throw InvalidLongitudeException::invalidString($value);
        }

        return new self((float) $value);
    }

    /**
     * Create a longitude from a float value.
     *
     * @param  float $value The longitude as a float
     * @return self  New Longitude instance
     *
     * @throws InvalidLongitudeException When the value is out of range
     *
     * @example
     * $longitude = Longitude::fromFloat(-74.0060); // Valid
     * $longitude = Longitude::fromFloat(181.0);    // Throws InvalidLongitudeException
     */
    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    /**
     * Get the longitude value.
     *
     * @return float The longitude value in decimal degrees
     *
     * @example
     * $longitude = new Longitude(-74.0060);
     * echo $longitude->value(); // -74.0060
     */
    public function value(): float
    {
        return $this->value;
    }

    /**
     * Check if this longitude equals another longitude.
     *
     * @param  Longitude $longitude The longitude to compare with
     * @return bool      True if the longitudes are equal
     *
     * @example
     * $lon1 = new Longitude(-74.0060);
     * $lon2 = new Longitude(-74.0060);
     * $lon1->equals($lon2); // true
     */
    public function equals(ValueObject $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->value === $other->value;
    }

    /**
     * Convert the longitude to a string representation.
     *
     * @return string The longitude as a string
     *
     * @example
     * $longitude = new Longitude(-74.0060);
     * echo (string) $longitude; // "-74.0060"
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Convert the longitude to an array representation.
     *
     * @return array{longitude: float} The longitude as an associative array
     *
     * @example
     * $longitude = new Longitude(-74.0060);
     * $longitude->toArray(); // ['longitude' => -74.0060]
     */
    public function toArray(): array
    {
        return ['longitude' => $this->value];
    }

    /**
     * Check if this longitude is in the eastern hemisphere.
     *
     * @return bool True if the longitude is greater than 0
     *
     * @example
     * $longitude = new Longitude(120.0);
     * $longitude->isEastern(); // true
     */
    public function isEastern(): bool
    {
        return $this->value > 0.0;
    }

    /**
     * Check if this longitude is in the western hemisphere.
     *
     * @return bool True if the longitude is less than 0
     *
     * @example
     * $longitude = new Longitude(-120.0);
     * $longitude->isWestern(); // true
     */
    public function isWestern(): bool
    {
        return $this->value < 0.0;
    }

    /**
     * Check if this longitude is at the prime meridian.
     *
     * @return bool True if the longitude is exactly 0
     *
     * @example
     * $longitude = new Longitude(0.0);
     * $longitude->isPrimeMeridian(); // true
     */
    public function isPrimeMeridian(): bool
    {
        return $this->value === 0.0;
    }

    /**
     * Check if this longitude is at the international date line.
     *
     * @return bool True if the longitude is exactly 180 or -180
     *
     * @example
     * $longitude = new Longitude(180.0);
     * $longitude->isInternationalDateLine(); // true
     */
    public function isInternationalDateLine(): bool
    {
        return $this->value === 180.0 || $this->value === -180.0;
    }
}
