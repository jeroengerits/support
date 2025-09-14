# Coordinates

A comprehensive PHP package for handling geographic coordinates with strict typing, robust validation, and high-performance distance calculations. Built with clean code principles and modern PHP 8.4+ features.

## Features

- **🔒 Strict Typing**: Full type safety with PHP 8.4+ strict types and readonly properties
- **🏗️ Value Objects**: Immutable `Coordinates`, `Latitude`, and `Longitude` objects with proper encapsulation
- **⚡ High Performance**: Optimized Haversine formula with intelligent caching for repeated calculations
- **🌍 Multiple Earth Models**: Support for spherical, WGS84, and GRS80 Earth models
- **📏 Distance Units**: Native support for kilometers and miles
- **✅ Robust Validation**: Automatic range validation with clear, descriptive error messages
- **🧪 Comprehensive Testing**: 100% test coverage with Pest PHP
- **📚 Clean Architecture**: Follows SOLID principles and clean code guidelines

## Quick Start

### Basic Usage

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

// Create coordinates (recommended approach)
$newYork = Coordinates::create(40.7128, -74.0060);
$london = Coordinates::create(51.5074, -0.1278);

// Calculate distance
$distance = $newYork->distanceTo($london); // 5570.9 km

// String representation
echo $newYork; // "40.7128,-74.0060"
```

### Creating Individual Components

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Latitude;
use JeroenGerits\Support\Coordinates\ValueObjects\Longitude;

// Using static factory methods (recommended)
$latitude = Latitude::create(40.7128);
$longitude = Longitude::create(-74.0060);

// Direct instantiation
$latitude = new Latitude(40.7128);
$longitude = new Longitude(-74.0060);

// Automatic validation
try {
    $invalidLatitude = Latitude::create(100.0); // Throws InvalidCoordinatesException
} catch (InvalidCoordinatesException $e) {
    echo $e->getMessage(); // "Latitude value 100 is outside the valid range of -90 to 90 degrees"
}
```

### Distance Calculations

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;

$amsterdam = Coordinates::create(52.3676, 4.9041);
$london = Coordinates::create(51.5074, -0.1278);

// Calculate distance in kilometers (default)
$distanceKm = $amsterdam->distanceTo($london); // 357.2 km

// Calculate distance in miles
$distanceMiles = $amsterdam->distanceTo($london, DistanceUnit::MILES); // 222.0 mi

// Using different Earth models for advanced calculations
$distanceWgs84 = $amsterdam->distanceTo($london, DistanceUnit::KILOMETERS, EarthModel::WGS84);
$distanceSpherical = $amsterdam->distanceTo($london, DistanceUnit::KILOMETERS, EarthModel::SPHERICAL);

// Batch calculations for multiple coordinate pairs
$coordinatePairs = [
    [$amsterdam, $london],
    [Coordinates::create(40.7128, -74.0060), Coordinates::create(34.0522, -118.2437)], // NY to LA
];

$distances = Coordinates::batchDistanceCalculation($coordinatePairs, DistanceUnit::KILOMETERS);
// Returns: [357.2, 3944.8] km
```

### Equality Comparison

```php
$coordinates1 = Coordinates::create(40.7128, -74.0060);
$coordinates2 = Coordinates::create(40.7128, -74.0060);
$coordinates3 = Coordinates::create(51.5074, -0.1278);

// Check if coordinates are equal
$isEqual = $coordinates1->isEqual($coordinates2); // true
$isDifferent = $coordinates1->isEqual($coordinates3); // false

// Type-safe comparison
$latitude = Latitude::create(40.7128);
$longitude = Longitude::create(-74.0060);
$isDifferentType = $latitude->isEqual($longitude); // false
```

### String Representation

```php
$coordinates = Coordinates::create(40.7128, -74.0060);

// Convert to string
echo $coordinates; // "40.7128,-74.0060"
echo $coordinates->latitude; // "40.7128"
echo $coordinates->longitude; // "-74.0060"

