<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;

it('has correct string values', function (): void {
    expect(EarthModel::SPHERICAL->value)->toBe('spherical')
        ->and(EarthModel::WGS84->value)->toBe('wgs84')
        ->and(EarthModel::GRS80->value)->toBe('grs80');
});

it('returns correct radius in kilometers', function (): void {
    expect(EarthModel::SPHERICAL->getRadiusKm())->toBe(6371.0)
        ->and(EarthModel::WGS84->getRadiusKm())->toBe(6371.0088)
        ->and(EarthModel::GRS80->getRadiusKm())->toBe(6371.0000);
});

it('returns correct radius in miles', function (): void {
    expect(EarthModel::SPHERICAL->getRadiusMiles())->toBe(3958.8)
        ->and(EarthModel::WGS84->getRadiusMiles())->toBe(3958.7613)
        ->and(EarthModel::GRS80->getRadiusMiles())->toBe(3958.7600);
});

it('returns correct radius for distance units', function (): void {
    expect(EarthModel::WGS84->getRadius(DistanceUnit::KILOMETERS))->toBe(6371.0088)
        ->and(EarthModel::WGS84->getRadius(DistanceUnit::MILES))->toBe(3958.7613)
        ->and(EarthModel::SPHERICAL->getRadius(DistanceUnit::KILOMETERS))->toBe(6371.0)
        ->and(EarthModel::SPHERICAL->getRadius(DistanceUnit::MILES))->toBe(3958.8);
});

it('can be used in match expressions', function (): void {
    $model = EarthModel::WGS84;

    $result = match ($model) {
        EarthModel::SPHERICAL => 'spherical',
        EarthModel::WGS84 => 'wgs84',
        EarthModel::GRS80 => 'grs80',
    };

    expect($result)->toBe('wgs84');
});

it('can be compared for equality', function (): void {
    expect(EarthModel::WGS84)->toBe(EarthModel::WGS84)
        ->and(EarthModel::WGS84)->not->toBe(EarthModel::SPHERICAL)
        ->and(EarthModel::SPHERICAL)->not->toBe(EarthModel::GRS80);
});
