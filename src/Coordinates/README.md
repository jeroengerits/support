# Coordinates Package

Geographic coordinate handling with strict typing and high-performance distance calculations.

## Features

- **Type Safety**: Strict typing with readonly properties
- **Distance Calculations**: Haversine formula with multiple Earth models
- **Performance**: Intelligent caching for trigonometric calculations
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

### Cache Management

```php
// Get cache statistics
$stats = Coordinates::getCacheStats();
echo "Hit ratio: " . round($stats->getHitRatio() * 100, 2) . "%";

// Clear cache
Coordinates::clearCache();
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

- **Intelligent Caching**: Trigonometric calculations cached automatically using HasCache trait
- **Early Returns**: Identical coordinates return 0 distance immediately
- **Batch Processing**: Efficient processing of multiple coordinate pairs
- **Memory Management**: LRU eviction prevents memory leaks
- **Instance Caching**: Uses HasCache trait for instance-level caching capabilities

## HasCache Trait Integration

The Coordinates class demonstrates how to use the HasCache trait for instance-level caching:

```php
$coordinates = Coordinates::create(40.7128, -74.0060);

// Get cached metadata (demonstrates trait usage)
$metadata = $coordinates->getCachedMetadata();
// Returns: ['latitude' => 40.7128, 'longitude' => -74.0060, 'computed_at' => ..., ...]

// Subsequent calls return cached result
$metadata2 = $coordinates->getCachedMetadata(); // Returns cached version
```
