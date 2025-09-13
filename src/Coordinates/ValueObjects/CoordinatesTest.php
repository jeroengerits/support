<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

it('creates coordinates with latitude and longitude', function (): void {
    $latitude = new Latitude(40.7128);
    $longitude = new Longitude(-74.0060);
    $coordinates = new Coordinates($latitude, $longitude);

    expect($coordinates->latitude())->toBe($latitude)
        ->and($coordinates->longitude())->toBe($longitude);
});

it('creates coordinates from float values', function (): void {
    $coordinates = Coordinates::fromFloats(40.7128, -74.0060);

    expect($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('creates coordinates from array', function (): void {
    $coordinates = Coordinates::fromArray([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);

    expect($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('throws exception when creating from array with missing latitude', function (): void {
    expect(fn (): Coordinates => Coordinates::fromArray(['longitude' => -74.0060]))
        ->toThrow(InvalidArgumentException::class, 'Array must contain both latitude and longitude keys');
});

it('throws exception when creating from array with missing longitude', function (): void {
    expect(fn (): Coordinates => Coordinates::fromArray(['latitude' => 40.7128]))
        ->toThrow(InvalidArgumentException::class, 'Array must contain both latitude and longitude keys');
});

it('creates coordinates from string', function (): void {
    $coordinates = Coordinates::fromString('40.7128,-74.0060');

    expect($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('creates coordinates from string with space after comma', function (): void {
    $coordinates = Coordinates::fromString('40.7128, -74.0060');

    expect($coordinates->latitude()->value)->toBe(40.7128)
        ->and($coordinates->longitude()->value)->toBe(-74.0060);
});

it('throws exception when creating from invalid string format', function (): void {
    expect(fn (): Coordinates => Coordinates::fromString('40.7128'))
        ->toThrow(InvalidArgumentException::class, 'Invalid coordinates format. Expected "latitude,longitude"');
});

it('throws exception when creating from string with invalid latitude', function (): void {
    expect(fn (): Coordinates => Coordinates::fromString('91.0,-74.0060'))
        ->toThrow(InvalidArgumentException::class, 'Latitude must be between -90 and 90 degrees');
});

it('throws exception when creating from string with invalid longitude', function (): void {
    expect(fn (): Coordinates => Coordinates::fromString('40.7128,181.0'))
        ->toThrow(InvalidArgumentException::class, 'Longitude must be between -180 and 180 degrees');
});

it('is equal to another coordinates with same values', function (): void {
    $coordinates1 = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));
    $coordinates2 = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect($coordinates1->equals($coordinates2))->toBeTrue();
});

it('is not equal to another coordinates with different latitude', function (): void {
    $coordinates1 = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));
    $coordinates2 = new Coordinates(new Latitude(41.7128), new Longitude(-74.0060));

    expect($coordinates1->equals($coordinates2))->toBeFalse();
});

it('is not equal to another coordinates with different longitude', function (): void {
    $coordinates1 = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));
    $coordinates2 = new Coordinates(new Latitude(40.7128), new Longitude(-75.0060));

    expect($coordinates1->equals($coordinates2))->toBeFalse();
});

it('converts to string', function (): void {
    $coordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect((string) $coordinates)->toBe('40.7128,-74.006');
});

it('converts to array', function (): void {
    $coordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect($coordinates->toArray())->toBe([
        'latitude' => 40.7128,
        'longitude' => -74.0060,
    ]);
});

it('determines if coordinates are in northern hemisphere', function (): void {
    $northernCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));
    $southernCoordinates = new Coordinates(new Latitude(-40.7128), new Longitude(-74.0060));

    expect($northernCoordinates->isNorthern())->toBeTrue()
        ->and($southernCoordinates->isNorthern())->toBeFalse();
});

it('determines if coordinates are in southern hemisphere', function (): void {
    $northernCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));
    $southernCoordinates = new Coordinates(new Latitude(-40.7128), new Longitude(-74.0060));

    expect($northernCoordinates->isSouthern())->toBeFalse()
        ->and($southernCoordinates->isSouthern())->toBeTrue();
});

