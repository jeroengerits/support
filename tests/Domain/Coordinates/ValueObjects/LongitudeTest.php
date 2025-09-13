<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude;

test('can be instantiated with valid longitude', function (): void {
    $longitude = new Longitude(-74.0060);
    expect($longitude->value())->toBe(-74.0060);
});

test('can be instantiated with minimum valid longitude', function (): void {
    $longitude = new Longitude(-180.0);
    expect($longitude->value())->toBe(-180.0);
});

test('can be instantiated with maximum valid longitude', function (): void {
    $longitude = new Longitude(180.0);
    expect($longitude->value())->toBe(180.0);
});

test('can be instantiated with zero longitude', function (): void {
    $longitude = new Longitude(0.0);
    expect($longitude->value())->toBe(0.0);
});

test('throws exception for longitude below minimum', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude => new Longitude(-180.1))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException::class, 'Longitude must be between -180 and 180 degrees, got: -180.1');
});

test('throws exception for longitude above maximum', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude => new Longitude(180.1))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException::class, 'Longitude must be between -180 and 180 degrees, got: 180.1');
});

test('is eastern returns true for positive longitude', function (): void {
    $longitude = new Longitude(74.0060);
    expect($longitude->isEastern())->toBeTrue();
});

test('is eastern returns false for negative longitude', function (): void {
    $longitude = new Longitude(-74.0060);
    expect($longitude->isEastern())->toBeFalse();
});

test('is eastern returns false for zero longitude', function (): void {
    $longitude = new Longitude(0.0);
    expect($longitude->isEastern())->toBeFalse();
});

test('is western returns true for negative longitude', function (): void {
    $longitude = new Longitude(-74.0060);
    expect($longitude->isWestern())->toBeTrue();
});

test('is western returns false for positive longitude', function (): void {
    $longitude = new Longitude(74.0060);
    expect($longitude->isWestern())->toBeFalse();
});

test('is western returns false for zero longitude', function (): void {
    $longitude = new Longitude(0.0);
    expect($longitude->isWestern())->toBeFalse();
});

test('is prime meridian returns true for zero longitude', function (): void {
    $longitude = new Longitude(0.0);
    expect($longitude->isPrimeMeridian())->toBeTrue();
});

test('is prime meridian returns false for non zero longitude', function (): void {
    $longitude = new Longitude(74.0060);
    expect($longitude->isPrimeMeridian())->toBeFalse();
});

test('is international date line returns true for 180 degrees', function (): void {
    $longitude = new Longitude(180.0);
    expect($longitude->isInternationalDateLine())->toBeTrue();
});

test('is international date line returns true for minus 180 degrees', function (): void {
    $longitude = new Longitude(-180.0);
    expect($longitude->isInternationalDateLine())->toBeTrue();
});

test('is international date line returns false for other longitudes', function (): void {
    $longitude = new Longitude(74.0060);
    expect($longitude->isInternationalDateLine())->toBeFalse();
});

test('equals returns true for same longitude values', function (): void {
    $longitude1 = new Longitude(-74.0060);
    $longitude2 = new Longitude(-74.0060);

    expect($longitude1->isEqual($longitude2))->toBeTrue();
});

test('equals returns false for different longitude values', function (): void {
    $longitude1 = new Longitude(-74.0060);
    $longitude2 = new Longitude(-0.1278);

    expect($longitude1->isEqual($longitude2))->toBeFalse();
});

test('equals returns false for different type', function (): void {
    $longitude = new Longitude(-74.0060);
    $latitude = new \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude(40.7128);

    expect($longitude->isEqual($latitude))->toBeFalse();
});

test('equals handles floating point precision', function (): void {
    $longitude1 = new Longitude(-74.0060);
    $longitude2 = new Longitude(-74.0060 + PHP_FLOAT_EPSILON);

    expect($longitude1->isEqual($longitude2))->toBeTrue();
});

test('to string returns string representation', function (): void {
    $longitude = new Longitude(-74.0060);
    expect((string) $longitude)->toBe('-74.006');
});

test('to string returns positive string for positive longitude', function (): void {
    $longitude = new Longitude(74.0060);
    expect((string) $longitude)->toBe('74.006');
});

test('value object interface compliance', function (): void {
    $longitude = new Longitude(-74.0060);
    expect($longitude)->toBeInstanceOf(\JeroenGerits\Support\Contract\Equatable::class);
});
