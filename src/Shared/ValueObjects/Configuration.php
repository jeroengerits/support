<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Shared\ValueObjects;

class Configuration
{
    public function __construct(
        public readonly array $geocoding = [],
        public readonly array $weather = [],
        public readonly array $http = [],
        public readonly array $cache = []
    ) {}

    public static function fromEnvironment(): self
    {
        return new self(
            geocoding: [
                'nominatim' => [
                    'user_agent' => $_ENV['NOMINATIM_USER_AGENT'] ?? 'Support Package/1.0',
                    'email' => $_ENV['NOMINATIM_EMAIL'] ?? null,
                    'timeout' => (int) ($_ENV['NOMINATIM_TIMEOUT'] ?? 30),
                ],
            ],
            weather: [
                'openweathermap' => [
                    'api_key' => $_ENV['OPENWEATHER_API_KEY'] ?? null,
                    'units' => $_ENV['OPENWEATHER_UNITS'] ?? 'metric',
                ],
            ],
            http: [
                'timeout' => (int) ($_ENV['HTTP_TIMEOUT'] ?? 30),
                'retry_attempts' => (int) ($_ENV['HTTP_RETRY_ATTEMPTS'] ?? 3),
            ],
            cache: [
                'ttl' => (int) ($_ENV['CACHE_TTL'] ?? 3600),
                'enabled' => filter_var($_ENV['CACHE_ENABLED'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            ]
        );
    }
}