it('determines if coordinates are in eastern hemisphere', function (): void {
    $easternCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(120.0));
    $westernCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-120.0));

    expect($easternCoordinates->isEastern())->toBeTrue()
        ->and($westernCoordinates->isEastern())->toBeFalse();
});

it('determines if coordinates are in western hemisphere', function (): void {
    $easternCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(120.0));
    $westernCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-120.0));

    expect($easternCoordinates->isWestern())->toBeFalse()
        ->and($westernCoordinates->isWestern())->toBeTrue();
});

it('determines if coordinates are at equator', function (): void {
    $equatorCoordinates = new Coordinates(new Latitude(0.0), new Longitude(-74.0060));
    $nonEquatorCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect($equatorCoordinates->isEquator())->toBeTrue()
        ->and($nonEquatorCoordinates->isEquator())->toBeFalse();
});

it('determines if coordinates are at prime meridian', function (): void {
    $primeMeridianCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(0.0));
    $nonPrimeMeridianCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect($primeMeridianCoordinates->isPrimeMeridian())->toBeTrue()
        ->and($nonPrimeMeridianCoordinates->isPrimeMeridian())->toBeFalse();
});

it('determines if coordinates are at international date line', function (): void {
    $dateLineCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(180.0));
    $nonDateLineCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect($dateLineCoordinates->isInternationalDateLine())->toBeTrue()
        ->and($nonDateLineCoordinates->isInternationalDateLine())->toBeFalse();
});

it('determines if coordinates are at Greenwich meridian', function (): void {
    $greenwichCoordinates = new Coordinates(new Latitude(51.4769), new Longitude(0.0));
    $nonGreenwichCoordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect($greenwichCoordinates->isGreenwichMeridian())->toBeTrue()
        ->and($nonGreenwichCoordinates->isGreenwichMeridian())->toBeFalse();
});

it('calculates distance to another coordinates', function (): void {
    $coordinates1 = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060)); // New York
    $coordinates2 = new Coordinates(new Latitude(51.5074), new Longitude(-0.1278)); // London

    $distance = $coordinates1->distanceTo($coordinates2);

    expect($distance)->toBeGreaterThan(5500)
        ->and($distance)->toBeLessThan(5600); // Approximately 5570 km
});

it('calculates distance to same coordinates as zero', function (): void {
    $coordinates = new Coordinates(new Latitude(40.7128), new Longitude(-74.0060));

    expect($coordinates->distanceTo($coordinates))->toBe(0.0);
});

// Distance Calculation Tests
it('calculates distance between major cities correctly', function (): void {
    $newYork = Coordinates::fromFloats(40.7128, -74.0060);
    $london = Coordinates::fromFloats(51.5074, -0.1278);
    $tokyo = Coordinates::fromFloats(35.6762, 139.6503);
    $sydney = Coordinates::fromFloats(-33.8688, 151.2093);

    $nyToLondon = $newYork->distanceTo($london);
    $nyToTokyo = $newYork->distanceTo($tokyo);
    $nyToSydney = $newYork->distanceTo($sydney);

    expect($nyToLondon)->toBeGreaterThan(5500)->toBeLessThan(5600)
        ->and($nyToTokyo)->toBeGreaterThan(10800)->toBeLessThan(10900)
        ->and($nyToSydney)->toBeGreaterThan(15900)->toBeLessThan(16000);
});

it('calculates distance in different units', function (): void {
    $newYork = Coordinates::fromFloats(40.7128, -74.0060);
    $london = Coordinates::fromFloats(51.5074, -0.1278);

    $distanceKm = $newYork->distanceTo($london, DistanceUnit::KILOMETERS);
    $distanceMiles = $newYork->distanceTo($london, DistanceUnit::MILES);
    $distanceNautical = $newYork->distanceTo($london, DistanceUnit::NAUTICAL_MILES);
    $distanceMeters = $newYork->distanceTo($london, DistanceUnit::METERS);

    expect($distanceKm)->toBeGreaterThan(5500)->toBeLessThan(5600)
        ->and($distanceMiles)->toBeGreaterThan(3400)->toBeLessThan(3500)
        ->and($distanceNautical)->toBeGreaterThan(3000)->toBeLessThan(3100)
        ->and($distanceMeters)->toBeGreaterThan(5500000)->toBeLessThan(5600000);
});

