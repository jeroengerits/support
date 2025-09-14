<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

it('creates coordinates with latitude and longitude', function (): void {
    $coordinates = coordinates(40.7128, -74.0060);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(40.7128)
        ->and($coordinates->longitude->value)->toBe(-74.0060);
});

it('creates coordinates with string values', function (): void {
    $coordinates = coordinates('40.7128', '-74.0060');

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(40.7128)
        ->and($coordinates->longitude->value)->toBe(-74.0060);
});

it('creates coordinates with array input', function (): void {
    $coordinates = coordinates(['lat' => 40.7128, 'lng' => -74.0060]);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(40.7128)
        ->and($coordinates->longitude->value)->toBe(-74.0060);
});

it('creates latitude with float value', function (): void {
    $latitude = latitude(40.7128);

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(40.7128);
});

it('creates latitude with string value', function (): void {
    $latitude = latitude('40.7128');

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(40.7128);
});

it('creates latitude with int value', function (): void {
    $latitude = latitude(40);

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(40.0);
});

it('returns existing latitude object', function (): void {
    $originalLatitude = new Latitude(40.7128);
    $latitude = latitude($originalLatitude);

    expect($latitude)->toBe($originalLatitude);
});

it('creates longitude with float value', function (): void {
    $longitude = longitude(-74.0060);

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(-74.0060);
});

it('creates longitude with string value', function (): void {
    $longitude = longitude('-74.0060');

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(-74.0060);
});

it('creates longitude with int value', function (): void {
    $longitude = longitude(-74);

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(-74.0);
});

it('returns existing longitude object', function (): void {
    $originalLongitude = new Longitude(-74.0060);
    $longitude = longitude($originalLongitude);

    expect($longitude)->toBe($originalLongitude);
});

it('throws exception for invalid longitude value', function (): void {
    expect(fn (): Longitude => longitude(new stdClass))
        ->toThrow(InvalidCoordinatesException::class);
});

it('can be used together to create coordinates', function (): void {
    $lat = latitude(40.7128);
    $lng = longitude(-74.0060);
    $coordinates = coordinates($lat, $lng);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude)->toBe($lat)
        ->and($coordinates->longitude)->toBe($lng);
});

it('can be chained for fluent creation', function (): void {
    $coordinates = coordinates(
        latitude(40.7128),
        longitude(-74.0060)
    );

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(40.7128)
        ->and($coordinates->longitude->value)->toBe(-74.0060);
});

it('calculates distance between coordinates using Coordinates objects', function (): void {
    $a = coordinates(52.3676, 4.9041);
    $b = coordinates(52.3736, 4.9101);
    $distance = $a->distanceBetween($b);

    expect($distance)->toBeGreaterThan(0.7)
        ->and($distance)->toBeLessThan(0.9);
});

it('calculates distance in miles when specified', function (): void {
    $a = coordinates(52.3676, 4.9041);
    $b = coordinates(52.3736, 4.9101);
    $distance = $a->distanceBetweenInMiles($b);

    expect($distance)->toBeGreaterThan(0.4)
        ->and($distance)->toBeLessThan(0.6);
});

it('returns zero distance for identical coordinates', function (): void {
    $a = coordinates(52.3676, 4.9041);
    $b = coordinates(52.3676, 4.9041);
    $distance = $a->distanceBetweenInMiles($b);

    expect($distance)->toBe(0.0);
});

it('calculates distance with individual latitude and longitude parameters', function (): void {
    $a = coordinates(52.3676, 4.9041);
    $distance = $a->distanceBetween(52.3736, 4.9101);

    expect($distance)->toBeGreaterThan(0.7)
        ->and($distance)->toBeLessThan(0.9);
});

it('calculates distance with string parameters', function (): void {
    $a = coordinates(52.3676, 4.9041);
    $distance = $a->distanceBetween('52.3736', '4.9101');

    expect($distance)->toBeGreaterThan(0.7)
        ->and($distance)->toBeLessThan(0.9);
});

it('calculates distance with array parameter', function (): void {
    $a = coordinates(52.3676, 4.9041);
    $distance = $a->distanceBetween(['lat' => 52.3736, 'lng' => 4.9101]);

    expect($distance)->toBeGreaterThan(0.7)
        ->and($distance)->toBeLessThan(0.9);
});

it('handles invalid string parameters by converting them to zero', function (): void {
    $a = coordinates(52.3676, 4.9041);

    // Invalid string parameters get converted to 0.0 by PHP's (float) cast
    $distance1 = $a->distanceBetween('invalid_latitude', 4.9101);
    $distance2 = $a->distanceBetween(52.3736, 'invalid_longitude');

    // Both should return valid distances (not throw exceptions)
    expect($distance1)->toBeGreaterThan(0)
        ->and($distance2)->toBeGreaterThan(0);
});

it('throws exception when distance method receives invalid array parameter', function (): void {
    $a = coordinates(52.3676, 4.9041);

    expect(fn (): float => $a->distanceBetween(['invalid' => 'structure']))
        ->toThrow(InvalidCoordinatesException::class);
});

it('calculates distance with custom distance unit', function (): void {
    $a = coordinates(52.3676, 4.9041);
    $b = coordinates(52.3736, 4.9101);

    $distanceKm = $a->distanceBetween($b, null, DistanceUnit::KILOMETERS);
    $distanceMiles = $a->distanceBetween($b, null, DistanceUnit::MILES);

    expect($distanceKm)->toBeGreaterThan(0)
        ->and($distanceMiles)->toBeGreaterThan(0)
        ->and($distanceKm)->toBeGreaterThan($distanceMiles);
});
