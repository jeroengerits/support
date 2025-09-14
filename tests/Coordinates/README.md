# Coordinates

Provides a robust system for handling geographic coordinates with proper validation and type safety.

## Usage

The `coordinates`, `latitude` and `longitude` helper functions provides a convenient way to create coordinate value objects

```php
$coordinates = coordinates(52.3676, 4.9041); // Returns a CoordinateValue object
$latitude = latitude(52.3676); // Returns a LatitudeValue object
$longitude = longitude(52.3676); // Returns a LongitudeValue object
```

The `distanceBetweenCoordinates` helper function provides a convenient way to calculate distances between coordinates:

```php
$coordinates1 = coordinates(52.3676, 4.9041);
$coordinates2 = coordinates(51.5074, -0.1278);
$distance = distanceBetweenCoordinates($coordinates1, $coordinates2);
$distance = distanceBetweenCoordinates($coordinates1, $coordinates2, DistanceUnit::MILES);
```

All coordinate value objects implement the `Equatable` interface for proper equality comparison:

```php
$coordinates = coordinates(52.3676, 4.9041);
$coordinates->isEqual($coordinates); // true
```
