<?php

declare(strict_types=1);

namespace JeroenGerits\Support\ValueObject;

use JeroenGerits\Support\Contract\ValueObject;
use JeroenGerits\Support\Enum\DistanceUnit;
use JeroenGerits\Support\Exception\InvalidCoordinatesException;
use JeroenGerits\Support\Exception\InvalidLatitudeException;
use JeroenGerits\Support\Exception\InvalidLongitudeException;

final readonly class Coordinates implements ValueObject
{
    /**
     * Create a new Coordinates instance.
     *
     * @param Latitude  $latitude  The latitude value object
     * @param Longitude $longitude The longitude value object
     *
     * @example
     * $latitude = new Latitude(40.7128);
     * $longitude = new Longitude(-74.0060);
     * $coordinates = new Coordinates($latitude, $longitude);
     */
    public function __construct(
        private Latitude $latitude,
        private Longitude $longitude
    ) {}

    /**
     * Create coordinates from float values.
     *
     * @param  float $latitude  The latitude value in decimal degrees
     * @param  float $longitude The longitude value in decimal degrees
     * @return self  New Coordinates instance
     *
     * @throws InvalidLatitudeException  When latitude is out of range
     * @throws InvalidLongitudeException When longitude is out of range
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, -74.0060);
     */
    public static function fromFloats(float $latitude, float $longitude): self
    {
        return new self(
            new Latitude($latitude),
            new Longitude($longitude)
        );
    }

    /**
     * Create coordinates from an associative array.
     *
     * @param  array{latitude: float, longitude: float} $data Array with latitude and longitude keys
     * @return self                                     New Coordinates instance
     *
     * @throws InvalidCoordinatesException When required keys are missing
     * @throws InvalidLatitudeException    When latitude is out of range
     * @throws InvalidLongitudeException   When longitude is out of range
     *
     * @example
     * $coordinates = Coordinates::fromArray([
     *     'latitude' => 40.7128,
     *     'longitude' => -74.0060,
     * ]);
     */
    public static function fromArray(array $data): self
    {
        if (! isset($data['latitude']) || ! isset($data['longitude'])) {
            throw InvalidCoordinatesException::missingArrayKeys();
        }

        return new self(
            new Latitude($data['latitude']),
            new Longitude($data['longitude'])
        );
    }

    /**
     * Create coordinates from a string in "latitude,longitude" format.
     *
     * @param  string $coordinates String in format "lat,lon" or "lat, lon"
     * @return self   New Coordinates instance
     *
     * @throws InvalidCoordinatesException When format is invalid
     * @throws InvalidLatitudeException    When latitude is out of range
     * @throws InvalidLongitudeException   When longitude is out of range
     *
     * @example
     * $coordinates = Coordinates::fromString('40.7128,-74.0060');
     * $coordinates = Coordinates::fromString('40.7128, -74.0060'); // Space after comma is OK
     */
    public static function fromString(string $coordinates): self
    {
        $parts = explode(',', $coordinates);

        if (count($parts) !== 2) {
            throw InvalidCoordinatesException::invalidStringFormat();
        }

        $latitude = trim($parts[0]);
        $longitude = trim($parts[1]);

        // Check if either part is empty
        // @phpstan-ignore-next-line
        if ($latitude === '' || $latitude === '0' || ($longitude === '' || $longitude === '0')) {
            throw InvalidCoordinatesException::invalidStringFormat();
        }

        return new self(
            new Latitude((float) $latitude),
            new Longitude((float) $longitude)
        );
    }

    /**
     * Get the latitude component.
     *
     * @return Latitude The latitude value object
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, -74.0060);
     * $latitude = $coordinates->latitude();
     * echo $latitude->value(); // 40.7128
     */
    public function latitude(): Latitude
    {
        return $this->latitude;
    }

    /**
     * Get the longitude component.
     *
     * @return Longitude The longitude value object
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, -74.0060);
     * $longitude = $coordinates->longitude();
     * echo $longitude->value(); // -74.0060
     */
    public function longitude(): Longitude
    {
        return $this->longitude;
    }

    /**
     * Convert the coordinates to a string representation.
     *
     * @return string The coordinates as "latitude,longitude" string
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, -74.0060);
     * echo (string) $coordinates; // "40.7128,-74.0060"
     */
    public function __toString(): string
    {
        return $this->latitude->value().','.$this->longitude->value();
    }

    /**
     * Convert the coordinates to an array representation.
     *
     * @return array{latitude: float, longitude: float} The coordinates as an associative array
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, -74.0060);
     * $coordinates->toArray(); // ['latitude' => 40.7128, 'longitude' => -74.0060]
     */
    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude->value,
            'longitude' => $this->longitude->value,
        ];
    }

    /**
     * Check if these coordinates are in the northern hemisphere.
     *
     * @return bool True if the latitude is greater than 0
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, -74.0060);
     * $coordinates->isNorthern(); // true
     */
    public function isNorthern(): bool
    {
        return $this->latitude->isNorthern();
    }

    /**
     * Check if these coordinates are in the southern hemisphere.
     *
     * @return bool True if the latitude is less than 0
     *
     * @example
     * $coordinates = Coordinates::fromFloats(-40.7128, -74.0060);
     * $coordinates->isSouthern(); // true
     */
    public function isSouthern(): bool
    {
        return $this->latitude->isSouthern();
    }

    /**
     * Check if these coordinates are in the eastern hemisphere.
     *
     * @return bool True if the longitude is greater than 0
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, 120.0);
     * $coordinates->isEastern(); // true
     */
    public function isEastern(): bool
    {
        return $this->longitude->isEastern();
    }

    /**
     * Check if these coordinates are in the western hemisphere.
     *
     * @return bool True if the longitude is less than 0
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, -120.0);
     * $coordinates->isWestern(); // true
     */
    public function isWestern(): bool
    {
        return $this->longitude->isWestern();
    }

    /**
     * Check if these coordinates are at the equator.
     *
     * @return bool True if the latitude is exactly 0
     *
     * @example
     * $coordinates = Coordinates::fromFloats(0.0, -74.0060);
     * $coordinates->isEquator(); // true
     */
    public function isEquator(): bool
    {
        return $this->latitude->isEquator();
    }

    /**
     * Check if these coordinates are at the international date line.
     *
     * @return bool True if the longitude is exactly 180 or -180
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, 180.0);
     * $coordinates->isInternationalDateLine(); // true
     */
    public function isInternationalDateLine(): bool
    {
        return $this->longitude->isInternationalDateLine();
    }

    /**
     * Check if these coordinates are at the Greenwich meridian.
     *
     * This is an alias for isPrimeMeridian().
     *
     * @return bool True if the longitude is exactly 0
     *
     * @example
     * $coordinates = Coordinates::fromFloats(51.4769, 0.0);
     * $coordinates->isGreenwichMeridian(); // true
     */
    public function isGreenwichMeridian(): bool
    {
        return $this->longitude->isPrimeMeridian();
    }

    /**
     * Check if these coordinates are at the prime meridian.
     *
     * @return bool True if the longitude is exactly 0
     *
     * @example
     * $coordinates = Coordinates::fromFloats(40.7128, 0.0);
     * $coordinates->isPrimeMeridian(); // true
     */
    public function isPrimeMeridian(): bool
    {
        return $this->longitude->isPrimeMeridian();
    }

    /**
     * Calculate the distance to another set of coordinates using the Haversine formula.
     *
     * @param  Coordinates  $coordinates  The target coordinates
     * @param  DistanceUnit $distanceUnit The unit for the returned distance (default: kilometers)
     * @return float        The distance between the coordinates in the specified unit
     *
     * @example
     * $newYork = Coordinates::fromFloats(40.7128, -74.0060);
     * $london = Coordinates::fromFloats(51.5074, -0.1278);
     * $distanceKm = $newYork->distanceTo($london); // ~5570.0
     * $distanceMiles = $newYork->distanceTo($london, DistanceUnit::MILES); // ~3458.0
     */
    public function distanceTo(Coordinates $coordinates, DistanceUnit $distanceUnit = DistanceUnit::KILOMETERS): float
    {
        if ($this->equals($coordinates)) {
            return 0.0;
        }

        $lat1 = deg2rad($this->latitude->value());
        $lon1 = deg2rad($this->longitude->value());
        $lat2 = deg2rad($coordinates->latitude->value());
        $lon2 = deg2rad($coordinates->longitude->value());

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * asin(sqrt($a));

        // Earth's radius in kilometers
        $earthRadiusKm = 6371.0;
        $distanceKm = $earthRadiusKm * $c;

        return $distanceKm * $distanceUnit->getConversionFactor();
    }

    /**
     * Check if these coordinates equal another value object.
     *
     * @param  ValueObject $other The value object to compare with
     * @return bool        True if both latitude and longitude are equal
     *
     * @example
     * $coords1 = Coordinates::fromFloats(40.7128, -74.0060);
     * $coords2 = Coordinates::fromFloats(40.7128, -74.0060);
     * $coords1->equals($coords2); // true
     */
    public function equals(ValueObject $other): bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->latitude->equals($other->latitude) &&
            $this->longitude->equals($other->longitude);
    }
}
