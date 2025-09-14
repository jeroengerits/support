# Shared Utilities

Common utilities, configuration management, and service registry.

## Quick Start

```php
use JeroenGerits\Support\Shared\Clients\Registry;
use JeroenGerits\Support\Shared\ValueObjects\Configuration;
use JeroenGerits\Support\Shared\ValueObjects\RetryHelper;

// Load configuration
$config = Configuration::fromEnvironment();

// Create service registry
$registry = new Registry();

// Register services
$registry->register('http', function () {
    return new Guzzle();
});

// Use retry helper
$result = RetryHelper::retry(function () {
    return $someOperation();
});
```

## Service Registry

```php
$registry = new Registry();

// Register a service
$registry->register('my-service', function () {
    return new MyService();
});

// Retrieve a service
$service = $registry->get('my-service');

// Check if service exists
if ($registry->has('my-service')) {
    echo "Service exists\n";
}
```

## Configuration

```php
use JeroenGerits\Support\Shared\ValueObjects\Configuration;

// Load from environment
$config = Configuration::fromEnvironment();

// Access configuration
$nominatimConfig = $config->geocoding['nominatim'];
$openweatherConfig = $config->weather['openweathermap'];
$httpConfig = $config->http;
```

## Retry Helper

```php
use JeroenGerits\Support\Shared\ValueObjects\RetryHelper;

// Basic retry
$result = RetryHelper::retry(function () {
    return $someOperation();
});

// With custom parameters
$result = RetryHelper::retry(function () {
    return $someOperation();
}, maxAttempts: 5, baseDelay: 2.0);
```

## Error Handling

```php
use JeroenGerits\Support\Shared\Exceptions\ServiceException;

// Basic exception
$exception = new ServiceException('Service operation failed');

// Exception factory methods
$exception = ServiceException::notFound('my-service');
$exception = ServiceException::unavailable('my-service');
$exception = ServiceException::invalidConfiguration('Missing required parameter');
```

## API Reference

### Registry

| Method | Description |
|--------|-------------|
| `register($name, $factory)` | Register service |
| `get($name)` | Get service |
| `has($name)` | Check if exists |

### ServiceFactory

| Method | Description |
|--------|-------------|
| `createGeocodingClient($provider, $config)` | Create geocoding client |
| `createWeatherClient($provider, $config)` | Create weather client |

### Configuration

| Method | Description |
|--------|-------------|
| `fromEnvironment()` | Load from environment |

### RetryHelper

| Method | Description |
|--------|-------------|
| `retry($callback, $maxAttempts, $baseDelay)` | Retry operation |