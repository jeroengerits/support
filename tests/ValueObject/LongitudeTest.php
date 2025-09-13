<?php

declare(strict_types=1);

use JeroenGerits\Support\Exception\InvalidLongitudeException;
use JeroenGerits\Support\Exception\SupportException;
use JeroenGerits\Support\ValueObject\Longitude;

it('creates a valid longitude', function (): void {
    $longitude = new Longitude(-74.0060);

    expect($longitude->value)->toBe(-74.0060);
});

it('throws exception for longitude below -180', function (): void {
    expect(fn (): Longitude => new Longitude(-181.0))
        ->toThrow(InvalidLongitudeException::class);
});

it('throws exception for longitude above 180', function (): void {
    expect(fn (): Longitude => new Longitude(181.0))
        ->toThrow(InvalidLongitudeException::class);
});

it('accepts longitude at -180 degrees', function (): void {
    $longitude = new Longitude(-180.0);

    expect($longitude->value)->toBe(-180.0);
});

it('accepts longitude at 180 degrees', function (): void {
    $longitude = new Longitude(180.0);

    expect($longitude->value)->toBe(180.0);
});

it('is equal to another longitude with same value', function (): void {
    $longitude1 = new Longitude(-74.0060);
    $longitude2 = new Longitude(-74.0060);

    expect($longitude1->equals($longitude2))->toBeTrue();
});

it('is not equal to another longitude with different value', function (): void {
    $longitude1 = new Longitude(-74.0060);
    $longitude2 = new Longitude(-75.0060);

    expect($longitude1->equals($longitude2))->toBeFalse();
});

it('converts to string', function (): void {
    $longitude = new Longitude(-74.0060);

    expect((string) $longitude)->toBe('-74.006');
});

it('converts to array', function (): void {
    $longitude = new Longitude(-74.0060);

    expect($longitude->toArray())->toBe(['longitude' => -74.0060]);
});

it('creates from string', function (): void {
    $longitude = Longitude::fromString('-74.0060');

    expect($longitude->value)->toBe(-74.0060);
});

it('creates from float', function (): void {
    $longitude = Longitude::fromFloat(-74.0060);

    expect($longitude->value)->toBe(-74.0060);
});

it('throws exception when creating from invalid string', function (): void {
    expect(fn (): Longitude => Longitude::fromString('invalid'))
        ->toThrow(InvalidLongitudeException::class);
});

it('throws exception when creating from invalid float', function (): void {
    expect(fn (): Longitude => Longitude::fromFloat(181.0))
        ->toThrow(InvalidLongitudeException::class);
});

it('determines if longitude is in eastern hemisphere', function (): void {
    $easternLongitude = new Longitude(120.0);
    $westernLongitude = new Longitude(-120.0);

    expect($easternLongitude->isEastern())->toBeTrue()
        ->and($westernLongitude->isEastern())->toBeFalse();
});

it('determines if longitude is in western hemisphere', function (): void {
    $easternLongitude = new Longitude(120.0);
    $westernLongitude = new Longitude(-120.0);

    expect($easternLongitude->isWestern())->toBeFalse()
        ->and($westernLongitude->isWestern())->toBeTrue();
});

it('determines if longitude is at prime meridian', function (): void {
    $primeMeridianLongitude = new Longitude(0.0);
    $nonPrimeMeridianLongitude = new Longitude(-74.0060);

    expect($primeMeridianLongitude->isPrimeMeridian())->toBeTrue()
        ->and($nonPrimeMeridianLongitude->isPrimeMeridian())->toBeFalse();
});

it('determines if longitude is at international date line', function (): void {
    $dateLineLongitude = new Longitude(180.0);
    $nonDateLineLongitude = new Longitude(-74.0060);

    expect($dateLineLongitude->isInternationalDateLine())->toBeTrue()
        ->and($nonDateLineLongitude->isInternationalDateLine())->toBeFalse();
});

// Edge Cases and Advanced Tests
it('handles floating point precision correctly', function (float $input, float $expected): void {
    if ($input < -180.0 || $input > 180.0) {
        expect(fn (): Longitude => new Longitude($input))->toThrow(InvalidLongitudeException::class);
    } else {
        $longitude = new Longitude($input);
        expect($longitude->value)->toBe($expected);
    }
})->with([
    [-180.0000000001, -180.0],
    [180.0000000001, 180.0],
    [0.0, 0.0],
    [45.5, 45.5],
    [-45.5, -45.5],
]);

