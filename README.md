# Support

A PHP support package with utility classes and value objects for various projects.

## Coordinates

Provides a robust system for handling geographic coordinates with proper validation and type safety.

### Value Objects

#### Latitude

Represents a latitude value with validation (-90° to +90°).

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;

$latitude = new Latitude(52.3676); // Valid latitude
$latitude = new Latitude(91.0);    // Throws Exception
```

#### Longitude

Represents a longitude value with validation (-180° to +180°).

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

$longitude = new Longitude(4.9041); // Valid longitude
$longitude = new Longitude(181.0);  // Throws Exception
```

#### Coordinates

Combines latitude and longitude into a coordinate pair.

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

$coordinates = new Coordinates(
    new Latitude(52.3676),
    new Longitude(4.9041)
);

echo $coordinates; // "52.3676,4.9041"
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
// Create coordinates
$coordinates = coordinates(52.3676, 4.9041);
$coordinates = coordinates('52.3676', '4.9041');
$coordinates = coordinates(['lat' => 52.3676, 'lng' => 4.9041]);

// Create individual value objects
$latitude = latitude(52.3676);
$latitude = latitude('52.3676');
$latitude = latitude(52);

$longitude = longitude(4.9041);
$longitude = longitude('4.9041');
$longitude = longitude(4);

// Fluent creation
$coordinates = coordinates(
    latitude(52.3676),
    longitude(4.9041)
);
```

### Distance Calculation

The `CoordinatesCalculator` provides accurate distance calculations between coordinates using the Haversine formula:

```php
use JeroenGerits\Support\Coordinates\CoordinatesCalculator;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;

// Calculate distance in kilometers (default)
$amsterdam = coordinates(52.3676, 4.9041);
$london = coordinates(51.5074, -0.1278);
$distanceKm = CoordinatesCalculator::distanceBetween($amsterdam, $london);
// Result: ~357 km

// Calculate distance in miles
$distanceMiles = CoordinatesCalculator::distanceBetween($amsterdam, $london, DistanceUnit::MILES);
// Result: ~222 miles

// Identical coordinates return 0
$samePoint = coordinates(40.7128, -74.0060);
$zeroDistance = CoordinatesCalculator::distanceBetween($samePoint, $samePoint);
// Result: 0.0
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
