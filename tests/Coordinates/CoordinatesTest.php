<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

describe('Coordinates Package', function (): void {
    describe('Coordinates', function (): void {
        describe('Factory', function (): void {
            it('creates from float values', function (): void {
                $latitude = 52.3676;
                $longitude = 4.9041;

                $coordinates = Coordinates::create($latitude, $longitude);

                expect($coordinates)->toBeInstanceOf(Coordinates::class)
                    ->and($coordinates->latitude->value)->toBe($latitude)
                    ->and($coordinates->longitude->value)->toBe($longitude);
            });

            it('creates from negative values', function (): void {
                $latitude = -52.3676;
                $longitude = -4.9041;

                $coordinates = Coordinates::create($latitude, $longitude);

                expect($coordinates)->toBeInstanceOf(Coordinates::class)
                    ->and($coordinates->latitude->value)->toBe($latitude)
                    ->and($coordinates->longitude->value)->toBe($longitude);
            });

            it('creates from zero values', function (): void {
                $latitude = 0.0;
                $longitude = 0.0;

                $coordinates = Coordinates::create($latitude, $longitude);

                expect($coordinates)->toBeInstanceOf(Coordinates::class)
                    ->and($coordinates->latitude->value)->toBe($latitude)
                    ->and($coordinates->longitude->value)->toBe($longitude);
            });

            it('creates from integer values', function (): void {
                $latitude = 52;
                $longitude = 4;

                $coordinates = Coordinates::create($latitude, $longitude);

                expect($coordinates)->toBeInstanceOf(Coordinates::class)
                    ->and($coordinates->latitude->value)->toBe(52.0)
                    ->and($coordinates->longitude->value)->toBe(4.0);
            });
        });

        describe('String', function (): void {
            it('converts to comma-separated format', function (): void {
                $coordinates = Coordinates::create(40.7128, -74.0060);

                expect((string) $coordinates)->toBe('40.7128,-74.006');
            });

            it('includes latitude and longitude', function (): void {
                $coordinates = Coordinates::create(52.3676, 4.9041);

                expect((string) $coordinates)->toBe('52.3676,4.9041')
                    ->and((string) $coordinates->latitude)->toBe('52.3676')
                    ->and((string) $coordinates->longitude)->toBe('4.9041');
            });
        });

        describe('Equality', function (): void {
            it('returns true for identical coordinates', function (): void {
                $coordinates1 = Coordinates::create(40.7128, -74.0060);
                $coordinates2 = Coordinates::create(40.7128, -74.0060);

                expect($coordinates1->isEqual($coordinates2))->toBeTrue();
            });

            it('returns false for different coordinates', function (): void {
                $coordinates1 = Coordinates::create(40.7128, -74.0060);
                $coordinates2 = Coordinates::create(51.5074, -0.1278);

                expect($coordinates1->isEqual($coordinates2))->toBeFalse();
            });

            it('returns false for incompatible types', function (): void {
                $coordinates = Coordinates::create(40.7128, -74.0060);

                // Create a mock object that implements Equatable but is not a Coordinates
                $other = new class implements \JeroenGerits\Support\Shared\Contracts\Equatable
                {
                    public function isEqual(\JeroenGerits\Support\Shared\Contracts\Equatable $other): bool
                    {
                        return false;
                    }
                };

                expect($coordinates->isEqual($other))->toBeFalse();
            });
        });

        describe('Distance', function (): void {
            describe('Basic', function (): void {
                it('calculates in kilometers by default', function (): void {
                    $coordinates1 = Coordinates::create(52.3676, 4.9041); // Amsterdam
                    $coordinates2 = Coordinates::create(52.3736, 4.9101); // Close to Amsterdam
                    $distance = $coordinates1->distanceTo($coordinates2);

                    // Should be approximately 0.78 km
                    expect($distance)->toBeGreaterThan(0.7)
                        ->and($distance)->toBeLessThan(0.9);
                });

                it('calculates in miles when specified', function (): void {
                    $coordinates1 = Coordinates::create(52.3676, 4.9041); // Amsterdam
                    $coordinates2 = Coordinates::create(52.3736, 4.9101); // Close to Amsterdam
                    $distance = $coordinates1->distanceTo($coordinates2, DistanceUnit::MILES);

                    // Should be approximately 0.48 miles
                    expect($distance)->toBeGreaterThan(0.4)
                        ->and($distance)->toBeLessThan(0.6);
                });

                it('returns zero for identical coordinates', function (): void {
                    $coordinates1 = Coordinates::create(52.3676, 4.9041);
                    $coordinates2 = Coordinates::create(52.3676, 4.9041);
                    $distance = $coordinates1->distanceTo($coordinates2);

                    expect($distance)->toBe(0.0);
                });
            });

            describe('Real-world', function (): void {
                it('calculates between major cities', function (): void {
                    $amsterdam = Coordinates::create(52.3676, 4.9041); // Amsterdam
                    $london = Coordinates::create(51.5074, -0.1278);   // London
                    $distance = $amsterdam->distanceTo($london);

                    // Distance between Amsterdam and London is approximately 357 km
                    expect($distance)->toBeGreaterThan(350)
                        ->and($distance)->toBeLessThan(365);
                });

                it('calculates between antipodal points', function (): void {
                    $point1 = Coordinates::create(0.0, 0.0);     // Equator, Prime Meridian
                    $point2 = Coordinates::create(0.0, 180.0);   // Equator, International Date Line
                    $distance = $point1->distanceTo($point2);

                    // Half the Earth's circumference at the equator
                    expect($distance)->toBeGreaterThan(20000)
                        ->and($distance)->toBeLessThan(20050);
                });

                it('handles negative coordinates', function (): void {
                    $point1 = Coordinates::create(-33.9249, 18.4241); // Cape Town
                    $point2 = Coordinates::create(-26.2041, 28.0473); // Johannesburg
                    $distance = $point1->distanceTo($point2);

                    // Distance between Cape Town and Johannesburg is approximately 1270 km
                    expect($distance)->toBeGreaterThan(1260)
                        ->and($distance)->toBeLessThan(1280);
                });

                it('calculates very small distances', function (): void {
                    $point1 = Coordinates::create(40.7128, -74.0060); // New York
                    $point2 = Coordinates::create(40.7129, -74.0061); // Very close to New York
                    $distance = $point1->distanceTo($point2);

                    // Should be a very small distance (less than 100 meters)
                    expect($distance)->toBeGreaterThan(0)
                        ->and($distance)->toBeLessThan(0.1);
                });
            });

            describe('Earth Models', function (): void {
                it('supports different models', function (): void {
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
            });
        });

        describe('Batch', function (): void {
            it('processes multiple pairs efficiently', function (): void {
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

            it('supports different units', function (): void {
                $coordinatePairs = [
                    [Coordinates::create(52.3676, 4.9041), Coordinates::create(52.3736, 4.9101)],
                ];

                $distancesKm = Coordinates::batchDistanceCalculation($coordinatePairs, DistanceUnit::KILOMETERS);
                $distancesMiles = Coordinates::batchDistanceCalculation($coordinatePairs, DistanceUnit::MILES);

                expect($distancesKm[0])->toBeGreaterThan(0.7)->toBeLessThan(0.9)
                    ->and($distancesMiles[0])->toBeGreaterThan(0.4)->toBeLessThan(0.6);
            });
        });

        describe('Cache', function (): void {
            it('manages trigonometric cache', function (): void {
                // Clear cache first
                Coordinates::getCache()->clear();
                expect(Coordinates::getCache()->getStats()->getItems())->toBe(0);

                // Perform some calculations to populate cache
                $point1 = Coordinates::create(40.7128, -74.0060);
                $point2 = Coordinates::create(51.5074, -0.1278);
                $point1->distanceTo($point2);

                // Cache should now have entries
                expect(Coordinates::getCache()->getStats()->getItems())->toBeGreaterThan(0);

                // Clear cache again
                Coordinates::getCache()->clear();
                expect(Coordinates::getCache()->getStats()->getItems())->toBe(0);
            });

            it('manages Earth radius cache separately', function (): void {
                // Clear cache first
                Coordinates::getCache()->clear();
                expect(Coordinates::getCache()->getStats()->getItems())->toBe(0);

                // Perform calculations with different Earth models and units
                $point1 = Coordinates::create(40.7128, -74.0060);
                $point2 = Coordinates::create(51.5074, -0.1278);

                $point1->distanceTo($point2, DistanceUnit::KILOMETERS, EarthModel::WGS84);
                $point1->distanceTo($point2, DistanceUnit::MILES, EarthModel::WGS84);
                $point1->distanceTo($point2, DistanceUnit::KILOMETERS, EarthModel::SPHERICAL);

                // Earth radius cache should have entries
                expect(Coordinates::getCache()->getStats()->getItems())->toBeGreaterThan(0);

                // Clear cache again
                Coordinates::getCache()->clear();
                expect(Coordinates::getCache()->getStats()->getItems())->toBe(0);
            });

            it('caches are independent', function (): void {
                // Clear cache first
                Coordinates::getCache()->clear();

                // Perform calculations
                $point1 = Coordinates::create(40.7128, -74.0060);
                $point2 = Coordinates::create(51.5074, -0.1278);
                $point1->distanceTo($point2);

                $trigCacheSize = Coordinates::getCache()->getStats()->getItems();
                $radiusCacheSize = Coordinates::getCache()->getStats()->getItems();

                // Both caches should have entries
                expect($trigCacheSize)->toBeGreaterThan(0)
                    ->and($radiusCacheSize)->toBeGreaterThan(0);

                // Clear only trigonometric cache
                Coordinates::getCache()->clear();
                expect(Coordinates::getCache()->getStats()->getItems())->toBe(0); // Both are cleared
            });

            it('demonstrates HasCache trait usage with getCachedMetadata', function (): void {
                $coordinates = Coordinates::create(40.7128, -74.0060);

                // First call - should compute and cache
                $metadata1 = $coordinates->getCachedMetadata();
                expect($metadata1)->toBeArray()
                    ->and($metadata1['latitude'])->toBe(40.7128)
                    ->and($metadata1['longitude'])->toBe(-74.0060)
                    ->and($metadata1['string_representation'])->toBe('40.7128,-74.006')
                    ->and($metadata1)->toHaveKey('computed_at')
                    ->and($metadata1)->toHaveKey('hash');

                // Second call - should return cached result
                $metadata2 = $coordinates->getCachedMetadata();
                expect($metadata1['computed_at'])->toBe($metadata2['computed_at'])
                    ->and($metadata1)->toBe($metadata2); // Same timestamp = cached
                // Identical arrays
            });
        });
    });

    describe('Latitude', function (): void {
        describe('Factory', function (): void {
            it('creates from float value', function (): void {
                $latitude = Latitude::create(52.3676);

                expect($latitude)->toBeInstanceOf(Latitude::class)
                    ->and($latitude->value)->toBe(52.3676);
            });

            it('creates from integer value', function (): void {
                $latitude = Latitude::create(52);

                expect($latitude)->toBeInstanceOf(Latitude::class)
                    ->and($latitude->value)->toBe(52.0);
            });
        });

        describe('Validation', function (): void {
            it('throws exception for invalid range', function (): void {
                expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Latitude => new Latitude(100.0))->toThrow(InvalidCoordinatesException::class)
                    ->and(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Latitude => new Latitude(-100.0))->toThrow(InvalidCoordinatesException::class);
            });

            it('throws exception with improved error message', function (): void {
                try {
                    new Latitude(100.0);
                    expect(false)->toBeTrue('Expected exception was not thrown');
                } catch (InvalidCoordinatesException $e) {
                    expect($e->getMessage())->toContain('Latitude value 100 is outside the valid range of -90 to 90 degrees')
                        ->and($e->getCode())->toBe(InvalidCoordinatesException::CODE_OUT_OF_RANGE);
                }
            });
        });

        describe('String', function (): void {
            it('converts to string format', function (): void {
                $latitude = new Latitude(40.7128);

                expect((string) $latitude)->toBe('40.7128')
                    ->and($latitude->toString())->toBe('40.7128');
            });
        });

        describe('Equality', function (): void {
            it('returns true for identical values', function (): void {
                $latitude1 = new Latitude(40.7128);
                $latitude2 = new Latitude(40.7128);

                expect($latitude1->isEqual($latitude2))->toBeTrue();
            });

            it('returns false for different values', function (): void {
                $latitude1 = new Latitude(40.7128);
                $latitude2 = new Latitude(51.5074);

                expect($latitude1->isEqual($latitude2))->toBeFalse();
            });

            it('returns false for different types', function (): void {
                $latitude = new Latitude(40.7128);
                $longitude = new Longitude(-74.0060);

                expect($latitude->isEqual($longitude))->toBeFalse();
            });
        });

        describe('Constants', function (): void {
            it('has correct min and max values', function (): void {
                expect(Latitude::MIN_LATITUDE)->toBe(-90.0)
                    ->and(Latitude::MAX_LATITUDE)->toBe(90.0);
            });
        });
    });

    describe('Longitude', function (): void {
        describe('Factory', function (): void {
            it('creates from float value', function (): void {
                $longitude = Longitude::create(4.9041);

                expect($longitude)->toBeInstanceOf(Longitude::class)
                    ->and($longitude->value)->toBe(4.9041);
            });

            it('creates from integer value', function (): void {
                $longitude = Longitude::create(4);

                expect($longitude)->toBeInstanceOf(Longitude::class)
                    ->and($longitude->value)->toBe(4.0);
            });
        });

        describe('Validation', function (): void {
            it('throws exception for invalid range', function (): void {
                expect(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Longitude => new Longitude(200.0))->toThrow(InvalidCoordinatesException::class)
                    ->and(fn (): \JeroenGerits\Support\Coordinates\ValueObjects\Longitude => new Longitude(-200.0))->toThrow(InvalidCoordinatesException::class);
            });

            it('throws exception with improved error message', function (): void {
                try {
                    new Longitude(200.0);
                    expect(false)->toBeTrue('Expected exception was not thrown');
                } catch (InvalidCoordinatesException $e) {
                    expect($e->getMessage())->toContain('Longitude value 200 is outside the valid range of -180 to 180 degrees')
                        ->and($e->getCode())->toBe(InvalidCoordinatesException::CODE_OUT_OF_RANGE);
                }
            });
        });

        describe('String', function (): void {
            it('converts to string format', function (): void {
                $longitude = new Longitude(-74.0060);

                expect((string) $longitude)->toBe('-74.006')
                    ->and($longitude->toString())->toBe('-74.006');
            });
        });

        describe('Equality', function (): void {
            it('returns true for identical values', function (): void {
                $longitude1 = new Longitude(-74.0060);
                $longitude2 = new Longitude(-74.0060);

                expect($longitude1->isEqual($longitude2))->toBeTrue();
            });

            it('returns false for different values', function (): void {
                $longitude1 = new Longitude(-74.0060);
                $longitude2 = new Longitude(-0.1278);

                expect($longitude1->isEqual($longitude2))->toBeFalse();
            });

            it('returns false for different types', function (): void {
                $longitude = new Longitude(-74.0060);
                $latitude = new Latitude(40.7128);

                expect($longitude->isEqual($latitude))->toBeFalse();
            });
        });

        describe('Constants', function (): void {
            it('has correct min and max values', function (): void {
                expect(Longitude::MIN_LONGITUDE)->toBe(-180.0)
                    ->and(Longitude::MAX_LONGITUDE)->toBe(180.0);
            });
        });
    });

    describe('DistanceUnit', function (): void {
        describe('Values', function (): void {
            it('has correct string values', function (): void {
                expect(DistanceUnit::KILOMETERS->value)->toBe('km')
                    ->and(DistanceUnit::MILES->value)->toBe('mi');
            });

            it('provides all cases', function (): void {
                $cases = DistanceUnit::cases();

                expect($cases)->toHaveCount(2)
                    ->and($cases)->toContain(DistanceUnit::KILOMETERS)
                    ->and($cases)->toContain(DistanceUnit::MILES);
            });
        });

        describe('Equality', function (): void {
            it('supports comparison', function (): void {
                expect(DistanceUnit::KILOMETERS)->toBe(DistanceUnit::KILOMETERS)
                    ->and(DistanceUnit::KILOMETERS)->not->toBe(DistanceUnit::MILES)
                    ->and(DistanceUnit::MILES)->toBe(DistanceUnit::MILES);
            });
        });

        describe('Pattern Matching', function (): void {
            it('works with match expressions', function (): void {
                $unit = DistanceUnit::KILOMETERS;

                $result = match ($unit) {
                    DistanceUnit::KILOMETERS => 'kilometers',
                    DistanceUnit::MILES => 'miles',
                };

                expect($result)->toBe('kilometers');
            });

            it('works with switch statements', function (): void {
                $unit = DistanceUnit::MILES;
                $result = '';

                switch ($unit) {
                    case DistanceUnit::KILOMETERS:
                        $result = 'km';

                        break;
                    case DistanceUnit::MILES:
                        $result = 'mi';

                        break;
                }

                expect($result)->toBe('mi');
            });
        });

        describe('Usage', function (): void {
            it('can be used in array keys', function (): void {
                $units = [
                    DistanceUnit::KILOMETERS->value => 'kilometers',
                    DistanceUnit::MILES->value => 'miles',
                ];

                expect($units)->toHaveKey('km')
                    ->and($units)->toHaveKey('mi')
                    ->and($units['km'])->toBe('kilometers')
                    ->and($units['mi'])->toBe('miles');
            });

            it('can be iterated', function (): void {
                $allUnits = [];

                foreach (DistanceUnit::cases() as $unit) {
                    $allUnits[] = $unit->value;
                }

                expect($allUnits)->toContain('km')
                    ->and($allUnits)->toContain('mi')
                    ->and($allUnits)->toHaveCount(2);
            });

            it('can be used as parameters', function (): void {
                $testFunction = function (DistanceUnit $unit): string {
                    return $unit->value;
                };

                expect($testFunction(DistanceUnit::KILOMETERS))->toBe('km')
                    ->and($testFunction(DistanceUnit::MILES))->toBe('mi');
            });

            it('can be used in return types', function (): void {
                $getUnit = function (string $type): DistanceUnit {
                    return match ($type) {
                        'km' => DistanceUnit::KILOMETERS,
                        'mi' => DistanceUnit::MILES,
                        default => throw new InvalidArgumentException('Invalid unit type'),
                    };
                };

                expect($getUnit('km'))->toBe(DistanceUnit::KILOMETERS)
                    ->and($getUnit('mi'))->toBe(DistanceUnit::MILES);
            });
        });
    });

    describe('EarthModel', function (): void {
        describe('Values', function (): void {
            it('has correct string values', function (): void {
                expect(EarthModel::SPHERICAL->value)->toBe('spherical')
                    ->and(EarthModel::WGS84->value)->toBe('wgs84')
                    ->and(EarthModel::GRS80->value)->toBe('grs80');
            });

            it('supports equality comparison', function (): void {
                expect(EarthModel::WGS84)->toBe(EarthModel::WGS84)
                    ->and(EarthModel::WGS84)->not->toBe(EarthModel::SPHERICAL)
                    ->and(EarthModel::SPHERICAL)->not->toBe(EarthModel::GRS80);
            });
        });

        describe('Radius', function (): void {
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
        });

        describe('Pattern Matching', function (): void {
            it('works with match expressions', function (): void {
                $model = EarthModel::WGS84;

                $result = match ($model) {
                    EarthModel::SPHERICAL => 'spherical',
                    EarthModel::WGS84 => 'wgs84',
                    EarthModel::GRS80 => 'grs80',
                };

                expect($result)->toBe('wgs84');
            });
        });
    });

    describe('InvalidCoordinatesException', function (): void {
        describe('Creation', function (): void {
            it('creates with default message', function (): void {
                $exception = new InvalidCoordinatesException;

                expect($exception->getMessage())->toBe('Invalid coordinates provided')
                    ->and($exception->getCode())->toBe(1001)
                    ->and($exception->getPrevious())->toBeNull();
            });

            it('creates with custom message', function (): void {
                $message = 'Custom coordinates error message';
                $exception = new InvalidCoordinatesException($message);

                expect($exception->getMessage())->toBe($message)
                    ->and($exception->getCode())->toBe(1001)
                    ->and($exception->getPrevious())->toBeNull();
            });

            it('creates with custom message and code', function (): void {
                $message = 'Custom coordinates error message';
                $code = 2001;
                $exception = new InvalidCoordinatesException($message, $code);

                expect($exception->getMessage())->toBe($message)
                    ->and($exception->getCode())->toBe($code)
                    ->and($exception->getPrevious())->toBeNull();
            });

            it('creates with custom message, code and previous exception', function (): void {
                $message = 'Custom coordinates error message';
                $code = 2001;
                $previous = new Exception('Previous exception');
                $exception = new InvalidCoordinatesException($message, $code, $previous);

                expect($exception->getMessage())->toBe($message)
                    ->and($exception->getCode())->toBe($code)
                    ->and($exception->getPrevious())->toBe($previous);
            });

            it('extends Exception class', function (): void {
                $exception = new InvalidCoordinatesException;

                expect($exception)->toBeInstanceOf(Exception::class);
            });
        });

        describe('Factory Methods', function (): void {
            it('creates for latitude out of range', function (): void {
                $exception = InvalidCoordinatesException::latitudeOutOfRange(100.0);

                expect($exception->getMessage())->toContain('Latitude value 100 is outside the valid range')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_OUT_OF_RANGE);
            });

            it('creates for longitude out of range', function (): void {
                $exception = InvalidCoordinatesException::longitudeOutOfRange(200.0);

                expect($exception->getMessage())->toContain('Longitude value 200 is outside the valid range')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_OUT_OF_RANGE);
            });

            it('creates for invalid array structure', function (): void {
                $exception = InvalidCoordinatesException::invalidArrayStructure([]);

                expect($exception->getMessage())->toContain('Invalid coordinate array structure')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_INVALID_FORMAT);
            });

            it('creates for out of range with custom parameters', function (): void {
                $exception = InvalidCoordinatesException::createOutOfRange(150.0, 'CustomCoordinate', -90.0, 90.0);

                expect($exception->getMessage())->toBe('CustomCoordinate value 150 is outside the valid range of -90 to 90 degrees')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_OUT_OF_RANGE);
            });

            it('creates for invalid type with detailed information', function (): void {
                $exception = InvalidCoordinatesException::invalidType('invalid', 'latitude');

                expect($exception->getMessage())->toContain('Invalid latitude type: string')
                    ->and($exception->getMessage())->toContain('Expected: float, int, string, or Latitude')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_INVALID_TYPE);
            });

            it('creates for invalid format with examples', function (): void {
                $exception = InvalidCoordinatesException::invalidFormat('not-a-number', 'longitude');

                expect($exception->getMessage())->toContain("Invalid longitude format: 'not-a-number'")
                    ->and($exception->getMessage())->toContain("Expected decimal degrees (e.g., '-74.0060')")
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_INVALID_FORMAT);
            });

            it('creates for missing array keys with details', function (): void {
                $array = ['lat' => 40.7128];
                $exception = InvalidCoordinatesException::missingFromArray($array, 'lng');

                expect($exception->getMessage())->toContain("Required key 'lng' missing from coordinate array")
                    ->and($exception->getMessage())->toContain('Available keys: [lat] (1 total)')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_MISSING_VALUE);
            });

            it('creates for invalid array structure with details', function (): void {
                $array = ['invalid' => 'structure'];
                $exception = InvalidCoordinatesException::invalidArrayStructure($array);

                expect($exception->getMessage())->toContain('Invalid coordinate array structure')
                    ->and($exception->getMessage())->toContain('Got: array with 1 elements and keys [invalid]')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_INVALID_FORMAT);
            });

            it('creates for invalid numeric value', function (): void {
                $exception = InvalidCoordinatesException::invalidNumericValue('not-numeric', 'coordinate');

                expect($exception->getMessage())->toContain("Invalid coordinate numeric value: 'not-numeric'")
                    ->and($exception->getMessage())->toContain('Expected a valid numeric value that can be converted to float')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_INVALID_TYPE);
            });

            it('creates for empty value', function (): void {
                $exception = InvalidCoordinatesException::emptyValue('latitude');

                expect($exception->getMessage())->toBe('Empty latitude value provided. Coordinate values cannot be empty or null')
                    ->and($exception->getCode())->toBe(InvalidCoordinatesException::CODE_MISSING_VALUE);
            });
        });
    });
});
