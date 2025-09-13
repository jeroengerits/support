<?php

declare(strict_types=1);

use JeroenGerits\Support\Exception\InvalidCoordinatesException;
use JeroenGerits\Support\Exception\InvalidLatitudeException;
use JeroenGerits\Support\Exception\InvalidLongitudeException;
use JeroenGerits\Support\ValueObject\Coordinates;
use JeroenGerits\Support\ValueObject\Latitude;
use JeroenGerits\Support\ValueObject\Longitude;

// Test latitude helper
it('creates latitude from float', function (): void {
    $latitude = latitude(40.7128);
    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(40.7128);
});

it('creates latitude from string', function (): void {
    $latitude = latitude('40.7128');
    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(40.7128);
});

it('creates latitude from array with latitude key', function (): void {
    $latitude = latitude(['latitude' => 40.7128]);
    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(40.7128);
});

it('creates latitude from array with numeric index', function (): void {
    $latitude = latitude([40.7128]);
    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(40.7128);
});

it('throws exception for invalid latitude array', function (): void {
    expect(fn (): \JeroenGerits\Support\ValueObject\Latitude => latitude(['invalid' => 'value']))
        ->toThrow(InvalidLatitudeException::class, 'Invalid latitude value provided');
});

// Test longitude helper
it('creates longitude from float', function (): void {
    $longitude = longitude(-74.0060);
    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(-74.0060);
});

it('creates longitude from string', function (): void {
    $longitude = longitude('-74.0060');
    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(-74.0060);
});

it('creates longitude from array with longitude key', function (): void {
    $longitude = longitude(['longitude' => -74.0060]);
    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(-74.0060);
});

it('creates longitude from array with numeric index', function (): void {
    $longitude = longitude([-74.0060]);
    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(-74.0060);
});

it('throws exception for invalid longitude array', function (): void {
    expect(fn (): \JeroenGerits\Support\ValueObject\Longitude => longitude(['invalid' => 'value']))
        ->toThrow(InvalidLongitudeException::class, 'Invalid longitude value provided');
});

// Test coordinates helper
it('creates coordinates from two floats', function (): void {
    $coordinates = coordinates(40.7128, -74.0060);
    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('creates coordinates from two integers', function (): void {
    $coordinates = coordinates(40, -74);
    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value)->toBe(40.0)
        ->and($coordinates->longitude()->value)->toBe(-74.0);
});

it('creates coordinates from array', function (): void {
    $coordinates = coordinates(['latitude' => 40.7128, 'longitude' => -74.0060]);
    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('creates coordinates from string', function (): void {
    $coordinates = coordinates('40.7128,-74.0060');
    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('creates coordinates from latitude string and longitude float', function (): void {
    $coordinates = coordinates('40.7128', -74.0060);
    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('creates coordinates from latitude float and longitude string', function (): void {
    $coordinates = coordinates(40.7128, '-74.0060');
    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('creates coordinates from two strings', function (): void {
    $coordinates = coordinates('40.7128', '-74.0060');
    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('throws exception for invalid coordinates parameters', function (): void {
    expect(fn (): \JeroenGerits\Support\ValueObject\Coordinates => coordinates('invalid'))
        ->toThrow(InvalidCoordinatesException::class, 'Invalid coordinates format. Expected "latitude,longitude"');
});

it('throws exception for invalid coordinates array', function (): void {
    expect(fn (): \JeroenGerits\Support\ValueObject\Coordinates => coordinates(['invalid' => 'value']))
        ->toThrow(InvalidCoordinatesException::class);
});

// Test dd helper
it('dd function exists', function (): void {
    expect(function_exists('dd'))->toBeTrue();
});
