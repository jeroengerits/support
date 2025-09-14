<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Contracts;

/**
 * Interface for objects that can be compared for equality.
 *
 * This interface defines a contract for objects that can determine
 * whether they are equal to another object of the same type. This is
 * particularly useful for value objects where equality is based on
 * the object's content rather than its identity.
 *
 * @example
 * ```php
 * use JeroenGerits\Support\Contracts\Equatable;
 * use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
 * use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
 * use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;
 *
 * // Create two coordinate objects with the same values
 * $coord1 = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));
 * $coord2 = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));
 *
 * // Check if they are equal
 * $isEqual = $coord1->isEqual($coord2); // true
 *
 * // Different coordinates
 * $coord3 = new Coordinates(new Latitude(51.5074), new Longitude(-0.1278));
 * $isEqual = $coord1->isEqual($coord3); // false
 * ```
 */
interface Equatable
{
    /**
     * Check if this object is equal to another.
     *
     * This method should implement value-based equality comparison,
     * where two objects are considered equal if they represent the
     * same value, regardless of their object identity.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if objects are equal, false otherwise
     *
     * @example
     * ```php
     * // Value objects with same content should be equal
     * $lat1 = new Latitude(40.7128);
     * $lat2 = new Latitude(40.7128);
     * $isEqual = $lat1->isEqual($lat2); // true
     *
     * // Value objects with different content should not be equal
     * $lat3 = new Latitude(51.5074);
     * $isEqual = $lat1->isEqual($lat3); // false
     *
     * // Different types should not be equal
     * $lng = new Longitude(-74.0060);
     * $isEqual = $lat1->isEqual($lng); // false
     * ```
     */
    public function isEqual(Equatable $other): bool;
}
