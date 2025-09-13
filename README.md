# Support

A PHP support package with various classes and helpers for my personal projects.

## Contracts

### `ValueObject`

A contract for immutable value objects that represent domain concepts.

```php
use JeroenGerits\Support\Contract\ValueObject;

class MyValueObject implements ValueObject
{
    public function equals(ValueObject $other): bool
    {
        // Implementation
    }
    
    public function toArray(): array
    {
        // Implementation
    }
    
    public function __toString(): string
    {
        // Implementation
    }
}
```

## Helpers

### `dd()`

A simple dump and die helper function for debugging.

```php
dd($variable); // Dumps variable and exits
dd($var1, $var2, $var3); // Dumps multiple variables
```

## Value Objects

### `Coordinates`

A value object representing geographic coordinates (latitude and longitude).

```php
use JeroenGerits\Support\Enum\DistanceUnit;
use JeroenGerits\Support\ValueObject\Coordinates;

// Create coordinates
$coordinates = Coordinates::fromFloats(40.7128, -74.0060); // New York
$coordinates = Coordinates::fromString('40.7128,-74.0060');
$coordinates = Coordinates::fromArray(['latitude' => 40.7128, 'longitude' => -74.0060]);

// Access components
$latitude = $coordinates->latitude();
$longitude = $coordinates->longitude();

// Geographic queries
$coordinates->isNorthern(); // true if latitude > 0
$coordinates->isSouthern(); // true if latitude < 0
$coordinates->isEastern();  // true if longitude > 0
$coordinates->isWestern();  // true if longitude < 0
$coordinates->isEquator();  // true if latitude == 0
$coordinates->isPrimeMeridian(); // true if longitude == 0

// Distance calculations
$ny = Coordinates::fromFloats(40.7128, -74.0060);
$london = Coordinates::fromFloats(51.5074, -0.1278);
$distanceKm = $ny->distanceTo($london);
$distanceMiles = $ny->distanceTo($london, DistanceUnit::MILES);

// Conversion
echo $coordinates; // "40.7128,-74.0060"
$array = $coordinates->toArray(); // ['latitude' => 40.7128, 'longitude' => -74.0060]
```

### `Latitude`

A value object representing latitude with validation (-90 to 90 degrees).

```php
use JeroenGerits\Support\ValueObject\Latitude;

// Create latitude
$latitude = new Latitude(40.7128);
$latitude = Latitude::fromString('40.7128');
$latitude = Latitude::fromFloat(40.7128);

// Access value
$value = $latitude->value(); // 40.7128

// Geographic queries
$latitude->isNorthern(); // true if > 0
$latitude->isSouthern(); // true if < 0
$latitude->isEquator();  // true if == 0

// Conversion
echo $latitude; // "40.7128"
$array = $latitude->toArray(); // ['latitude' => 40.7128]

// Validation
new Latitude(91.0); // Throws InvalidLatitudeException
```

### `Longitude`

A value object representing longitude with validation (-180 to 180 degrees).

```php
use JeroenGerits\Support\ValueObject\Longitude;

// Create longitude
$longitude = new Longitude(-74.0060);
$longitude = Longitude::fromString('-74.0060');
$longitude = Longitude::fromFloat(-74.0060);

// Access value
$value = $longitude->value(); // -74.0060

// Geographic queries
$longitude->isEastern(); // true if > 0
$longitude->isWestern(); // true if < 0
$longitude->isPrimeMeridian(); // true if == 0
$longitude->isInternationalDateLine(); // true if == 180 or -180

// Conversion
echo $longitude; // "-74.0060"
$array = $longitude->toArray(); // ['longitude' => -74.0060]

// Validation
new Longitude(181.0); // Throws InvalidLongitudeException
```

## Enums

### `DistanceUnit`

An enum for distance units used in coordinate calculations.

```php
use JeroenGerits\Support\Enum\DistanceUnit;

// Available units
DistanceUnit::KILOMETERS;     // 'km'
DistanceUnit::MILES;          // 'mi'
DistanceUnit::NAUTICAL_MILES; // 'nmi'
DistanceUnit::METERS;         // 'm'
DistanceUnit::MILLIMETERS;    // 'mm'
DistanceUnit::CENTIMETERS;    // 'cm'
DistanceUnit::DECIMETERS;     // 'dm'
DistanceUnit::INCHES;         // 'in'
DistanceUnit::FEET;           // 'ft'
DistanceUnit::YARDS;          // 'yd'
DistanceUnit::LIGHT_YEARS;    // 'ly'

// Methods
$unit = DistanceUnit::MILES;
$factor = $unit->getConversionFactor(); // 0.621371 (km to miles)
$name = $unit->getDisplayName();        // "miles"
$abbr = $unit->getAbbreviation();       // "mi"
```

## Exceptions

### `InvalidCoordinatesException`

Thrown when invalid coordinate data is provided (missing keys, invalid format).

### `InvalidLatitudeException`

Thrown when latitude values are outside the valid range (-90 to 90 degrees).

### `InvalidLongitudeException`

Thrown when longitude values are outside the valid range (-180 to 180 degrees).

### Examples

#### Distance Between Cities

```php
use JeroenGerits\Support\Enum\DistanceUnit;use JeroenGerits\Support\ValueObject\Coordinates;

$newYork = Coordinates::fromFloats(40.7128, -74.0060);
$london = Coordinates::fromFloats(51.5074, -0.1278);
$tokyo = Coordinates::fromFloats(35.6762, 139.6503);

$nyToLondon = $newYork->distanceTo($london); // ~5570 km
$nyToTokyo = $newYork->distanceTo($tokyo, DistanceUnit::MILES); // ~6755 miles
```

#### Coordinate Validation

```php
try {
    $coordinates = Coordinates::fromString('40.7128,-74.0060');
    echo "Valid coordinates: " . $coordinates;
} catch (InvalidCoordinatesException $e) {
    echo "Invalid coordinates: " . $e->getMessage();
}
```

#### Hemisphere Detection

```php
$coordinates = Coordinates::fromFloats(40.7128, -74.0060); // New York

if ($coordinates->isNorthern() && $coordinates->isWestern()) {
    echo "Located in the Northwestern hemisphere";
}
```

## Testing

```bash
composer test
```

## License

MIT License