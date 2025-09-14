<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Geo\Contracts;

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Geo\ValueObjects\LocationInformation;

interface Geocoding
{
    public function reverseGeocode(Coordinates $coordinates): LocationInformation;

    public function geocode(string $address): ?Coordinates;

    public function isAvailable(): bool;

    public function getProviderName(): string;
}
