<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;

it('has all expected enum cases', function (): void {
    $cases = DistanceUnit::cases();

    expect($cases)->toHaveCount(11)
        ->and($cases)->toContain(DistanceUnit::KILOMETERS)
        ->and($cases)->toContain(DistanceUnit::MILES)
        ->and($cases)->toContain(DistanceUnit::NAUTICAL_MILES)
        ->and($cases)->toContain(DistanceUnit::METERS)
        ->and($cases)->toContain(DistanceUnit::MILLIMETERS)
        ->and($cases)->toContain(DistanceUnit::CENTIMETERS)
        ->and($cases)->toContain(DistanceUnit::DECIMETERS)
        ->and($cases)->toContain(DistanceUnit::INCHES)
        ->and($cases)->toContain(DistanceUnit::FEET)
        ->and($cases)->toContain(DistanceUnit::YARDS)
        ->and($cases)->toContain(DistanceUnit::LIGHT_YEARS);
});

it('has correct string values for all cases', function (): void {
    expect(DistanceUnit::KILOMETERS->value)->toBe('km')
        ->and(DistanceUnit::MILES->value)->toBe('mi')
        ->and(DistanceUnit::NAUTICAL_MILES->value)->toBe('nmi')
        ->and(DistanceUnit::METERS->value)->toBe('m')
        ->and(DistanceUnit::MILLIMETERS->value)->toBe('mm')
        ->and(DistanceUnit::CENTIMETERS->value)->toBe('cm')
        ->and(DistanceUnit::DECIMETERS->value)->toBe('dm')
        ->and(DistanceUnit::INCHES->value)->toBe('in')
        ->and(DistanceUnit::FEET->value)->toBe('ft')
        ->and(DistanceUnit::YARDS->value)->toBe('yd')
        ->and(DistanceUnit::LIGHT_YEARS->value)->toBe('ly');
});

it('returns correct conversion factors for all units', function (): void {
    expect(DistanceUnit::KILOMETERS->getConversionFactor())->toBe(1.0)
        ->and(DistanceUnit::MILES->getConversionFactor())->toBe(0.621371)
        ->and(DistanceUnit::NAUTICAL_MILES->getConversionFactor())->toBe(0.539957)
        ->and(DistanceUnit::METERS->getConversionFactor())->toBe(1000.0)
        ->and(DistanceUnit::MILLIMETERS->getConversionFactor())->toBe(1000000.0)
        ->and(DistanceUnit::CENTIMETERS->getConversionFactor())->toBe(100000.0)
        ->and(DistanceUnit::DECIMETERS->getConversionFactor())->toBe(10000.0)
        ->and(DistanceUnit::INCHES->getConversionFactor())->toBe(39370.1)
        ->and(DistanceUnit::FEET->getConversionFactor())->toBe(3280.84)
        ->and(DistanceUnit::YARDS->getConversionFactor())->toBe(1093.61)
        ->and(DistanceUnit::LIGHT_YEARS->getConversionFactor())->toBe(1.057e-13);
});

it('returns correct display names for all units', function (): void {
    expect(DistanceUnit::KILOMETERS->getDisplayName())->toBe('kilometers')
        ->and(DistanceUnit::MILES->getDisplayName())->toBe('miles')
        ->and(DistanceUnit::NAUTICAL_MILES->getDisplayName())->toBe('nautical miles')
        ->and(DistanceUnit::METERS->getDisplayName())->toBe('meters')
        ->and(DistanceUnit::MILLIMETERS->getDisplayName())->toBe('millimeters')
        ->and(DistanceUnit::CENTIMETERS->getDisplayName())->toBe('centimeters')
        ->and(DistanceUnit::DECIMETERS->getDisplayName())->toBe('decimeters')
        ->and(DistanceUnit::INCHES->getDisplayName())->toBe('inches')
        ->and(DistanceUnit::FEET->getDisplayName())->toBe('feet')
        ->and(DistanceUnit::YARDS->getDisplayName())->toBe('yards')
        ->and(DistanceUnit::LIGHT_YEARS->getDisplayName())->toBe('light years');
});

