<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Factories\CreateCoordinates;

test('factory from floats creates coordinates from float values', function (): void {
    $coordinates = CreateCoordinates::fromFloats(40.7128, -74.0060);

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from array creates coordinates from valid array', function (): void {
    $coordinates = CreateCoordinates::fromArray([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from array throws exception when latitude missing', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => CreateCoordinates::fromArray(['longitude' => -74.0060]))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException::class, 'Array must contain both latitude and longitude keys');
});

test('factory from array throws exception when longitude missing', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => CreateCoordinates::fromArray(['latitude' => 40.7128]))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException::class, 'Array must contain both latitude and longitude keys');
});

test('factory from string creates coordinates from valid string', function (): void {
    $coordinates = CreateCoordinates::fromString('40.7128,-74.0060');

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from string trims whitespace', function (): void {
    $coordinates = CreateCoordinates::fromString(' 40.7128 , -74.0060 ');

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from string throws exception for invalid format', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => CreateCoordinates::fromString('40.7128'))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException::class, 'String must contain exactly one comma separating latitude and longitude');
});

test('factory from string throws exception for multiple commas', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => CreateCoordinates::fromString('40.7128,-74.0060,extra'))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException::class, 'String must contain exactly one comma separating latitude and longitude');
});

test('factory from with single array parameter', function (): void {
    $coordinates = CreateCoordinates::from(['latitude' => 40.7128, 'longitude' => -74.0060]);

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from with single string parameter', function (): void {
    $coordinates = CreateCoordinates::from('40.7128,-74.0060');

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from with two numeric parameters', function (): void {
    $coordinates = CreateCoordinates::from(40.7128, -74.0060);

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from with latitude string and longitude numeric', function (): void {
    $coordinates = CreateCoordinates::from('40.7128', -74.0060);

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from with latitude numeric and longitude string', function (): void {
    $coordinates = CreateCoordinates::from(40.7128, '-74.0060');

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from with both string parameters', function (): void {
    $coordinates = CreateCoordinates::from('40.7128', '-74.0060');

    expect($coordinates->latitude()->value())->toBe(40.7128);
    expect($coordinates->longitude()->value())->toBe(-74.0060);
});

test('factory from throws exception for invalid parameters', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Coordinates => CreateCoordinates::from('invalid', 'data'))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException::class, 'Invalid latitude string: invalid');
});
