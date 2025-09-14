<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Shared\Contracts;

interface Service
{
    public function isAvailable(): bool;

    public function getName(): string;
}
