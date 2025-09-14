<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Contracts;

interface Coordinate
{
    public function getValue(): float;

    public function isValid(): bool;

    public function __toString(): string;
}
