# Support

A PHP support package with utility functions and classes.

## 🛠️ Helper functions

Simple, flexible helper functions

### `coordinates()` - Create coordinates from anything

```php
// Two numbers
$coords = coordinates(40.7128, -74.0060);        // New York
$coords = coordinates(51.5074, -0.1278);         // London

// Comma-separated string
$coords = coordinates('40.7128,-74.0060');

// Array
$coords = coordinates(['latitude' => 40.7128, 'longitude' => -74.0060]);

// Mixed types
$coords = coordinates('40.7128', -74.0060);      // string + float
$coords = coordinates(40.7128, '-74.0060');      // float + string
```

### `latitude()` - Create latitude from anything

```php
$lat = latitude(40.7128);                        // float
$lat = latitude('40.7128');                      // string
$lat = latitude(['latitude' => 40.7128]);        // array with key
$lat = latitude([40.7128]);                      // array with index
```

### `longitude()` - Create longitude from anything

```php
$lng = longitude(-74.0060);                      // float
$lng = longitude('-74.0060');                    // string
$lng = longitude(['longitude' => -74.0060]);     // array with key
$lng = longitude([-74.0060]);                    // array with index
```

### `dd()` - Debug helper

```php
dd($variable);                                    // Dump and die
dd($var1, $var2, $var3);                         // Multiple variables
```

## 📍 Working with Coordinates

```php
// Create coordinates
$ny = coordinates(40.7128, -74.0060);
$london = coordinates(51.5074, -0.1278);

// Get distance between cities
$distance = $ny->distanceTo($london);             // ~5570 km
$distance = $ny->distanceTo($london, DistanceUnit::MILES); // ~3461 miles

// Geographic queries
$ny->isNorthern();                                // true (latitude > 0)
$ny->isWestern();                                 // true (longitude < 0)
$ny->isEquator();                                 // false
```

## 🎯 Value Objects

### `Coordinates`

```php
use JeroenGerits\Support\ValueObject\Coordinates;
use JeroenGerits\Support\Enum\DistanceUnit;

$coords = Coordinates::fromFloats(40.7128, -74.0060);
$coords->latitude()->value();                     // 40.7128
$coords->longitude()->value();                    // -74.0060
$coords->distanceTo($other, DistanceUnit::MILES);
```

### `Latitude` & `Longitude`

```php
use JeroenGerits\Support\ValueObject\Latitude;
use JeroenGerits\Support\ValueObject\Longitude;

$lat = new Latitude(40.7128);                     // Validates -90 to 90
$lng = new Longitude(-74.0060);                   // Validates -180 to 180

$lat->isNorthern();                               // true
$lng->isWestern();                                // true
```

## 🧪 Testing

```bash
composer test
```

## 📄 License

MIT License