it('calculates distance efficiently', function (): void {
    $start = microtime(true);

    $coordinates1 = Coordinates::fromFloats(40.7128, -74.0060);
    $coordinates2 = Coordinates::fromFloats(51.5074, -0.1278);

    for ($i = 0; $i < 1000; $i++) {
        $coordinates1->distanceTo($coordinates2);
    }

    $end = microtime(true);
    expect($end - $start)->toBeLessThan(0.1); // Should complete in under 100ms
});

it('handles antipodal points correctly', function (): void {
    $point1 = Coordinates::fromFloats(0.0, 0.0);
    $point2 = Coordinates::fromFloats(0.0, 180.0);

    $distance = $point1->distanceTo($point2);

    // Distance should be approximately half the Earth's circumference
    expect($distance)->toBeGreaterThan(20000)->toBeLessThan(20050);
});

it('handles coordinates near the poles', function (): void {
    $northPole = Coordinates::fromFloats(90.0, 0.0);
    $southPole = Coordinates::fromFloats(-90.0, 0.0);
    $equator = Coordinates::fromFloats(0.0, 0.0);

    $poleToPole = $northPole->distanceTo($southPole);
    $poleToEquator = $northPole->distanceTo($equator);

    // Distance from pole to pole should be approximately half the Earth's circumference
    expect($poleToPole)->toBeGreaterThan(20000)->toBeLessThan(20050)
        ->and($poleToEquator)->toBeGreaterThan(10000)->toBeLessThan(10025);

    // Distance from pole to equator should be approximately quarter the Earth's circumference
});

it('validates distance unit conversion factors', function (): void {
    $unit = DistanceUnit::MILES;
    $factor = $unit->getConversionFactor();

    expect($factor)->toBeGreaterThan(0.6)->toBeLessThan(0.7)
        ->and($unit->getDisplayName())->toBe('miles')
        ->and($unit->getAbbreviation())->toBe('mi');
});

it('handles very short distances accurately', function (): void {
    $point1 = Coordinates::fromFloats(40.7128, -74.0060);
    $point2 = Coordinates::fromFloats(40.7129, -74.0061);

    $distance = $point1->distanceTo($point2);

    // Should be a very short distance (a few hundred meters)
    expect($distance)->toBeGreaterThan(0.01)->toBeLessThan(1.0);
});

// Exception Tests
it('throws missingArrayKeys exception with correct message', function (array $invalidArray): void {
    try {
        Coordinates::fromArray($invalidArray);
        expect(false)->toBeTrue('Exception should have been thrown');
    } catch (InvalidCoordinatesException $e) {
        expect($e->getMessage())->toContain('Array must contain both latitude and longitude keys');
    }
})->with([
    [['longitude' => -74.0060]],
    [['latitude' => 40.7128]],
    [[]],
    [['lat' => 40.7128, 'lon' => -74.0060]],
]);

it('throws invalidStringFormat exception with correct message', function (string $invalidString): void {
    try {
        Coordinates::fromString($invalidString);
        expect(false)->toBeTrue('Exception should have been thrown');
    } catch (InvalidCoordinatesException $e) {
        expect($e->getMessage())->toContain('Invalid coordinates format')
            ->and($e->getMessage())->toContain('Expected "latitude,longitude"');
    }
})->with([
    '40.7128',
    '40.7128,',
    ',40.7128',
    '40.7128,-74.0060,extra',
    '',
    ' ',
]);

it('ensures exception extends InvalidArgumentException', function (): void {
    expect(new InvalidCoordinatesException('test'))->toBeInstanceOf(InvalidArgumentException::class);
});