it('returns correct abbreviations for all units', function (): void {
    expect(DistanceUnit::KILOMETERS->getAbbreviation())->toBe('km')
        ->and(DistanceUnit::MILES->getAbbreviation())->toBe('mi')
        ->and(DistanceUnit::NAUTICAL_MILES->getAbbreviation())->toBe('nmi')
        ->and(DistanceUnit::METERS->getAbbreviation())->toBe('m')
        ->and(DistanceUnit::MILLIMETERS->getAbbreviation())->toBe('mm')
        ->and(DistanceUnit::CENTIMETERS->getAbbreviation())->toBe('cm')
        ->and(DistanceUnit::DECIMETERS->getAbbreviation())->toBe('dm')
        ->and(DistanceUnit::INCHES->getAbbreviation())->toBe('in')
        ->and(DistanceUnit::FEET->getAbbreviation())->toBe('ft')
        ->and(DistanceUnit::YARDS->getAbbreviation())->toBe('yd')
        ->and(DistanceUnit::LIGHT_YEARS->getAbbreviation())->toBe('ly');
});

it('abbreviation method returns the same as value property', function (): void {
    foreach (DistanceUnit::cases() as $unit) {
        expect($unit->getAbbreviation())->toBe($unit->value);
    }
});

it('validates conversion factor accuracy', function (): void {
    // Test that conversion factors are reasonable
    $kilometers = DistanceUnit::KILOMETERS->getConversionFactor();
    $miles = DistanceUnit::MILES->getConversionFactor();
    $nauticalMiles = DistanceUnit::NAUTICAL_MILES->getConversionFactor();
    $meters = DistanceUnit::METERS->getConversionFactor();
    $millimeters = DistanceUnit::MILLIMETERS->getConversionFactor();
    $centimeters = DistanceUnit::CENTIMETERS->getConversionFactor();
    $decimeters = DistanceUnit::DECIMETERS->getConversionFactor();
    $inches = DistanceUnit::INCHES->getConversionFactor();
    $feet = DistanceUnit::FEET->getConversionFactor();
    $yards = DistanceUnit::YARDS->getConversionFactor();
    $lightYears = DistanceUnit::LIGHT_YEARS->getConversionFactor();

    // Kilometers should be the base unit (1.0)
    expect($kilometers)->toBe(1.0)
        ->and($miles)->toBeGreaterThan(0.6)->toBeLessThan(0.7)
        ->and($nauticalMiles)->toBeGreaterThan(0.5)->toBeLessThan(0.6)
        ->and($meters)->toBe(1000.0)
        ->and($millimeters)->toBe(1000000.0)
        ->and($centimeters)->toBe(100000.0)
        ->and($decimeters)->toBe(10000.0)
        ->and($inches)->toBeGreaterThan(39000)->toBeLessThan(40000)
        ->and($feet)->toBeGreaterThan(3200)->toBeLessThan(3300)
        ->and($yards)->toBeGreaterThan(1090)->toBeLessThan(1100)
        ->and($lightYears)->toBeLessThan(1e-10);

    // Miles should be less than 1 (since 1 km = ~0.62 miles)

    // Nautical miles should be less than 1 (since 1 km = ~0.54 nautical miles)

    // Meters should be greater than 1 (since 1 km = 1000 meters)

    // Millimeters should be much greater than 1 (since 1 km = 1,000,000 mm)

    // Centimeters should be greater than meters (since 1 km = 100,000 cm)

    // Decimeters should be greater than meters (since 1 km = 10,000 dm)

    // Imperial units should be reasonable

    // Light years should be very small (since 1 km is a tiny fraction of a light year)
});

it('handles enum comparison correctly', function (): void {
    $km1 = DistanceUnit::KILOMETERS;
    $km2 = DistanceUnit::KILOMETERS;
    $miles = DistanceUnit::MILES;

    expect($km1)->toBe($km2)
        ->and($km1)->not->toBe($miles)
        ->and($km1 === $km2)->toBeTrue()
        ->and($km1 === $miles)->toBeFalse();
});

