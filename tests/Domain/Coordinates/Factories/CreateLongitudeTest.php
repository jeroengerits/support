<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Factories\CreateLongitude;

test('factory from creates longitude from float', function (): void {
    $longitude = CreateLongitude::from(-74.0060);
    expect($longitude->value())->toBe(-74.0060);
});

test('factory from creates longitude from string', function (): void {
    $longitude = CreateLongitude::from('-74.0060');
    expect($longitude->value())->toBe(-74.0060);
});

test('factory from creates longitude from array with longitude key', function (): void {
    $longitude = CreateLongitude::from(['longitude' => -74.0060]);
    expect($longitude->value())->toBe(-74.0060);
});

test('factory from creates longitude from array with numeric index', function (): void {
    $longitude = CreateLongitude::from([-74.0060]);
    expect($longitude->value())->toBe(-74.0060);
});

test('factory from throws exception for invalid array', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude => CreateLongitude::from(['invalid' => 'data']))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException::class, 'Invalid longitude value provided');
});

test('factory from string creates longitude from valid string', function (): void {
    $longitude = CreateLongitude::fromString('-74.0060');
    expect($longitude->value())->toBe(-74.0060);
});

test('factory from string creates longitude from positive string', function (): void {
    $longitude = CreateLongitude::fromString('74.0060');
    expect($longitude->value())->toBe(74.0060);
});

test('factory from string throws exception for invalid string', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude => CreateLongitude::fromString('invalid'))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException::class, 'Invalid longitude string: invalid');
});

test('factory from string throws exception for empty string', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Longitude => CreateLongitude::fromString(''))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException::class, 'Invalid longitude string: ');
});
