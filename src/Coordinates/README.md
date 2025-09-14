# Coordinates

Provides a system for handling geographic coordinates with strict typing, proper validation, and high-performance
distance calculations.

## Features

- **Strict Typing**: All methods require explicit `float` parameters for maximum type safety
- **Value Objects**: Immutable `Coordinates`, `Latitude`, and `Longitude` objects
- **Distance Calculations**: High-performance Haversine formula with caching
- **Multiple Earth Models**: Support for spherical, WGS84, and GRS80 Earth models
- **Distance Units**: Kilometers and miles support
- **Validation**: Automatic range validation for latitude (-90° to +90°) and longitude (-180° to +180°)

## Usage

### Creating Coordinates

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

// Create coordinates using the static factory method (recommended)
$coordinates = Coordinates::create(40.7128, -74.0060);

// Create coordinates directly (requires Latitude and Longitude objects)
$coordinates = new Coordinates(
    new Latitude(40.7128),
    new Longitude(-74.0060)
);
```

### Creating Individual Components

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

// Using static factory methods
$latitude = Latitude::create(40.7128);
$longitude = Longitude::create(-74.0060);

// Direct instantiation
$latitude = new Latitude(40.7128);
$longitude = new Longitude(-74.0060);
```

### Distance Calculations

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;

$amsterdam = Coordinates::create(52.3676, 4.9041);
$london = Coordinates::create(51.5074, -0.1278);

// Calculate distance in kilometers (default)
$distanceKm = $amsterdam->distanceTo($london);

// Calculate distance in miles
$distanceMiles = $amsterdam->distanceTo($london, DistanceUnit::MILES);

// Using different Earth models for advanced calculations
$distance = $amsterdam->distanceTo(
    $london,
    DistanceUnit::KILOMETERS,
    EarthModel::WGS84
);
```

### Equality Comparison

```php
$coordinates1 = Coordinates::create(40.7128, -74.0060);
$coordinates2 = Coordinates::create(40.7128, -74.0060);

// Check if coordinates are equal
$isEqual = $coordinates1->isEqual($coordinates2); // true
```

### String Representation

```php
$coordinates = Coordinates::create(40.7128, -74.0060);

// Convert to string
echo $coordinates; // "40.7128,-74.0060"
echo $coordinates->latitude; // "40.7128"
echo $coordinates->longitude; // "-74.0060"
```

## Advanced Features

### Earth Models

The package supports different Earth models for distance calculations:

```php
use JeroenGerits\Support\Coordinates\Enums\EarthModel;

// Available Earth models
EarthModel::SPHERICAL; // Spherical Earth model with mean radius
EarthModel::WGS84;     // World Geodetic System 1984
EarthModel::GRS80;     // Geodetic Reference System 1980

// Get radius values
$radiusKm = EarthModel::WGS84->getRadiusKm();     // 6371.0088
$radiusMiles = EarthModel::WGS84->getRadiusMiles(); // 3958.7613

// Get radius for specific distance unit
$radius = EarthModel::WGS84->getRadius(DistanceUnit::KILOMETERS);
```

### Batch Distance Calculations

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;

$coordinatePairs = [
    [Coordinates::create(40.7128, -74.0060), Coordinates::create(51.5074, -0.1278)],
    [Coordinates::create(52.3676, 4.9041), Coordinates::create(48.8566, 2.3522)],
];

$distances = Coordinates::batchDistanceCalculation($coordinatePairs, DistanceUnit::KILOMETERS, EarthModel::WGS84);
```

### Cache Management

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

// Check cache size
$cacheSize = Coordinates::getCacheSize();

// Clear cache
Coordinates::clearCache();
```

## Error Handling

All coordinate creation methods will throw `InvalidCoordinatesException` for invalid values:

```php
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;

try {
    $latitude = new Latitude(100.0); // Invalid: latitude must be between -90 and 90
} catch (InvalidCoordinatesException $e) {
    echo $e->getMessage(); // "Latitude value 100 is outside the valid range of -90 to 90 degrees"
}
```

## Performance

- **Caching**: Trigonometric calculations are cached for improved performance
- **Early Returns**: Identical coordinates return 0 distance immediately
- **Optimized Algorithms**: Uses efficient Haversine formula implementation
- **Memory Efficient**: Static caching with configurable limits

## Type Safety

This package enforces strict typing throughout:

- All factory methods require `float` parameters
- No automatic type coercion or string parsing
- Compile-time type checking prevents runtime errors
- Explicit distance unit specification required
