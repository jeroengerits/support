<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\CoordinatesFactory;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

it('creates coordinates with latitude and longitude using factory', function (): void {
    $coordinates = CoordinatesFactory::createCoordinates(40.7128, -74.0060);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude->value)->toBe(40.7128)
        ->and($coordinates->longitude->value)->toBe(-74.0060);
});

it('calculates distance between coordinates using Coordinates objects', function (): void {
    $a = CoordinatesFactory::createCoordinates(52.3676, 4.9041);
    $b = CoordinatesFactory::createCoordinates(52.3736, 4.9101);
    $distance = $a->distanceTo($b);

    expect($distance)->toBeGreaterThan(0.7)
        ->and($distance)->toBeLessThan(0.9);
});

it('calculates distance in miles when specified', function (): void {
    $a = CoordinatesFactory::createCoordinates(52.3676, 4.9041);
    $b = CoordinatesFactory::createCoordinates(52.3736, 4.9101);
    $distance = $a->distanceTo($b, DistanceUnit::MILES);

    expect($distance)->toBeGreaterThan(0.4)
        ->and($distance)->toBeLessThan(0.6);
});

it('returns zero distance for identical coordinates', function (): void {
    $a = CoordinatesFactory::createCoordinates(52.3676, 4.9041);
    $b = CoordinatesFactory::createCoordinates(52.3676, 4.9041);
    $distance = $a->distanceTo($b, DistanceUnit::MILES);

    expect($distance)->toBe(0.0);
});

it('calculates distance with custom distance unit', function (): void {
    $a = CoordinatesFactory::createCoordinates(52.3676, 4.9041);
    $b = CoordinatesFactory::createCoordinates(52.3736, 4.9101);

    $distanceKm = $a->distanceTo($b, DistanceUnit::KILOMETERS);
    $distanceMiles = $a->distanceTo($b, DistanceUnit::MILES);

    expect($distanceKm)->toBeGreaterThan(0.7)
        ->and($distanceKm)->toBeLessThan(0.9)
        ->and($distanceMiles)->toBeGreaterThan(0.4)
        ->and($distanceMiles)->toBeLessThan(0.6);
});
