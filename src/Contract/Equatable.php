<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Contract;

interface Equatable
{
    public function isEqual(Equatable $other): bool;
}
