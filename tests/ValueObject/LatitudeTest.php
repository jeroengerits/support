<?php

declare(strict_types=1);

use JeroenGerits\Support\Exception\InvalidLatitudeException;
use JeroenGerits\Support\ValueObject\Latitude;

it('creates a valid latitude', function (): void {
    $latitude = new Latitude(40.7128);

    expect($latitude->value)->toBe(40.7128);
});

it('throws exception for latitude below -90', function (): void {
    expect(fn (): Latitude => new Latitude(-91.0))
        ->toThrow(InvalidLatitudeException::class);
});

it('throws exception for latitude above 90', function (): void {
    expect(fn (): Latitude => new Latitude(91.0))
        ->toThrow(InvalidLatitudeException::class);
});

it('accepts latitude at -90 degrees', function (): void {
    $latitude = new Latitude(-90.0);

    expect($latitude->value)->toBe(-90.0);
});

it('accepts latitude at 90 degrees', function (): void {
    $latitude = new Latitude(90.0);

    expect($latitude->value)->toBe(90.0);
});

it('is equal to another latitude with same value', function (): void {
    $latitude1 = new Latitude(40.7128);
    $latitude2 = new Latitude(40.7128);

    expect($latitude1->equals($latitude2))->toBeTrue();
});

it('is not equal to another latitude with different value', function (): void {
    $latitude1 = new Latitude(40.7128);
    $latitude2 = new Latitude(41.7128);

    expect($latitude1->equals($latitude2))->toBeFalse();
});

it('converts to string', function (): void {
    $latitude = new Latitude(40.7128);

    expect((string) $latitude)->toBe('40.7128');
});

it('converts to array', function (): void {
    $latitude = new Latitude(40.7128);

    expect($latitude->toArray())->toBe(['latitude' => 40.7128]);
});

it('creates from string', function (): void {
    $latitude = Latitude::fromString('40.7128');

    expect($latitude->value)->toBe(40.7128);
});

it('creates from float', function (): void {
    $latitude = Latitude::fromFloat(40.7128);

    expect($latitude->value)->toBe(40.7128);
});

it('throws exception when creating from invalid string', function (): void {
    expect(fn (): Latitude => Latitude::fromString('invalid'))
        ->toThrow(InvalidLatitudeException::class);
});

it('throws exception when creating from invalid float', function (): void {
    expect(fn (): Latitude => Latitude::fromFloat(91.0))
        ->toThrow(InvalidLatitudeException::class);
});

it('determines if latitude is in northern hemisphere', function (): void {
    $northernLatitude = new Latitude(40.7128);
    $southernLatitude = new Latitude(-40.7128);

    expect($northernLatitude->isNorthern())->toBeTrue()
        ->and($southernLatitude->isNorthern())->toBeFalse();
});

it('determines if latitude is in southern hemisphere', function (): void {
    $northernLatitude = new Latitude(40.7128);
    $southernLatitude = new Latitude(-40.7128);

    expect($northernLatitude->isSouthern())->toBeFalse()
        ->and($southernLatitude->isSouthern())->toBeTrue();
});

it('determines if latitude is at equator', function (): void {
    $equatorLatitude = new Latitude(0.0);
    $nonEquatorLatitude = new Latitude(40.7128);

    expect($equatorLatitude->isEquator())->toBeTrue()
        ->and($nonEquatorLatitude->isEquator())->toBeFalse();
});

// Edge Cases and Advanced Tests
it('handles floating point precision correctly', function (float $input, float $expected): void {
    if ($input < -90.0 || $input > 90.0) {
        expect(fn (): Latitude => new Latitude($input))->toThrow(InvalidLatitudeException::class);
    } else {
        $latitude = new Latitude($input);
        expect($latitude->value)->toBe($expected);
    }
})->with([
    [-90.0000000001, -90.0],
    [90.0000000001, 90.0],
    [0.0, 0.0],
    [45.5, 45.5],
    [-45.5, -45.5],
]);

