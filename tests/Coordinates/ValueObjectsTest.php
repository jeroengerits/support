<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

it('creates coordinates with float parameters', function (): void {
    $latitude = 52.3676;
    $longitude = 4.9041;

    $coordinates = Coordinates::create($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe($latitude)
        ->and($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with negative values', function (): void {
    $latitude = -52.3676;
    $longitude = -4.9041;

    $coordinates = Coordinates::create($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe($latitude)
        ->and($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with zero values', function (): void {
    $latitude = 0.0;
    $longitude = 0.0;

    $coordinates = Coordinates::create($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe($latitude)
        ->and($coordinates->longitude->value)->toBe($longitude);
});

it('creates coordinates with integer values', function (): void {
    $latitude = 52;
    $longitude = 4;

    $coordinates = Coordinates::create($latitude, $longitude);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(52.0)
        ->and($coordinates->longitude->value)->toBe(4.0);
});

// Tests for createLatitude method
it('creates latitude with float value', function (): void {
    $latitude = Latitude::create(52.3676);

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(52.3676);
});

it('creates latitude with int value', function (): void {
    $latitude = Latitude::create(52);

    expect($latitude)->toBeInstanceOf(Latitude::class)
        ->and($latitude->value)->toBe(52.0);
});

// Tests for createLongitude method
it('creates longitude with float value', function (): void {
    $longitude = Longitude::create(4.9041);

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(4.9041);
});

it('creates longitude with int value', function (): void {
    $longitude = Longitude::create(4);

    expect($longitude)->toBeInstanceOf(Longitude::class)
        ->and($longitude->value)->toBe(4.0);
});
