<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude;

test('can be instantiated with latitude and longitude', function (): void {
    $latitude = new Latitude(40.7128);
    $longitude = new Longitude(-74.0060);
    $coordinates = new Coordinates($latitude, $longitude);

    expect($coordinates->latitude())->toBe($latitude);
    expect($coordinates->longitude())->toBe($longitude);
});

test('distance to calculates correct distance in kilometers', function (): void {
    $newYork = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $london = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(51.5074), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-0.1278));

    $distance = $newYork->distanceTo($london, DistanceUnit::KILOMETERS);

    // Approximate distance between NYC and London is ~5570 km
    expect($distance)->toBeGreaterThan(5400);
    expect($distance)->toBeLessThan(5800);
});

test('distance to calculates correct distance in miles', function (): void {
    $newYork = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $london = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(51.5074), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-0.1278));

    $distance = $newYork->distanceTo($london, DistanceUnit::MILES);

    // Approximate distance between NYC and London is ~3460 miles
    expect($distance)->toBeGreaterThan(3300);
    expect($distance)->toBeLessThan(3600);
});

test('distance to returns zero for same coordinates', function (): void {
    $coordinates1 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $coordinates2 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));

    $distance = $coordinates1->distanceTo($coordinates2);

    expect($distance)->toBe(0.0);
});

test('distance to uses kilometers as default', function (): void {
    $newYork = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $london = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(51.5074), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-0.1278));

    $distanceDefault = $newYork->distanceTo($london);
    $distanceKilometers = $newYork->distanceTo($london, DistanceUnit::KILOMETERS);

    expect($distanceDefault)->toBe($distanceKilometers);
});

test('value returns array representation', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));

    expect($coordinates->value())->toBe([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);
});

test('to array returns array representation', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));

    expect($coordinates->toArray())->toBe([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);
});

test('is northern returns true for positive latitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isNorthern())->toBeTrue();
});

test('is northern returns false for negative latitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(-40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isNorthern())->toBeFalse();
});

test('is southern returns true for negative latitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(-40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isSouthern())->toBeTrue();
});

test('is southern returns false for positive latitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isSouthern())->toBeFalse();
});

test('is eastern returns true for positive longitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(74.0060));
    expect($coordinates->isEastern())->toBeTrue();
});

test('is eastern returns false for negative longitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isEastern())->toBeFalse();
});

test('is western returns true for negative longitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isWestern())->toBeTrue();
});

test('is western returns false for positive longitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(74.0060));
    expect($coordinates->isWestern())->toBeFalse();
});

test('is equator returns true for zero latitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(0.0), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isEquator())->toBeTrue();
});

test('is equator returns false for non zero latitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isEquator())->toBeFalse();
});

test('is prime meridian returns true for zero longitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(0.0));
    expect($coordinates->isPrimeMeridian())->toBeTrue();
});

test('is prime meridian returns false for non zero longitude', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates->isPrimeMeridian())->toBeFalse();
});

test('equals returns true for same coordinates', function (): void {
    $coordinates1 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $coordinates2 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));

    expect($coordinates1->isEqual($coordinates2))->toBeTrue();
});

test('equals returns false for different coordinates', function (): void {
    $coordinates1 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $coordinates2 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(51.5074), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-0.1278));

    expect($coordinates1->isEqual($coordinates2))->toBeFalse();
});

test('equals returns false for different type', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $latitude = new Latitude(40.7128);

    expect($coordinates->isEqual($latitude))->toBeFalse();
});

test('equals handles floating point precision', function (): void {
    $coordinates1 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    $coordinates2 = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128 + PHP_FLOAT_EPSILON), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060 + PHP_FLOAT_EPSILON));

    expect($coordinates1->isEqual($coordinates2))->toBeTrue();
});

test('to string returns string representation', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect((string) $coordinates)->toBe('40.7128,-74.006');
});

test('to string handles negative coordinates', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(-40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect((string) $coordinates)->toBe('-40.7128,-74.006');
});

test('value object interface compliance', function (): void {
    $coordinates = new Coordinates(new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128), new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060));
    expect($coordinates)->toBeInstanceOf(\JeroenGerits\Support\Contract\Equatable::class);
});
