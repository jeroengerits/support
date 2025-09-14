<?php

declare(strict_types=1);

use JeroenGerits\Support\Geo\Contracts\Geocoding;
use JeroenGerits\Support\Http\Contracts\HttpClient;
use JeroenGerits\Support\Shared\Clients\ServiceFactory;
use JeroenGerits\Support\Weather\Contracts\Weather;

describe('Shared Domain', function (): void {
    describe('ServiceFactory', function (): void {
        it('creates Nominatim geocoding client', function (): void {
            $httpClient = Mockery::mock(HttpClient::class);
            $config = ['httpClient' => $httpClient];

            $client = ServiceFactory::createGeocodingClient('nominatim', $config);

            expect($client)->toBeInstanceOf(Geocoding::class);
        });

        it('creates OpenWeatherMap weather client', function (): void {
            $httpClient = Mockery::mock(HttpClient::class);
            $config = [
                'httpClient' => $httpClient,
                'api_key' => 'test_key',
            ];

            $client = ServiceFactory::createWeatherClient('openweathermap', $config);

            expect($client)->toBeInstanceOf(Weather::class);
        });

        it('throws exception for unknown geocoding provider', function (): void {
            $httpClient = Mockery::mock(HttpClient::class);
            $config = ['httpClient' => $httpClient];

            expect(fn (): \JeroenGerits\Support\Geo\Contracts\Geocoding => ServiceFactory::createGeocodingClient('unknown', $config))
                ->toThrow(InvalidArgumentException::class, 'Unknown geocoding provider: unknown');
        });

        it('throws exception for unknown weather provider', function (): void {
            $httpClient = Mockery::mock(HttpClient::class);
            $config = ['httpClient' => $httpClient];

            expect(fn (): \JeroenGerits\Support\Weather\Contracts\Weather => ServiceFactory::createWeatherClient('unknown', $config))
                ->toThrow(InvalidArgumentException::class, 'Unknown weather provider: unknown');
        });

        it('passes configuration to geocoding client', function (): void {
            $httpClient = Mockery::mock(HttpClient::class);
            $config = [
                'httpClient' => $httpClient,
                'user_agent' => 'TestApp/1.0',
                'timeout' => 60,
            ];

            $client = ServiceFactory::createGeocodingClient('nominatim', $config);

            expect($client)->toBeInstanceOf(Geocoding::class);
        });

        it('passes configuration to weather client', function (): void {
            $httpClient = Mockery::mock(HttpClient::class);
            $config = [
                'httpClient' => $httpClient,
                'api_key' => 'test_key',
                'units' => 'imperial',
            ];

            $client = ServiceFactory::createWeatherClient('openweathermap', $config);

            expect($client)->toBeInstanceOf(Weather::class);
        });
    });
});
