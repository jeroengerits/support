<?php

declare(strict_types=1);

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

it('throws exception for invalid latitude value', function (): void {
    expect(fn (): Latitude => latitude(new stdClass))
        ->toThrow(TypeError::class);
});

it('throws exception for out of range latitude value', function (): void {
    expect(fn (): Latitude => latitude(100.0))
        ->toThrow(\JeroenGerits\Support\Coordinates\Exceptions\InvalidLatitudeException::class, 'Latitude must be between -90 and 90');
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
        ->toThrow(TypeError::class);
});

it('throws exception for out of range longitude value', function (): void {
    expect(fn (): Longitude => longitude(200.0))
        ->toThrow(\JeroenGerits\Support\Coordinates\Exceptions\InvalidLongitudeException::class, 'Longitude must be between -180 and 180');
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
