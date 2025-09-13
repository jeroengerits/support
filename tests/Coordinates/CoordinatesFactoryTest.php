<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

it('creates coordinates with float parameters', function (): void {
    $latitude = 52.3676;
    $longitude = 4.9041;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe($latitude);
    expect($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with string parameters', function (): void {
    $latitude = '52.3676';
    $longitude = '4.9041';

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe(52.3676);
    expect($coordinates->longitude->value)->toBe(4.9041);
});

it('creates coordinates with latitude and longitude objects', function (): void {
    $latitude = new Latitude(52.3676);
    $longitude = new Longitude(4.9041);

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude)->toBe($latitude);
    expect($coordinates->longitude)->toBe($longitude);
});

it('creates coordinates with mixed parameters', function (): void {
    $latitude = new Latitude(52.3676);
    $longitude = 4.9041;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude)->toBe($latitude);
    expect($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with string latitude and longitude object', function (): void {
    $latitude = '52.3676';
    $longitude = new Longitude(4.9041);

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe(52.3676);
    expect($coordinates->longitude)->toBe($longitude);
});

it('throws exception for invalid latitude type', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates(null, 4.9041))
        ->toThrow(InvalidArgumentException::class, 'Invalid latitude value');
});

it('throws exception for invalid longitude type', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates(52.3676, null))
        ->toThrow(InvalidArgumentException::class, 'Invalid longitude value');
});

it('throws exception for array latitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates([52.3676], 4.9041))
        ->toThrow(InvalidArgumentException::class, 'Invalid latitude value');
});

it('throws exception for array longitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates(52.3676, [4.9041]))
        ->toThrow(InvalidArgumentException::class, 'Invalid longitude value');
});

it('creates coordinates with negative values', function (): void {
    $latitude = -52.3676;
    $longitude = -4.9041;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe($latitude);
    expect($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with zero values', function (): void {
    $latitude = 0.0;
    $longitude = 0.0;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe($latitude);
    expect($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with string zero values', function (): void {
    $latitude = '0.0';
    $longitude = '0.0';

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe(0.0);
    expect($coordinates->longitude->value)->toBe(0.0);
});

it('creates coordinates with integer values', function (): void {
    $latitude = 52;
    $longitude = 4;

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe(52.0);
    expect($coordinates->longitude->value)->toBe(4.0);
});

it('creates coordinates with string integer values', function (): void {
    $latitude = '52';
    $longitude = '4';

    $coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class);
    expect($coordinates->latitude->value)->toBe(52.0);
    expect($coordinates->longitude->value)->toBe(4.0);
});

it('throws exception for boolean latitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates(true, 4.9041))
        ->toThrow(InvalidArgumentException::class, 'Invalid latitude value');
});

it('throws exception for boolean longitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates(52.3676, false))
        ->toThrow(InvalidArgumentException::class, 'Invalid longitude value');
});

it('throws exception for object latitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates(new stdClass, 4.9041))
        ->toThrow(InvalidArgumentException::class, 'Invalid latitude value');
});

it('throws exception for object longitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates => CoordinatesFactory::createCoordinates(52.3676, new stdClass))
        ->toThrow(InvalidArgumentException::class, 'Invalid longitude value');
});
