<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Weather\Contracts;

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Weather\ValueObjects\WeatherInformation;

interface Weather
{
    public function getCurrentWeather(Coordinates $coordinates): WeatherInformation;

    public function isAvailable(): bool;

    public function getProviderName(): string;
}
