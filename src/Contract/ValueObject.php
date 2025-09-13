<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Contract;

use Stringable;

/**
 * Contract for value objects.
 *
 * Value objects are immutable objects that represent a concept or value
 * in the domain. They are characterized by their value rather than their
 * identity, and two value objects with the same value are considered equal.
 *
 * @since 0.0.1
 */
interface ValueObject extends Stringable
{
    /**
     * Check if this value object is equal to another value object.
     *
     * @param  ValueObject $other The other value object to compare with
     * @return bool        True if the value objects are equal, false otherwise
     *
     * @example
     * $value1 = new MyValueObject('test');
     * $value2 = new MyValueObject('test');
     * $value1->equals($value2); // true
     */
    public function equals(ValueObject $other): bool;

    /**
     * Convert the value object to an array representation.
     *
     * @return array<string, mixed> Array representation of the value object
     *
     * @example
     * $value = new MyValueObject('test');
     * $value->toArray(); // ['value' => 'test']
     */
    public function toArray(): array;
}
