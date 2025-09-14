<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Shared\Clients;

use JeroenGerits\Support\Geo\Clients\Nominatim;
use JeroenGerits\Support\Geo\Contracts\Geocoding;
use JeroenGerits\Support\Weather\Clients\OpenWeatherMap;
use JeroenGerits\Support\Weather\Contracts\Weather;

class ServiceFactory
{
    public static function createGeocodingClient(string $provider, array $config = []): Geocoding
    {
        return match ($provider) {
            'nominatim' => new Nominatim($config['httpClient'], $config),
            default => throw new \InvalidArgumentException("Unknown geocoding provider: {$provider}")
        };
    }

    public static function createWeatherClient(string $provider, array $config = []): Weather
    {
        return match ($provider) {
            'openweathermap' => new OpenWeatherMap($config['httpClient'], $config),
            default => throw new \InvalidArgumentException("Unknown weather provider: {$provider}")
        };
    }
}
