# Coordinates Package

Geographic coordinate handling with strict typing and high-performance distance calculations.

## Features

- **Type Safety**: Strict typing with readonly properties
- **Distance Calculations**: Haversine formula with multiple Earth models
- **Performance**: Optimized calculations with early returns
- **Validation**: Automatic range validation with clear error messages

## Quick Start

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;

// Create coordinates
$ny = Coordinates::create(40.7128, -74.0060);
$london = Coordinates::create(51.5074, -0.1278);

// Calculate distance
$distance = $ny->distanceTo($london); // 5570.9 km
$distanceMiles = $ny->distanceTo($london, DistanceUnit::MILES); // 3459.0 mi

// String representation
echo $ny; // "40.7128,-74.0060"
```

## Advanced Usage

### Individual Components

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

$lat = Latitude::create(40.7128);
$lon = Longitude::create(-74.0060);
```

### Batch Calculations

```php
$pairs = [
    [$ny, $london],
    [Coordinates::create(52.3676, 4.9041), Coordinates::create(48.8566, 2.3522)]
];

$distances = Coordinates::batchDistanceCalculation($pairs);
// Returns: [5570.9, 431.5] km
```


## Error Handling

```php
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;

try {
    $lat = Latitude::create(100.0); // Invalid: outside -90 to 90 range
} catch (InvalidCoordinatesException $e) {
    echo $e->getMessage(); // Clear error message with context
}
```

## Earth Models

```php
use JeroenGerits\Support\Coordinates\Enums\EarthModel;

// Different Earth models for specialized calculations
$distance = $ny->distanceTo($london, DistanceUnit::KILOMETERS, EarthModel::WGS84);
$distance = $ny->distanceTo($london, DistanceUnit::KILOMETERS, EarthModel::SPHERICAL);
```

## Performance

- **Early Returns**: Identical coordinates return 0 distance immediately
- **Batch Processing**: Efficient processing of multiple coordinate pairs
- **Optimized Calculations**: Direct trigonometric calculations without overhead
- **Memory Efficient**: No caching overhead for single-use calculations

