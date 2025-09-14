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

// Longitute
$longitude = CoordinatesFactory::createLongitude(12.3);

// Longitute
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

### Equality Comparison

All coordinate value objects implement the `Equatable` interface for proper equality comparison:

```php
$coordinates1 = coordinates(52.3676, 4.9041);
$coordinates2 = coordinates(52.3676, 4.9041);

$coordinates1->isEqual($coordinates2); // true
```

### Exception Handling

The package provides specific exceptions for different types of validation errors:

#### InvalidLatitudeException
Thrown when a latitude value is outside the valid range (-90° to +90°):

```php
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLatitudeException;

try {
    $latitude = latitude(100.0); // Invalid latitude
} catch (InvalidLatitudeException $e) {
    echo $e->getMessage(); // "Latitude must be between -90 and 90"
}
```

#### InvalidLongitudeException
Thrown when a longitude value is outside the valid range (-180° to +180°):

```php
use JeroenGerits\Support\Coordinates\Exceptions\InvalidLongitudeException;

try {
    $longitude = longitude(200.0); // Invalid longitude
} catch (InvalidLongitudeException $e) {
    echo $e->getMessage(); // "Longitude must be between -180 and 180"
}
```

#### InvalidCoordinatesException
Thrown when coordinate data is invalid or missing:

```php
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;

try {
    $coordinates = coordinates(['invalid' => 'data']); // Missing lat/lng
} catch (InvalidCoordinatesException $e) {
    echo $e->getMessage(); // "Array must contain latitude and longitude values"
}
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

# Run helper functions tests
./vendor/bin/pest tests/HelpersTest.php

# Run exception tests
./vendor/bin/pest tests/Coordinates/Exceptions/
```

### Test Coverage

- **CoordinatesFactory**: 100% coverage
- **Helper Functions**: 100% coverage for all helper functions
- **Exception Classes**: 100% coverage for all custom exceptions
- **Value Objects**: Comprehensive validation testing
- **Edge Cases**: Boundary values, invalid inputs, type safety
- **Exception Handling**: Proper error handling for invalid inputs

## App Package

The App package provides a simple dependency injection container wrapper using League Container, allowing you to create
applications with service registration and automatic dependency injection.

### Basic Usage

```php
use JeroenGerits\Support\App\App;
use JeroenGerits\Support\App\Services\LoggerService;
use JeroenGerits\Support\App\Services\HelloWorldService;

// Create a new app
$app = App::new('My App');

// Register services
$app->addService(LoggerService::class);
$app->addService(HelloWorldService::class);

// Use the services
$logger = $app->get(LoggerService::class);
$helloService = $app->get(HelloWorldService::class);

// The HelloWorldService will automatically get the LoggerService injected
echo $helloService->sayHello('PHP Developer'); // Outputs: Hello, PHP Developer!
```

### Service Registration

#### Register by Class Name

```php
$app->addService(LoggerService::class);
$service = $app->get(LoggerService::class);
```

#### Register with Alias

```php
$app->addServiceWithAlias('logger', LoggerService::class);
$service = $app->get('logger');
```

#### Check if Service Exists

```php
if ($app->has('logger')) {
    $logger = $app->get('logger');
}
```

### Built-in Services

#### LoggerService

A simple logging service that outputs formatted messages to the console.

```php
$logger = $app->get(LoggerService::class);

$logger->info('Application started');
$logger->warning('This is a warning');
$logger->error('An error occurred');
$logger->debug('Debug information');
```

#### HelloWorldService

A demonstration service that shows dependency injection in action.

```php
$helloService = $app->get(HelloWorldService::class);

echo $helloService->sayHello('World'); // Hello, World!
echo $helloService->greet();           // Hello, World!
```

### Advanced Usage

#### Custom Service Providers

```php
use League\Container\ServiceProvider\ServiceProviderInterface;

class MyServiceProvider implements ServiceProviderInterface
{
    public function provides(string $id): bool
    {
        return $id === 'my-service';
    }

    public function register(): void
    {
        // Register your services here
    }
}

$app->addServiceProvider(new MyServiceProvider());
```

#### Service Configuration

```php
// Configure a service with specific settings
$app->addService(LoggerService::class)
    ->addArgument('custom-logger-name');
```

## Requirements

- PHP 8.4 or higher
- League Container ^5.0
- Pest PHP (for testing)

## License

MIT License. See [LICENSE](LICENSE) file for details.
