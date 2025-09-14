<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLatitudeException;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLongitudeException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

it('creates coordinates with float parameters', function (): void {
    $latitude = 52.3676;
    $longitude = 4.9041;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe($latitude)
        ->and($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with string parameters', function (): void {
    $latitude = '52.3676';
    $longitude = '4.9041';

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(52.3676)
        ->and($coordinates->longitude->value)->toBe(4.9041);
});

it('creates coordinates with latitude and longitude objects', function (): void {
    $latitude = new Latitude(52.3676);
    $longitude = new Longitude(4.9041);

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude)->toBe($latitude)
        ->and($coordinates->longitude)->toBe($longitude);
});

it('creates coordinates with mixed parameters', function (): void {
    $latitude = new Latitude(52.3676);
    $longitude = 4.9041;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude)->toBe($latitude)
        ->and($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with string latitude and longitude object', function (): void {
    $latitude = '52.3676';
    $longitude = new Longitude(4.9041);

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(52.3676)
        ->and($coordinates->longitude)->toBe($longitude);
});

it('throws exception for invalid latitude type', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates(null, 4.9041))
        ->toThrow(TypeError::class);
});

it('throws exception for invalid longitude type', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates(52.3676, null))
        ->toThrow(TypeError::class);
});

it('throws exception for array latitude', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates([52.3676], 4.9041))
        ->toThrow(TypeError::class);
});

it('throws exception for array longitude', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates(52.3676, [4.9041]))
        ->toThrow(TypeError::class);
});

it('creates coordinates with negative values', function (): void {
    $latitude = -52.3676;
    $longitude = -4.9041;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe($latitude)
        ->and($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with zero values', function (): void {
    $latitude = 0.0;
    $longitude = 0.0;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe($latitude)
        ->and($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with string zero values', function (): void {
    $latitude = '0.0';
    $longitude = '0.0';

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(0.0)
        ->and($coordinates->longitude->value)->toBe(0.0);
});

it('creates coordinates with integer values', function (): void {
    $latitude = 52;
    $longitude = 4;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(52.0)
        ->and($coordinates->longitude->value)->toBe(4.0);
});

it('creates coordinates with string integer values', function (): void {
    $latitude = '52';
    $longitude = '4';

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(52.0)
        ->and($coordinates->longitude->value)->toBe(4.0);
});

it('throws exception for boolean latitude', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates(true, 4.9041))
        ->toThrow(TypeError::class);
});

it('throws exception for boolean longitude', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates(52.3676, false))
        ->toThrow(TypeError::class);
});

it('throws exception for object latitude', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates(new stdClass, 4.9041))
        ->toThrow(TypeError::class);
});

it('throws exception for object longitude', function (): void {
    expect(fn (): Coordinates => CoordinatesFactory::createCoordinates(52.3676, new stdClass))
        ->toThrow(TypeError::class);
});

// Tests for createLatitude method
it('creates latitude with float value', function (): void {
    $latitude = CoordinatesFactory::createLatitude(52.3676);

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(52.3676);
});

it('creates latitude with string value', function (): void {
    $latitude = CoordinatesFactory::createLatitude('52.3676');

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(52.3676);
});

it('creates latitude with int value', function (): void {
    $latitude = CoordinatesFactory::createLatitude(52);

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(52.0);
});

it('returns existing latitude object', function (): void {
    $originalLatitude = new Latitude(52.3676);
    $latitude = CoordinatesFactory::createLatitude($originalLatitude);

    expect($latitude)->toBe($originalLatitude);
});

it('throws exception for invalid latitude value', function (): void {
    expect(fn (): Latitude => CoordinatesFactory::createLatitude(new stdClass))
        ->toThrow(TypeError::class);
});

it('throws exception for out of range latitude value', function (): void {
    expect(fn (): Latitude => CoordinatesFactory::createLatitude(100.0))
        ->toThrow(InvalidLatitudeException::class, 'Latitude value is outside the valid range of -90.0 to +90.0 degrees');
});

// Tests for createLongitude method
it('creates longitude with float value', function (): void {
    $longitude = CoordinatesFactory::createLongitude(4.9041);

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(4.9041);
});

it('creates longitude with string value', function (): void {
    $longitude = CoordinatesFactory::createLongitude('4.9041');

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(4.9041);
});

it('creates longitude with int value', function (): void {
    $longitude = CoordinatesFactory::createLongitude(4);

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(4.0);
});

it('returns existing longitude object', function (): void {
    $originalLongitude = new Longitude(4.9041);
    $longitude = CoordinatesFactory::createLongitude($originalLongitude);

    expect($longitude)->toBe($originalLongitude);
});

it('throws exception for invalid longitude value', function (): void {
    expect(fn (): Longitude => CoordinatesFactory::createLongitude(new stdClass))
        ->toThrow(TypeError::class);
});

it('throws exception for out of range longitude value', function (): void {
    expect(fn (): Longitude => CoordinatesFactory::createLongitude(200.0))
        ->toThrow(InvalidLongitudeException::class, 'Longitude value is outside the valid range of -180.0 to +180.0 degrees');
});
