<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Factories\CreateLatitude;

test('factory from creates latitude from float', function (): void {
    $latitude = CreateLatitude::from(40.7128);
    expect($latitude->value())->toBe(40.7128);
});

test('factory from creates latitude from string', function (): void {
    $latitude = CreateLatitude::from('40.7128');
    expect($latitude->value())->toBe(40.7128);
});

test('factory from creates latitude from array with latitude key', function (): void {
    $latitude = CreateLatitude::from(['latitude' => 40.7128]);
    expect($latitude->value())->toBe(40.7128);
});

test('factory from creates latitude from array with numeric index', function (): void {
    $latitude = CreateLatitude::from([40.7128]);
    expect($latitude->value())->toBe(40.7128);
});

test('factory from throws exception for invalid array', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude => CreateLatitude::from(['invalid' => 'data']))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException::class, 'Invalid latitude value provided');
});

test('factory from string creates latitude from valid string', function (): void {
    $latitude = CreateLatitude::fromString('40.7128');
    expect($latitude->value())->toBe(40.7128);
});

test('factory from string creates latitude from negative string', function (): void {
    $latitude = CreateLatitude::fromString('-40.7128');
    expect($latitude->value())->toBe(-40.7128);
});

test('factory from string throws exception for invalid string', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude => CreateLatitude::fromString('invalid'))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException::class, 'Invalid latitude string: invalid');
});

test('factory from string throws exception for empty string', function (): void {
    expect(fn (): \JeroenGerits\Support\Domain\Coordinates\ValueObjects\Latitude => CreateLatitude::fromString(''))
        ->toThrow(\JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException::class, 'Invalid latitude string: ');
});