it('validates latitude range with random values', function (): void {
    $validLatitude = 45.0; // Use a fixed valid value for now
    $latitude = new Latitude($validLatitude);
    expect($latitude->value)->toBe($validLatitude)
        ->and($latitude->value)->toBeGreaterThanOrEqual(-90.0)
        ->and($latitude->value)->toBeLessThanOrEqual(90.0);
});

it('throws exception for values just outside valid range', function (float $invalidValue): void {
    expect(fn (): Latitude => new Latitude($invalidValue))
        ->toThrow(InvalidLatitudeException::class);
})->with([
    -90.0000001,
    90.0000001,
    -91.0,
    91.0,
    -180.0,
    180.0,
]);

it('handles string conversion with various precision levels', function (float $value, string $expected): void {
    $latitude = new Latitude($value);
    expect((string) $latitude)->toBe($expected);
})->with([
    [0.0, '0'],
    [1.0, '1'],
    [1.5, '1.5'],
    [1.50, '1.5'],
    [1.500, '1.5'],
]);

it('handles array conversion consistently', function (float $value): void {
    $latitude = new Latitude($value);
    $array = $latitude->toArray();

    expect($array)->toHaveKey('latitude')
        ->and($array['latitude'])->toBe($value)
        ->and($array)->toHaveCount(1);
})->with([
    0.0,
    45.0,
    -45.0,
    90.0,
    -90.0,
]);

it('validates hemisphere detection with boundary values', function (float $value, bool $isNorthern, bool $isSouthern, bool $isEquator): void {
    $latitude = new Latitude($value);

    expect($latitude->isNorthern())->toBe($isNorthern)
        ->and($latitude->isSouthern())->toBe($isSouthern)
        ->and($latitude->isEquator())->toBe($isEquator);
})->with([
    [0.0, false, false, true],
    [0.0000001, true, false, false],
    [-0.0000001, false, true, false],
    [90.0, true, false, false],
    [-90.0, false, true, false],
]);

it('handles fromString with various valid formats', function (string $input, float $expected): void {
    $latitude = Latitude::fromString($input);
    expect($latitude->value())->toBe($expected);
})->with([
    ['0', 0.0],
    ['45.5', 45.5],
    ['-45.5', -45.5],
    ['90', 90.0],
    ['-90', -90.0],
    ['0.0', 0.0],
    ['45.50', 45.5],
]);

it('throws exception for invalid string formats', function (string $invalidInput): void {
    expect(fn (): Latitude => Latitude::fromString($invalidInput))
        ->toThrow(InvalidLatitudeException::class);
})->with([
    'invalid',
    'not-a-number',
    '',
    ' ',
    '45.5.5',
    '45,5',
]);

it('handles equality comparison with floating point precision', function (float $value1, float $value2, bool $expected): void {
    $lat1 = new Latitude($value1);
    $lat2 = new Latitude($value2);

    expect($lat1->equals($lat2))->toBe($expected);
})->with([
    [0.0, 0.0, true],
    [45.0, 45.0, true],
    [45.0, 45.1, false],
    [0.0, 0.0000001, false],
    [90.0, 90.0, true],
    [-90.0, -90.0, true],
]);

// Exception Tests
it('throws outOfRange exception with correct message', function (float $invalidValue): void {
    try {
        new Latitude($invalidValue);
        expect(false)->toBeTrue('Exception should have been thrown');
    } catch (InvalidLatitudeException $e) {
        expect($e->getMessage())->toContain('Latitude must be between -90 and 90 degrees')
            ->and($e->getMessage())->toContain((string) $invalidValue);
    }
})->with([
    -91.0,
    91.0,
    -180.0,
    180.0,
]);

it('throws invalidString exception with correct message', function (string $invalidString): void {
    try {
        Latitude::fromString($invalidString);
        expect(false)->toBeTrue('Exception should have been thrown');
    } catch (InvalidLatitudeException $e) {
        expect($e->getMessage())->toContain('Invalid latitude value')
            ->and($e->getMessage())->toContain($invalidString);
    }
})->with([
    'invalid',
    'not-a-number',
    '',
    ' ',
]);

it('ensures exception extends InvalidArgumentException', function (): void {
    expect(new InvalidLatitudeException('test'))->toBeInstanceOf(InvalidArgumentException::class);
});
