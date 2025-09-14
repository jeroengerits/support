<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Http\Contracts\HttpClient;
use JeroenGerits\Support\Http\Contracts\HttpResponse;
use JeroenGerits\Support\Weather\Clients\OpenWeatherMap;
use JeroenGerits\Support\Weather\Exceptions\WeatherException;
use JeroenGerits\Support\Weather\ValueObjects\WeatherInformation;

describe('Weather Domain', function (): void {
    describe('OpenWeatherMap Client', function (): void {
        it('gets current weather successfully', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);
            $mockResponse->shouldReceive('getJson')->andReturn([
                'main' => [
                    'temp' => 15.2,
                    'humidity' => 65,
                    'pressure' => 1013.25,
                ],
                'weather' => [
                    [
                        'description' => 'Clear sky',
                        'icon' => '01d',
                    ],
                ],
                'wind' => [
                    'speed' => 3.5,
                    'deg' => 270,
                ],
                'name' => 'Amsterdam',
            ]);

            $mockHttpClient->shouldReceive('get')
                ->with(Mockery::pattern('/api\.openweathermap\.org\/data\/2\.5\/weather/'))
                ->andReturn($mockResponse);

            $weather = new OpenWeatherMap($mockHttpClient, ['api_key' => 'test_key']);
            $coordinates = Coordinates::create(52.3676, 4.9041);
            $weatherInfo = $weather->getCurrentWeather($coordinates);

            expect($weatherInfo)->toBeInstanceOf(WeatherInformation::class)
                ->and($weatherInfo->temperature)->toBe(15.2)
                ->and($weatherInfo->description)->toBe('Clear sky')
                ->and($weatherInfo->humidity)->toBe(65)
                ->and($weatherInfo->pressure)->toBe(1013.25)
                ->and($weatherInfo->windSpeed)->toBe(3.5)
                ->and($weatherInfo->windDirection)->toBe(270)
                ->and($weatherInfo->icon)->toBe('01d')
                ->and($weatherInfo->location)->toBe('Amsterdam');
        });

        it('throws exception for invalid API key', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(false);
            $mockResponse->shouldReceive('getStatusCode')->andReturn(401);

            $mockHttpClient->shouldReceive('get')->andReturn($mockResponse);

            $weather = new OpenWeatherMap($mockHttpClient, ['api_key' => 'invalid_key']);
            $coordinates = Coordinates::create(52.3676, 4.9041);

            expect(fn (): \JeroenGerits\Support\Weather\ValueObjects\WeatherInformation => $weather->getCurrentWeather($coordinates))
                ->toThrow(WeatherException::class, 'Invalid API key for weather service \'OpenWeatherMap\'');
        });

        it('throws exception for rate limit exceeded', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(false);
            $mockResponse->shouldReceive('getStatusCode')->andReturn(429);

            $mockHttpClient->shouldReceive('get')->andReturn($mockResponse);

            $weather = new OpenWeatherMap($mockHttpClient, ['api_key' => 'test_key']);
            $coordinates = Coordinates::create(52.3676, 4.9041);

            expect(fn (): \JeroenGerits\Support\Weather\ValueObjects\WeatherInformation => $weather->getCurrentWeather($coordinates))
                ->toThrow(WeatherException::class, 'Rate limit exceeded for weather service \'OpenWeatherMap\'');
        });

        it('throws exception for service unavailable', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(false);
            $mockResponse->shouldReceive('getStatusCode')->andReturn(500);

            $mockHttpClient->shouldReceive('get')->andReturn($mockResponse);

            $weather = new OpenWeatherMap($mockHttpClient, ['api_key' => 'test_key']);
            $coordinates = Coordinates::create(52.3676, 4.9041);

            expect(fn (): \JeroenGerits\Support\Weather\ValueObjects\WeatherInformation => $weather->getCurrentWeather($coordinates))
                ->toThrow(WeatherException::class, 'Weather service \'OpenWeatherMap\' is currently unavailable');
        });

        it('throws exception for missing API key', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);

            expect(fn (): \JeroenGerits\Support\Weather\Clients\OpenWeatherMap => new OpenWeatherMap($mockHttpClient, []))
                ->toThrow(WeatherException::class, 'Invalid API key for weather service \'OpenWeatherMap\'');
        });

        it('checks availability correctly', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);

            $mockHttpClient->shouldReceive('get')
                ->with(Mockery::pattern('/api\.openweathermap\.org\/data\/2\.5\/weather/'))
                ->andReturn($mockResponse);

            $weather = new OpenWeatherMap($mockHttpClient, ['api_key' => 'test_key']);

            expect($weather->isAvailable())->toBeTrue();
        });

        it('returns false for availability when service is down', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);

            $mockHttpClient->shouldReceive('get')
                ->with(Mockery::pattern('/api\.openweathermap\.org\/data\/2\.5\/weather/'))
                ->andThrow(new Exception('Service unavailable'));

            $weather = new OpenWeatherMap($mockHttpClient, ['api_key' => 'test_key']);

            expect($weather->isAvailable())->toBeFalse();
        });

        it('returns correct provider name', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $weather = new OpenWeatherMap($mockHttpClient, ['api_key' => 'test_key']);

            expect($weather->getProviderName())->toBe('OpenWeatherMap');
        });

        it('uses custom units configuration', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);
            $mockResponse->shouldReceive('getJson')->andReturn([
                'main' => ['temp' => 59.4, 'humidity' => 65, 'pressure' => 1013.25],
                'weather' => [['description' => 'Clear sky', 'icon' => '01d']],
                'wind' => ['speed' => 3.5, 'deg' => 270],
                'name' => 'Amsterdam',
            ]);

            $mockHttpClient->shouldReceive('get')
                ->with(Mockery::on(function ($url): bool {
                    return str_contains($url, 'units=imperial');
                }))
                ->andReturn($mockResponse);

            $weather = new OpenWeatherMap($mockHttpClient, [
                'api_key' => 'test_key',
                'units' => 'imperial',
            ]);

            $coordinates = Coordinates::create(52.3676, 4.9041);
            $result = $weather->getCurrentWeather($coordinates);
            expect($result)->toBeInstanceOf(\JeroenGerits\Support\Weather\ValueObjects\WeatherInformation::class);
        });
    });
});
