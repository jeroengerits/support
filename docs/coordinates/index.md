# Coordinates

Geographic coordinate calculations with distance measurements and validation.

## Quick Start

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

$amsterdam = Coordinates::create(52.3676, 4.9041);
$london = Coordinates::create(51.5074, -0.1278);

$distance = $amsterdam->distanceTo($london);
echo "Distance: {$distance} km\n";
```

## Distance Units

```php
use JeroenGerits\Support\Coordinates\ValueObjects\DistanceUnit;

$distanceKm = $amsterdam->distanceTo($london, DistanceUnit::KILOMETERS);
$distanceMiles = $amsterdam->distanceTo($london, DistanceUnit::MILES);
$distanceNautical = $amsterdam->distanceTo($london, DistanceUnit::NAUTICAL_MILES);
```

## Earth Models

```php
use JeroenGerits\Support\Coordinates\ValueObjects\EarthModel;

$wgs84 = $amsterdam->distanceTo($london, DistanceUnit::KILOMETERS, EarthModel::WGS84);
$sphere = $amsterdam->distanceTo($london, DistanceUnit::KILOMETERS, EarthModel::SPHERE);
```

## Validation

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

// Valid coordinates
$lat = new Latitude(52.3676);   // -90 to 90
$lng = new Longitude(4.9041);   // -180 to 180

// Invalid coordinates throw InvalidCoordinatesException
$invalidLat = new Latitude(100.0); // Throws exception
```

## Batch Operations

```php
$coordinates = [
    Coordinates::create(52.3676, 4.9041),  // Amsterdam
    Coordinates::create(51.5074, -0.1278), // London
    Coordinates::create(48.8566, 2.3522),  // Paris
];

$distances = Coordinates::calculateDistances($coordinates);
```

## API Reference

### Coordinates

| Method | Description |
|--------|-------------|
| `create($lat, $lng)` | Create from latitude/longitude |
| `distanceTo($other, $unit, $model)` | Calculate distance |
| `calculateDistances($coordinates)` | Batch distance calculation |

### DistanceUnit

| Value | Description |
|-------|-------------|
| `KILOMETERS` | Kilometers (default) |
| `MILES` | Miles |
| `NAUTICAL_MILES` | Nautical miles |

### EarthModel

| Value | Description |
|-------|-------------|
| `WGS84` | World Geodetic System 1984 |
| `GRS80` | Geodetic Reference System 1980 |
| `SPHERE` | Perfect sphere approximation |