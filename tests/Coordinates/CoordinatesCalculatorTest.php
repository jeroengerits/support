<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

it('calculates distance between two coordinates in kilometers', function (): void {
    $coordinates1 = Coordinates::create(52.3676, 4.9041); // Amsterdam
    $coordinates2 = Coordinates::create(52.3736, 4.9101); // Close to Amsterdam
    $distance = $coordinates1->distanceTo($coordinates2);

    // Should be approximately 0.78 km
    expect($distance)->toBeGreaterThan(0.7)
        ->and($distance)->toBeLessThan(0.9);
});

it('calculates distance between two coordinates in miles', function (): void {
    $coordinates1 = Coordinates::create(52.3676, 4.9041); // Amsterdam
    $coordinates2 = Coordinates::create(52.3736, 4.9101); // Close to Amsterdam
    $distance = $coordinates1->distanceTo($coordinates2, DistanceUnit::MILES);

    // Should be approximately 0.48 miles
    expect($distance)->toBeGreaterThan(0.4)
        ->and($distance)->toBeLessThan(0.6);
});

it('returns zero distance for identical coordinates', function (): void {
    $coordinates1 = Coordinates::create(52.3676, 4.9041);
    $coordinates2 = Coordinates::create(52.3676, 4.9041);
    $distance = $coordinates1->distanceTo($coordinates2);

    expect($distance)->toBe(0.0);
});

it('calculates distance between distant cities', function (): void {
    $amsterdam = Coordinates::create(52.3676, 4.9041); // Amsterdam
    $london = Coordinates::create(51.5074, -0.1278);   // London
    $distance = $amsterdam->distanceTo($london);

    // Distance between Amsterdam and London is approximately 357 km
    expect($distance)->toBeGreaterThan(350)
        ->and($distance)->toBeLessThan(365);
});

it('calculates distance between antipodal points', function (): void {
    $point1 = Coordinates::create(0.0, 0.0);     // Equator, Prime Meridian
    $point2 = Coordinates::create(0.0, 180.0);   // Equator, International Date Line
    $distance = $point1->distanceTo($point2);

    // Half the Earth's circumference at the equator
    expect($distance)->toBeGreaterThan(20000)
        ->and($distance)->toBeLessThan(20050);
});

it('handles negative coordinates correctly', function (): void {
    $point1 = Coordinates::create(-33.9249, 18.4241); // Cape Town
    $point2 = Coordinates::create(-26.2041, 28.0473); // Johannesburg
    $distance = $point1->distanceTo($point2);

    // Distance between Cape Town and Johannesburg is approximately 1270 km
    expect($distance)->toBeGreaterThan(1260)
        ->and($distance)->toBeLessThan(1280);
});

it('calculates distance with very small differences', function (): void {
    $point1 = Coordinates::create(40.7128, -74.0060); // New York
    $point2 = Coordinates::create(40.7129, -74.0061); // Very close to New York
    $distance = $point1->distanceTo($point2);

    // Should be a very small distance (less than 100 meters)
    expect($distance)->toBeGreaterThan(0)
        ->and($distance)->toBeLessThan(0.1);
});

it('calculates batch distances for multiple coordinate pairs', function (): void {
    $coordinatePairs = [
        [Coordinates::create(52.3676, 4.9041), Coordinates::create(52.3736, 4.9101)], // Amsterdam area
        [Coordinates::create(51.5074, -0.1278), Coordinates::create(51.5074, -0.1278)], // London (identical)
        [Coordinates::create(40.7128, -74.0060), Coordinates::create(34.0522, -118.2437)], // NY to LA
    ];

    $distances = Coordinates::batchDistanceCalculation($coordinatePairs);

    expect($distances)->toHaveCount(3)
        ->and($distances[0])->toBeGreaterThan(0.7)->toBeLessThan(0.9) // Amsterdam area
        ->and($distances[1])->toBe(0.0) // Identical coordinates
        ->and($distances[2])->toBeGreaterThan(3900)->toBeLessThan(4100); // NY to LA
});

it('calculates batch distances with different units', function (): void {
    $coordinatePairs = [
        [Coordinates::create(52.3676, 4.9041), Coordinates::create(52.3736, 4.9101)],
    ];

    $distancesKm = Coordinates::batchDistanceCalculation($coordinatePairs, DistanceUnit::KILOMETERS);
    $distancesMiles = Coordinates::batchDistanceCalculation($coordinatePairs, DistanceUnit::MILES);

    expect($distancesKm[0])->toBeGreaterThan(0.7)->toBeLessThan(0.9)
        ->and($distancesMiles[0])->toBeGreaterThan(0.4)->toBeLessThan(0.6);
});

it('manages trigonometric cache correctly', function (): void {
    // Clear cache first
    Coordinates::clearCache();
    expect(Coordinates::getCacheSize())->toBe(0);

    // Perform some calculations to populate cache
    $point1 = Coordinates::create(40.7128, -74.0060);
    $point2 = Coordinates::create(51.5074, -0.1278);
    $point1->distanceTo($point2);

    // Cache should now have entries
    expect(Coordinates::getCacheSize())->toBeGreaterThan(0);

    // Clear cache again
    Coordinates::clearCache();
    expect(Coordinates::getCacheSize())->toBe(0);
});

it('supports different Earth models', function (): void {
    $point1 = Coordinates::create(40.7128, -74.0060); // New York
    $point2 = Coordinates::create(51.5074, -0.1278);  // London

    $distanceSpherical = $point1->distanceTo($point2, DistanceUnit::KILOMETERS, EarthModel::SPHERICAL);
    $distanceWgs84 = $point1->distanceTo($point2, DistanceUnit::KILOMETERS, EarthModel::WGS84);
    $distanceGrs80 = $point1->distanceTo($point2, DistanceUnit::KILOMETERS, EarthModel::GRS80);

    // All should be close but slightly different
    expect($distanceSpherical)->toBeGreaterThan(5500)->toBeLessThan(5600)
        ->and($distanceWgs84)->toBeGreaterThan(5500)->toBeLessThan(5600)
        ->and($distanceGrs80)->toBeGreaterThan(5500)->toBeLessThan(5600);
});
