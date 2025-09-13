<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates;

test('coordinates helper function exists', function (): void {
    expect(function_exists('coordinates'))->toBeTrue();
});

test('coordinates helper creates coordinates from two numeric parameters', function (): void {
    $coordinates = coordinates(40.7128, -74.0060);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(40.7128)
        ->and($coordinates->longitude()->value())->toBe(-74.0060);
});

test('coordinates helper creates coordinates from single array parameter', function (): void {
    $coordinates = coordinates(['latitude' => 40.7128, 'longitude' => -74.0060]);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(40.7128)
        ->and($coordinates->longitude()->value())->toBe(-74.0060);
});

test('coordinates helper creates coordinates from single string parameter', function (): void {
    $coordinates = coordinates('40.7128,-74.0060');

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(40.7128)
        ->and($coordinates->longitude()->value())->toBe(-74.0060);
});

test('coordinates helper creates coordinates from latitude string and longitude numeric', function (): void {
    $coordinates = coordinates('40.7128', -74.0060);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(40.7128)
        ->and($coordinates->longitude()->value())->toBe(-74.0060);
});

test('coordinates helper creates coordinates from latitude numeric and longitude string', function (): void {
    $coordinates = coordinates(40.7128, '-74.0060');

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(40.7128)
        ->and($coordinates->longitude()->value())->toBe(-74.0060);
});

test('coordinates helper creates coordinates from both string parameters', function (): void {
    $coordinates = coordinates('40.7128', '-74.0060');

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(40.7128)
        ->and($coordinates->longitude()->value())->toBe(-74.0060);
});

test('coordinates helper throws exception for invalid latitude string', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => coordinates('invalid', -74.0060))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException::class, 'Invalid latitude string: invalid');
});

test('coordinates helper throws exception for invalid longitude string', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => coordinates(40.7128, 'invalid'))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException::class, 'Invalid longitude string: invalid');
});

test('coordinates helper throws exception for invalid string format', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => coordinates('40.7128'))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException::class, 'String must contain exactly one comma separating latitude and longitude');
});

test('coordinates helper throws exception for array missing latitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => coordinates(['longitude' => -74.0060]))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException::class, 'Array must contain both latitude and longitude keys');
});

test('coordinates helper throws exception for array missing longitude', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => coordinates(['latitude' => 40.7128]))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException::class, 'Array must contain both latitude and longitude keys');
});

test('coordinates helper handles negative coordinates', function (): void {
    $coordinates = coordinates(-40.7128, -74.0060);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(-40.7128)
        ->and($coordinates->longitude()->value())->toBe(-74.0060);
});

test('coordinates helper handles zero coordinates', function (): void {
    $coordinates = coordinates(0.0, 0.0);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(0.0)
        ->and($coordinates->longitude()->value())->toBe(0.0);
});

test('coordinates helper handles integer parameters', function (): void {
    $coordinates = coordinates(40, -74);

    expect($coordinates)->toBeInstanceOf(Coordinates::class)
        ->and($coordinates->latitude()->value())->toBe(40.0)
        ->and($coordinates->longitude()->value())->toBe(-74.0);
});
