<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Contracts;

/**
 * Interface for objects that can be compared for equality.
 *
 * This interface defines a contract for objects that can determine
 * whether they are equal to another object of the same type.
 */
interface Equatable
{
    /**
     * Check if this object is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if objects are equal
     */
    public function isEqual(Equatable $other): bool;
}