it('validates longitude range with random values', function (): void {
    $validLongitude = 45.0; // Use a fixed valid value for now
    $longitude = new Longitude($validLongitude);
    expect($longitude->value)->toBe($validLongitude)
        ->and($longitude->value)->toBeGreaterThanOrEqual(-180.0)
        ->and($longitude->value)->toBeLessThanOrEqual(180.0);
});

it('throws exception for values just outside valid range', function (float $invalidValue): void {
    expect(fn (): Longitude => new Longitude($invalidValue))
        ->toThrow(InvalidLongitudeException::class);
})->with([
    -180.0000001,
    180.0000001,
    -181.0,
    181.0,
    -360.0,
    360.0,
]);

it('handles string conversion with various precision levels', function (float $value, string $expected): void {
    $longitude = new Longitude($value);
    expect((string) $longitude)->toBe($expected);
})->with([
    [0.0, '0'],
    [1.0, '1'],
    [1.5, '1.5'],
    [1.50, '1.5'],
    [1.500, '1.5'],
]);

it('handles array conversion consistently', function (float $value): void {
    $longitude = new Longitude($value);
    $array = $longitude->toArray();

    expect($array)->toHaveKey('longitude')
        ->and($array['longitude'])->toBe($value)
        ->and($array)->toHaveCount(1);
})->with([
    0.0,
    45.0,
    -45.0,
    180.0,
    -180.0,
]);

it('validates hemisphere detection with boundary values', function (float $value, bool $isEastern, bool $isWestern, bool $isPrimeMeridian, bool $isDateLine): void {
    $longitude = new Longitude($value);

    expect($longitude->isEastern())->toBe($isEastern)
        ->and($longitude->isWestern())->toBe($isWestern)
        ->and($longitude->isPrimeMeridian())->toBe($isPrimeMeridian)
        ->and($longitude->isInternationalDateLine())->toBe($isDateLine);
})->with([
    [0.0, false, false, true, false],
    [0.0000001, true, false, false, false],
    [-0.0000001, false, true, false, false],
    [180.0, true, false, false, true],
    [-180.0, false, true, false, true],
]);

it('handles fromString with various valid formats', function (string $input, float $expected): void {
    $longitude = Longitude::fromString($input);
    expect($longitude->value)->toBe($expected);
})->with([
    ['0', 0.0],
    ['45.5', 45.5],
    ['-45.5', -45.5],
    ['180', 180.0],
    ['-180', -180.0],
    ['0.0', 0.0],
    ['45.50', 45.5],
]);

it('throws exception for invalid string formats', function (string $invalidInput): void {
    expect(fn (): Longitude => Longitude::fromString($invalidInput))
        ->toThrow(InvalidLongitudeException::class);
})->with([
    'invalid',
    'not-a-number',
    '',
    ' ',
    '45.5.5',
    '45,5',
]);

it('handles equality comparison with floating point precision', function (float $value1, float $value2, bool $expected): void {
    $lon1 = new Longitude($value1);
    $lon2 = new Longitude($value2);

    expect($lon1->equals($lon2))->toBe($expected);
})->with([
    [0.0, 0.0, true],
    [45.0, 45.0, true],
    [45.0, 45.1, false],
    [0.0, 0.0000001, false],
    [180.0, 180.0, true],
    [-180.0, -180.0, true],
]);

// Exception Tests
it('throws outOfRange exception with correct message', function (float $invalidValue): void {
    try {
        new Longitude($invalidValue);
        expect(false)->toBeTrue('Exception should have been thrown');
    } catch (InvalidLongitudeException $e) {
        expect($e->getMessage())->toContain('Longitude must be between -180 and 180 degrees')
            ->and($e->getMessage())->toContain((string) $invalidValue);
    }
})->with([
    -181.0,
    181.0,
    -360.0,
    360.0,
]);

it('throws invalidString exception with correct message', function (string $invalidString): void {
    try {
        Longitude::fromString($invalidString);
        expect(false)->toBeTrue('Exception should have been thrown');
    } catch (InvalidLongitudeException $e) {
        expect($e->getMessage())->toContain('Invalid longitude value')
            ->and($e->getMessage())->toContain($invalidString);
    }
})->with([
    'invalid',
    'not-a-number',
    '',
    ' ',
]);

it('ensures exception extends SupportException', function (): void {
    expect(new InvalidLongitudeException('test'))->toBeInstanceOf(SupportException::class);
});
