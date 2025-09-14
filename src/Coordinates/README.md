# Coordinates Package

Geographic coordinate handling with strict typing and high-performance distance calculations.

## Quick Start

```php
use JeroenGerits\Support\Coordinates\Coordinates;
use JeroenGerits\Support\Coordinates\DistanceUnit;

// Create coordinates
$ny = Coordinates::create(40.7128, -74.0060);
$london = Coordinates::create(51.5074, -0.1278);

// Calculate distance
$distance = $ny->distanceTo($london); // 5570.9 km
$distanceMiles = $ny->distanceTo($london, DistanceUnit::MILES); // 3459.0 mi
```

## Features

- **Type Safety**: Strict typing with readonly properties
- **Distance Calculations**: Haversine formula with multiple Earth models
- **Batch Processing**: Efficient processing of multiple coordinate pairs
- **Validation**: Automatic range validation with clear error messages

## Usage Examples

### Individual Components

```php
use JeroenGerits\Support\Coordinates\Latitude;
use JeroenGerits\Support\Coordinates\Longitude;

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

### Error Handling

```php
use JeroenGerits\Support\Coordinates\InvalidCoordinatesException;

try {
    $lat = Latitude::create(100.0); // Invalid: outside -90 to 90 range
} catch (InvalidCoordinatesException $e) {
    echo $e->getMessage(); // Clear error message with context
}
```

