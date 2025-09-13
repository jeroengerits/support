# Support

A PHP support package with utility classes and value objects for various projects.

## Coordinates Package

The Coordinates package provides a robust system for handling geographic coordinates with proper validation and type safety.

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
```

### Helper Function

A global helper function is available for quick coordinate creation:

```php
$coordinates = coordinates(52.3676, 4.9041);
```

### Equality Comparison

All coordinate value objects implement the `Equatable` interface for proper equality comparison:

```php
$coordinates1 = coordinates(52.3676, 4.9041);
$coordinates2 = coordinates(52.3676, 4.9041);

$coordinates1->isEqual($coordinates2); // true
```

## Testing

The package includes comprehensive test coverage using Pest PHP:

```bash
# Run all tests
./vendor/bin/pest

# Run tests with coverage
./vendor/bin/pest --coverage

# Run specific test file
./vendor/bin/pest tests/Coordinates/CoordinatesFactoryTest.php
```

### Test Coverage

- **CoordinatesFactory**: 100% coverage
- **Value Objects**: Comprehensive validation testing
- **Edge Cases**: Boundary values, invalid inputs, type safety
- **Exception Handling**: Proper error handling for invalid inputs

## Requirements

- PHP 8.4 or higher
- Pest PHP (for testing)

## License

MIT License. See [LICENSE](LICENSE) file for details.
