<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Geo\Clients\Nominatim;
use JeroenGerits\Support\Geo\Exceptions\GeocodingException;
use JeroenGerits\Support\Geo\ValueObjects\LocationInformation;
use JeroenGerits\Support\Http\Contracts\HttpClient;
use JeroenGerits\Support\Http\Contracts\HttpResponse;

describe('Geo Domain', function (): void {
    describe('Nominatim Client', function (): void {
        it('performs reverse geocoding successfully', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);
            $mockResponse->shouldReceive('getJson')->andReturn([
                'display_name' => 'Amsterdam, Noord-Holland, Nederland',
                'address' => [
                    'city' => 'Amsterdam',
                    'state' => 'Noord-Holland',
                    'country' => 'Nederland',
                    'country_code' => 'nl',
                    'postcode' => '1012',
                ],
                'extratags' => [
                    'timezone' => 'Europe/Amsterdam',
                ],
                'importance' => 0.8,
            ]);

            $mockHttpClient->shouldReceive('get')
                ->with(Mockery::pattern('/nominatim\.openstreetmap\.org\/reverse/'), Mockery::type('array'))
                ->andReturn($mockResponse);

            $nominatim = new Nominatim($mockHttpClient);
            $coordinates = Coordinates::create(52.3676, 4.9041);
            $location = $nominatim->reverseGeocode($coordinates);

            expect($location)->toBeInstanceOf(LocationInformation::class)
                ->and($location->city)->toBe('Amsterdam')
                ->and($location->state)->toBe('Noord-Holland')
                ->and($location->country)->toBe('Nederland')
                ->and($location->countryCode)->toBe('nl')
                ->and($location->postalCode)->toBe('1012')
                ->and($location->timezone)->toBe('Europe/Amsterdam')
                ->and($location->confidence)->toBe(0.8);
        });

        it('performs forward geocoding successfully', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);
            $mockResponse->shouldReceive('getJson')->andReturn([
                [
                    'lat' => '52.3676',
                    'lon' => '4.9041',
                    'display_name' => 'Amsterdam, Noord-Holland, Nederland',
                ],
            ]);

            $mockHttpClient->shouldReceive('get')
                ->with(Mockery::pattern('/nominatim\.openstreetmap\.org\/search/'), Mockery::type('array'))
                ->andReturn($mockResponse);

            $nominatim = new Nominatim($mockHttpClient);
            $coordinates = $nominatim->geocode('Amsterdam, Netherlands');

            expect($coordinates)->toBeInstanceOf(Coordinates::class)
                ->and($coordinates->latitude->value)->toBe(52.3676)
                ->and($coordinates->longitude->value)->toBe(4.9041);
        });

        it('returns null for unsuccessful geocoding', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(false);

            $mockHttpClient->shouldReceive('get')->andReturn($mockResponse);

            $nominatim = new Nominatim($mockHttpClient);
            $coordinates = $nominatim->geocode('Non-existent place');

            expect($coordinates)->toBeNull();
        });

        it('returns null for empty geocoding results', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);
            $mockResponse->shouldReceive('getJson')->andReturn([]);

            $mockHttpClient->shouldReceive('get')->andReturn($mockResponse);

            $nominatim = new Nominatim($mockHttpClient);
            $coordinates = $nominatim->geocode('Non-existent place');

            expect($coordinates)->toBeNull();
        });

        it('throws exception for unsuccessful reverse geocoding', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(false);

            $mockHttpClient->shouldReceive('get')->andReturn($mockResponse);

            $nominatim = new Nominatim($mockHttpClient);
            $coordinates = Coordinates::create(52.3676, 4.9041);

            expect(fn (): \JeroenGerits\Support\Geo\ValueObjects\LocationInformation => $nominatim->reverseGeocode($coordinates))
                ->toThrow(GeocodingException::class, 'Geocoding service \'Nominatim\' is currently unavailable');
        });

        it('checks availability correctly', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);

            $mockHttpClient->shouldReceive('get')
                ->with('https://nominatim.openstreetmap.org/status')
                ->andReturn($mockResponse);

            $nominatim = new Nominatim($mockHttpClient);

            expect($nominatim->isAvailable())->toBeTrue();
        });

        it('returns false for availability when service is down', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);

            $mockHttpClient->shouldReceive('get')
                ->with('https://nominatim.openstreetmap.org/status')
                ->andThrow(new Exception('Service unavailable'));

            $nominatim = new Nominatim($mockHttpClient);

            expect($nominatim->isAvailable())->toBeFalse();
        });

        it('returns correct provider name', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $nominatim = new Nominatim($mockHttpClient);

            expect($nominatim->getProviderName())->toBe('Nominatim');
        });

        it('uses custom configuration', function (): void {
            $mockHttpClient = Mockery::mock(HttpClient::class);
            $mockResponse = Mockery::mock(HttpResponse::class);

            $mockResponse->shouldReceive('isSuccessful')->andReturn(true);
            $mockResponse->shouldReceive('getJson')->andReturn([]);

            $mockHttpClient->shouldReceive('get')
                ->with(Mockery::any(), Mockery::on(function (array $options): bool {
                    return isset($options['headers']['User-Agent']) &&
                           $options['headers']['User-Agent'] === 'CustomApp/1.0' &&
                           $options['timeout'] === 60;
                }))
                ->andReturn($mockResponse);

            $nominatim = new Nominatim($mockHttpClient, [
                'user_agent' => 'CustomApp/1.0',
                'timeout' => 60,
            ]);

            $result = $nominatim->geocode('Test');
            expect($result)->toBeNull();
        });
    });
});
