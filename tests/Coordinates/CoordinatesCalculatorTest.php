<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;

it('calculates distance between two coordinates in kilometers', function (): void {
    $coordinates1 = coordinates(52.3676, 4.9041); // Amsterdam
    $coordinates2 = coordinates(52.3736, 4.9101); // Close to Amsterdam
    $distance = CoordinatesCalculator::calculateDistance($coordinates1, $coordinates2);

    // Should be approximately 0.78 km
    expect($distance)->toBeGreaterThan(0.7)
        ->and($distance)->toBeLessThan(0.9);
});

it('calculates distance between two coordinates in miles', function (): void {
    $coordinates1 = coordinates(52.3676, 4.9041); // Amsterdam
    $coordinates2 = coordinates(52.3736, 4.9101); // Close to Amsterdam
    $distance = CoordinatesCalculator::calculateDistance($coordinates1, $coordinates2, DistanceUnit::MILES);

    // Should be approximately 0.48 miles
    expect($distance)->toBeGreaterThan(0.4)
        ->and($distance)->toBeLessThan(0.6);
});

it('returns zero distance for identical coordinates', function (): void {
    $coordinates1 = coordinates(52.3676, 4.9041);
    $coordinates2 = coordinates(52.3676, 4.9041);
    $distance = CoordinatesCalculator::calculateDistance($coordinates1, $coordinates2);

    expect($distance)->toBe(0.0);
});

it('calculates distance between distant cities', function (): void {
    $amsterdam = coordinates(52.3676, 4.9041); // Amsterdam
    $london = coordinates(51.5074, -0.1278);   // London
    $distance = CoordinatesCalculator::calculateDistance($amsterdam, $london);

    // Distance between Amsterdam and London is approximately 357 km
    expect($distance)->toBeGreaterThan(350)
        ->and($distance)->toBeLessThan(365);
});

it('calculates distance between antipodal points', function (): void {
    $point1 = coordinates(0, 0);     // Equator, Prime Meridian
    $point2 = coordinates(0, 180);   // Equator, International Date Line
    $distance = CoordinatesCalculator::calculateDistance($point1, $point2);

    // Half the Earth's circumference at the equator
    expect($distance)->toBeGreaterThan(20000)
        ->and($distance)->toBeLessThan(20050);
});

it('handles negative coordinates correctly', function (): void {
    $point1 = coordinates(-33.9249, 18.4241); // Cape Town
    $point2 = coordinates(-26.2041, 28.0473); // Johannesburg
    $distance = CoordinatesCalculator::calculateDistance($point1, $point2);

    // Distance between Cape Town and Johannesburg is approximately 1270 km
    expect($distance)->toBeGreaterThan(1260)
        ->and($distance)->toBeLessThan(1280);
});

it('calculates distance with very small differences', function (): void {
    $point1 = coordinates(40.7128, -74.0060); // New York
    $point2 = coordinates(40.7129, -74.0061); // Very close to New York
    $distance = CoordinatesCalculator::calculateDistance($point1, $point2);

    // Should be a very small distance (less than 100 meters)
    expect($distance)->toBeGreaterThan(0)
        ->and($distance)->toBeLessThan(0.1);
});
