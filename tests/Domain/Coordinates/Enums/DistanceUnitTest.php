<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Enums\DistanceUnit;

test('kilometers case exists', function (): void {
    $unit = DistanceUnit::KILOMETERS;
    expect($unit->value)->toBe('km');
});

test('miles case exists', function (): void {
    $unit = DistanceUnit::MILES;
    expect($unit->value)->toBe('mi');
});

test('meters case exists', function (): void {
    $unit = DistanceUnit::METERS;
    expect($unit->value)->toBe('m');
});

test('feet case exists', function (): void {
    $unit = DistanceUnit::FEET;
    expect($unit->value)->toBe('ft');
});

test('nautical miles case exists', function (): void {
    $unit = DistanceUnit::NAUTICAL_MILES;
    expect($unit->value)->toBe('nmi');
});

test('kilometers conversion factor is one', function (): void {
    $unit = DistanceUnit::KILOMETERS;
    expect($unit->conversionFactor())->toBe(1.0);
});

test('miles conversion factor', function (): void {
    $unit = DistanceUnit::MILES;
    expect($unit->conversionFactor())->toBe(0.621371);
});

test('meters conversion factor', function (): void {
    $unit = DistanceUnit::METERS;
    expect($unit->conversionFactor())->toBe(1000.0);
});

test('feet conversion factor', function (): void {
    $unit = DistanceUnit::FEET;
    expect($unit->conversionFactor())->toBe(3280.84);
});

test('nautical miles conversion factor', function (): void {
    $unit = DistanceUnit::NAUTICAL_MILES;
    expect($unit->conversionFactor())->toBe(0.539957);
});

test('kilometers display name', function (): void {
    $unit = DistanceUnit::KILOMETERS;
    expect($unit->displayName())->toBe('Kilometers');
});

test('miles display name', function (): void {
    $unit = DistanceUnit::MILES;
    expect($unit->displayName())->toBe('Miles');
});

test('meters display name', function (): void {
    $unit = DistanceUnit::METERS;
    expect($unit->displayName())->toBe('Meters');
});

test('feet display name', function (): void {
    $unit = DistanceUnit::FEET;
    expect($unit->displayName())->toBe('Feet');
});

test('nautical miles display name', function (): void {
    $unit = DistanceUnit::NAUTICAL_MILES;
    expect($unit->displayName())->toBe('Nautical Miles');
});

test('abbreviation returns enum value', function (): void {
    $kilometers = DistanceUnit::KILOMETERS;
    $miles = DistanceUnit::MILES;

    expect($kilometers->abbreviation())->toBe('km');
    expect($miles->abbreviation())->toBe('mi');
});

test('all cases can be instantiated', function (): void {
    $cases = DistanceUnit::cases();

    expect($cases)->toHaveCount(5);
    expect($cases)->toContain(DistanceUnit::KILOMETERS);
    expect($cases)->toContain(DistanceUnit::MILES);
    expect($cases)->toContain(DistanceUnit::METERS);
    expect($cases)->toContain(DistanceUnit::FEET);
    expect($cases)->toContain(DistanceUnit::NAUTICAL_MILES);
});

test('conversion factor calculations are accurate', function (): void {
    // Test that conversion factors make sense relative to kilometers
    $kilometers = DistanceUnit::KILOMETERS;
    $miles = DistanceUnit::MILES;
    $meters = DistanceUnit::METERS;

    // 1 kilometer should equal 1000 meters
    expect($meters->conversionFactor())->toBe($kilometers->conversionFactor() * 1000);

    // 1 kilometer should be approximately 0.621371 miles
    expect($miles->conversionFactor())->toBe(0.621371);
});

test('enum is serializable', function (): void {
    $unit = DistanceUnit::KILOMETERS;

    expect($unit->value)->toBe('km');
    expect($unit->abbreviation())->toBe('km');
});
