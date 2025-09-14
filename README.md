# Support

A PHP support package with utility classes and value objects for various projects.

## Coordinates

Provides a robust system for handling geographic coordinates with proper validation and type safety.

### Value Objects

Provides `Latitude`, `Longitude` and `Coordinates` value objects for handling geographic coordinates.

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;

$latitude = new Latitude(52.3676); // Valid latitude
$latitude = new Latitude(91.0);    // Throws Exception

$longitude = new Longitude(4.9041); // Valid longitude
$longitude = new Longitude(181.0);  // Throws Exception

$coordinates = new Coordinates(
    new Latitude(52.3676),
    new Longitude(4.9041)
);
```

### Factory

The `CoordinatesFactory` provides a convenient way to create coordinate instances from various input types.

```php
use JeroenGerits\Support\Coordinates\CoordinatesFactory;

// From floats
$coordinates = CoordinatesFactory::createCoordinates(52.3676, 4.9041);

// From strings
$coordinates = CoordinatesFactory::createCoordinates('52.3676', '4.9041');

// From integers
$coordinates = CoordinatesFactory::createCoordinates(52, 4);

// From existing value objects
$coordinates = CoordinatesFactory::createCoordinates($latitude, $longitude);

// Mixed types
$coordinates = CoordinatesFactory::createCoordinates('52.3676', 4.9041);

// Longitude
$longitude = CoordinatesFactory::createLongitude(12.3);

// Latitude
$latitude = CoordinatesFactory::createLatitude(22.3);
```

### Helper Functions

Global helper functions are available for quick coordinate and value object creation:

```php
$coordinates = coordinates(52.3676, 4.9041);

$latitude = latitude(52.3676);

$longitude = longitude(4.9041);

$coordinates = coordinates(
    latitude(52.3676),
    longitude(4.9041)
);
```

### Distance Calculation Helper

The `distanceBetweenCoordinates` helper function provides a convenient way to calculate distances between coordinates:

```php
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;

// Using Coordinates objects
$amsterdam = coordinates(52.3676, 4.9041);
$london = coordinates(51.5074, -0.1278);
$distance = distanceBetweenCoordinates($amsterdam, $london);
// Result: ~357 km

// Calculate in miles
$distance = distanceBetweenCoordinates($amsterdam, $london, DistanceUnit::MILES);
// Result: ~222 miles

// Identical coordinates return 0
$samePoint = coordinates(40.7128, -74.0060);
$zeroDistance = distanceBetweenCoordinates($samePoint, $samePoint);
// Result: 0.0 km
```

### Distance Calculation

The `CoordinatesCalculator` provides accurate distance calculations between coordinates using the Haversine formula:

```php
use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;

$amsterdam = coordinates(52.3676, 4.9041);
$london = coordinates(51.5074, -0.1278);

$distanceKm = CoordinatesCalculator::distanceBetween($amsterdam, $london);
// Result: ~357 km

$distanceMiles = CoordinatesCalculator::distanceBetween($amsterdam, $london, DistanceUnit::MILES);
// Result: ~222 miles
```

#### Supported Distance Units

- **KILOMETERS** (default): Earth radius = 6,371 km
- **MILES**: Earth radius = 3,958.8 miles

### Equality Comparison

All coordinate value objects implement the `Equatable` interface for proper equality comparison:

```php
$coordinates1 = coordinates(52.3676, 4.9041);
$coordinates2 = coordinates(52.3676, 4.9041);

$coordinates1->isEqual($coordinates2); // true
```
