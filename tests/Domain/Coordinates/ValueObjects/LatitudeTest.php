<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude;

test('can be instantiated with valid latitude', function (): void {
    $latitude = new Latitude(40.7128);
    expect($latitude->value())->toBe(40.7128);
});

test('can be instantiated with minimum valid latitude', function (): void {
    $latitude = new Latitude(-90.0);
    expect($latitude->value())->toBe(-90.0);
});

test('can be instantiated with maximum valid latitude', function (): void {
    $latitude = new Latitude(90.0);
    expect($latitude->value())->toBe(90.0);
});

test('can be instantiated with zero latitude', function (): void {
    $latitude = new Latitude(0.0);
    expect($latitude->value())->toBe(0.0);
});

test('throws exception for latitude below minimum', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude => new Latitude(-90.1))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException::class, 'Latitude must be between -90 and 90 degrees, got: -90.1');
});

test('throws exception for latitude above maximum', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude => new Latitude(90.1))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException::class, 'Latitude must be between -90 and 90 degrees, got: 90.1');
});

test('is northern returns true for positive latitude', function (): void {
    $latitude = new Latitude(40.7128);
    expect($latitude->isNorthern())->toBeTrue();
});

test('is northern returns false for negative latitude', function (): void {
    $latitude = new Latitude(-40.7128);
    expect($latitude->isNorthern())->toBeFalse();
});

test('is northern returns false for zero latitude', function (): void {
    $latitude = new Latitude(0.0);
    expect($latitude->isNorthern())->toBeFalse();
});

test('is southern returns true for negative latitude', function (): void {
    $latitude = new Latitude(-40.7128);
    expect($latitude->isSouthern())->toBeTrue();
});

test('is southern returns false for positive latitude', function (): void {
    $latitude = new Latitude(40.7128);
    expect($latitude->isSouthern())->toBeFalse();
});

test('is southern returns false for zero latitude', function (): void {
    $latitude = new Latitude(0.0);
    expect($latitude->isSouthern())->toBeFalse();
});

test('is equator returns true for zero latitude', function (): void {
    $latitude = new Latitude(0.0);
    expect($latitude->isEquator())->toBeTrue();
});

test('is equator returns false for non zero latitude', function (): void {
    $latitude = new Latitude(40.7128);
    expect($latitude->isEquator())->toBeFalse();
});

test('equals returns true for same latitude values', function (): void {
    $latitude1 = new Latitude(40.7128);
    $latitude2 = new Latitude(40.7128);

    expect($latitude1->isEqual($latitude2))->toBeTrue();
});

test('equals returns false for different latitude values', function (): void {
    $latitude1 = new Latitude(40.7128);
    $latitude2 = new Latitude(51.5074);

    expect($latitude1->isEqual($latitude2))->toBeFalse();
});

test('equals returns false for different type', function (): void {
    $latitude = new Latitude(40.7128);
    $longitude = new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude(-74.0060);

    expect($latitude->isEqual($longitude))->toBeFalse();
});

test('equals handles floating point precision', function (): void {
    $latitude1 = new Latitude(40.7128);
    $latitude2 = new Latitude(40.7128 + PHP_FLOAT_EPSILON);

    expect($latitude1->isEqual($latitude2))->toBeTrue();
});

test('to string returns string representation', function (): void {
    $latitude = new Latitude(40.7128);
    expect((string) $latitude)->toBe('40.7128');
});

test('to string returns negative string for negative latitude', function (): void {
    $latitude = new Latitude(-40.7128);
    expect((string) $latitude)->toBe('-40.7128');
});

test('value object interface compliance', function (): void {
    $latitude = new Latitude(40.7128);
    expect($latitude)->toBeInstanceOf(\JeroenGerits\Support\Contract\Equatable::class);
});