// Individual components
$latitude = Latitude::create(40.7128);
$longitude = Longitude::create(-74.0060);
echo $latitude->toString(); // "40.7128"
echo $longitude->toString(); // "-74.0060"
```

## Advanced Features

### Batch Distance Calculations

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Coordinates\Enums\DistanceUnit;
use JeroenGerits\Support\Coordinates\Enums\EarthModel;

$coordinatePairs = [
    [Coordinates::create(40.7128, -74.0060), Coordinates::create(51.5074, -0.1278)],
    [Coordinates::create(52.3676, 4.9041), Coordinates::create(48.8566, 2.3522)],
];

$distances = Coordinates::batchDistanceCalculation($coordinatePairs, DistanceUnit::KILOMETERS, EarthModel::WGS84);
```

### Cache Management

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

// Check cache size
$cacheSize = Coordinates::getCacheSize();

// Clear cache
Coordinates::clearCache();
```

## Error Handling

The package provides comprehensive error handling with descriptive messages and proper exception chaining:

```php
use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;

try {
    $latitude = new Latitude(100.0); // Invalid: latitude must be between -90 and 90
} catch (InvalidCoordinatesException $e) {
    echo $e->getMessage(); // "Latitude value 100 is outside the valid range of -90 to 90 degrees"
    echo $e->getCode(); // 1002 (CODE_OUT_OF_RANGE)
}

// Enhanced error messages with context
try {
    $longitude = new Longitude(200.0);
} catch (InvalidCoordinatesException $e) {
    echo $e->getMessage(); // "Longitude value 200 is outside the valid range of -180 to 180 degrees"
}

// Factory methods for common error scenarios
$exception = InvalidCoordinatesException::createOutOfRange(150.0, 'CustomCoordinate', -90.0, 90.0);
$exception = InvalidCoordinatesException::invalidType('invalid', 'latitude');
$exception = InvalidCoordinatesException::emptyValue('longitude');
```

## Clean Code Architecture

This package follows clean code principles and modern PHP best practices:

### Design Patterns

- **Value Objects**: Immutable objects representing domain concepts
- **Factory Pattern**: Static factory methods for object creation
- **Template Method**: Abstract base class for common coordinate functionality
- **Strategy Pattern**: Different Earth models for distance calculations

### Code Quality Features

- **Single Responsibility**: Each class has one clear purpose
- **Open/Closed Principle**: Extensible through inheritance and composition
- **Dependency Inversion**: Depends on abstractions, not concretions
- **DRY Principle**: Eliminated code duplication through inheritance
- **Meaningful Names**: Self-documenting code with descriptive method names
- **Small Functions**: Complex calculations broken into focused methods
- **Comprehensive Documentation**: PHPDoc with examples and type information

## Performance

- **⚡ Intelligent Caching**: Trigonometric calculations and Earth radius values are cached for optimal performance
- **🚀 Early Returns**: Identical coordinates return 0 distance immediately without calculation
- **🔧 Optimized Algorithms**: Efficient Haversine formula implementation with minimal overhead
- **💾 Memory Efficient**: Static caching with separate caches for different calculation types
- **📊 Batch Processing**: Process multiple coordinate pairs efficiently with `batchDistanceCalculation()`

### Cache Management

```php
// Check cache sizes
$trigCacheSize = Coordinates::getCacheSize();
$radiusCacheSize = Coordinates::getEarthRadiusCacheSize();

// Clear caches when needed
Coordinates::clearCache();
```

## Type Safety

This package enforces strict typing throughout:

- **🔒 Strict Types**: All files use `declare(strict_types=1)`
- **📝 Readonly Properties**: Immutable value objects with readonly properties
- **🎯 Type Hints**: Comprehensive type hints for all parameters and return values
- **⚡ Compile-time Safety**: Type checking prevents runtime errors
- **🔍 Static Analysis**: Compatible with PHPStan and other static analysis tools

## Refactoring Improvements

This package has been refactored following clean code principles:

### Code Quality Improvements

- **Eliminated Duplication**: Created `AbstractCoordinate` base class to remove code duplication
- **Improved Readability**: Broke down complex methods into smaller, focused functions
- **Better Naming**: Used descriptive names that explain intent and purpose
- **Enhanced Documentation**: Added comprehensive PHPDoc with examples
- **Exception Handling**: Improved error messages with context and helpful suggestions

### Architecture Improvements

- **Single Responsibility**: Each method has one clear purpose
- **Open/Closed Principle**: Easy to extend without modifying existing code
- **Dependency Inversion**: Depends on abstractions rather than concrete implementations
- **Template Method Pattern**: Common functionality extracted to base class
- **Factory Pattern**: Consistent object creation with validation
