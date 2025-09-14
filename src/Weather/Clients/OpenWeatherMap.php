<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Weather\Clients;

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Http\Contracts\HttpClient;
use JeroenGerits\Support\Weather\Contracts\Weather;
use JeroenGerits\Support\Weather\Exceptions\WeatherException;
use JeroenGerits\Support\Weather\ValueObjects\WeatherInformation;

class OpenWeatherMap implements Weather
{
    private const string PROVIDER_NAME = 'OpenWeatherMap';

    private const string BASE_URL = 'https://api.openweathermap.org/data/2.5';

    public function __construct(
        private HttpClient $httpClient,
        private array $config = []
    ) {
        if (empty($this->config['api_key'])) {
            throw WeatherException::apiKeyInvalid(self::PROVIDER_NAME);
        }
    }

    public function getCurrentWeather(Coordinates $coordinates): WeatherInformation
    {
        $url = $this->buildCurrentWeatherUrl($coordinates);
        $response = $this->httpClient->get($url);

        if (! $response->isSuccessful()) {
            if ($response->getStatusCode() === 401) {
                throw WeatherException::apiKeyInvalid(self::PROVIDER_NAME);
            }
            if ($response->getStatusCode() === 429) {
                throw WeatherException::rateLimitExceeded(self::PROVIDER_NAME);
            }

            throw WeatherException::serviceUnavailable(self::PROVIDER_NAME);
        }

        return $this->parseWeatherInformation($response->getJson());
    }

    public function isAvailable(): bool
    {
        try {
            // Test with a simple request
            $testUrl = self::BASE_URL.'/weather?lat=0&lon=0&appid='.$this->config['api_key'];
            $response = $this->httpClient->get($testUrl);

            return $response->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getProviderName(): string
    {
        return self::PROVIDER_NAME;
    }

    private function buildCurrentWeatherUrl(Coordinates $coordinates): string
    {
        $params = [
            'lat' => $coordinates->latitude->value,
            'lon' => $coordinates->longitude->value,
            'appid' => $this->config['api_key'],
            'units' => $this->config['units'] ?? 'metric',
        ];

        return self::BASE_URL.'/weather?'.http_build_query($params);
    }

    private function parseWeatherInformation(array $data): WeatherInformation
    {
        $main = $data['main'];
        $weather = $data['weather'][0];
        $wind = $data['wind'] ?? [];

        return new WeatherInformation(
            temperature: (float) $main['temp'],
            description: $weather['description'],
            humidity: (int) $main['humidity'],
            pressure: (float) $main['pressure'],
            windSpeed: (float) ($wind['speed'] ?? 0),
            windDirection: (int) ($wind['deg'] ?? 0),
            icon: $weather['icon'] ?? null,
            location: $data['name'] ?? null,
            timestamp: new \DateTimeImmutable
        );
    }
}