it('can be used in match expressions', function (): void {
    $getUnitCategory = function (DistanceUnit $unit): string {
        return match ($unit) {
            DistanceUnit::KILOMETERS => 'primary metric',
            DistanceUnit::METERS => 'primary metric',
            DistanceUnit::MILLIMETERS => 'small metric',
            DistanceUnit::CENTIMETERS => 'small metric',
            DistanceUnit::DECIMETERS => 'small metric',
            DistanceUnit::MILES => 'imperial',
            DistanceUnit::INCHES => 'imperial',
            DistanceUnit::FEET => 'imperial',
            DistanceUnit::YARDS => 'imperial',
            DistanceUnit::NAUTICAL_MILES => 'specialized',
            DistanceUnit::LIGHT_YEARS => 'specialized',
        };
    };

    expect($getUnitCategory(DistanceUnit::KILOMETERS))->toBe('primary metric')
        ->and($getUnitCategory(DistanceUnit::METERS))->toBe('primary metric')
        ->and($getUnitCategory(DistanceUnit::MILLIMETERS))->toBe('small metric')
        ->and($getUnitCategory(DistanceUnit::CENTIMETERS))->toBe('small metric')
        ->and($getUnitCategory(DistanceUnit::DECIMETERS))->toBe('small metric')
        ->and($getUnitCategory(DistanceUnit::MILES))->toBe('imperial')
        ->and($getUnitCategory(DistanceUnit::INCHES))->toBe('imperial')
        ->and($getUnitCategory(DistanceUnit::FEET))->toBe('imperial')
        ->and($getUnitCategory(DistanceUnit::YARDS))->toBe('imperial')
        ->and($getUnitCategory(DistanceUnit::NAUTICAL_MILES))->toBe('specialized')
        ->and($getUnitCategory(DistanceUnit::LIGHT_YEARS))->toBe('specialized');
});

it('can be serialized and deserialized', function (): void {
    foreach (DistanceUnit::cases() as $unit) {
        $serialized = serialize($unit);
        $unserialized = unserialize($serialized);

        expect($unserialized)->toBe($unit)
            ->and($unserialized->value)->toBe($unit->value);
    }
});

it('maintains consistency across all methods', function (): void {
    foreach (DistanceUnit::cases() as $unit) {
        // The abbreviation should always match the value
        expect($unit->getAbbreviation())->toBe($unit->value)
            ->and($unit->getDisplayName())->not->toBeEmpty()
            ->and($unit->getAbbreviation())->not->toBeEmpty()
            ->and($unit->getConversionFactor())->toBeGreaterThan(0);

        // All methods should return non-empty strings

        // Conversion factor should be a positive number
    }
});

it('provides meaningful string representation', function (): void {
    foreach (DistanceUnit::cases() as $unit) {
        // Enums don't have __toString() by default, but we can test the value property
        // which is what would be used for string representation
        expect($unit->value)->toBeString()
            ->and($unit->value)->not->toBeEmpty();
    }

    // Test specific values for each case
    expect(DistanceUnit::KILOMETERS->value)->toBe('km')
        ->and(DistanceUnit::MILES->value)->toBe('mi')
        ->and(DistanceUnit::NAUTICAL_MILES->value)->toBe('nmi')
        ->and(DistanceUnit::METERS->value)->toBe('m')
        ->and(DistanceUnit::MILLIMETERS->value)->toBe('mm')
        ->and(DistanceUnit::CENTIMETERS->value)->toBe('cm')
        ->and(DistanceUnit::DECIMETERS->value)->toBe('dm')
        ->and(DistanceUnit::INCHES->value)->toBe('in')
        ->and(DistanceUnit::FEET->value)->toBe('ft')
        ->and(DistanceUnit::YARDS->value)->toBe('yd')
        ->and(DistanceUnit::LIGHT_YEARS->value)->toBe('ly');
});
