<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Contracts;

/**
 * Interface for objects that can be compared for equality.
 */
interface Equatable
{
    /**
     * @param  Equatable $other The other object to compare
     * @return bool      True if objects are equal, false otherwise
     */
    public function isEqual(Equatable $other): bool;
}
