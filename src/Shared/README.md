# Shared Domain

The Shared domain provides common utilities and services used across all domains.

## Features

- **Service Registry**: Register and retrieve services
- **Service Factory**: Create services dynamically
- **Configuration Management**: Environment-based configuration
- **Retry Helper**: Exponential backoff retry mechanism

## Components

### Registry
Central service registry for managing service instances.

```php
use JeroenGerits\Support\Shared\Clients\Registry;

$registry = new Registry();

$registry->register('geocoding', function () {
    return new Nominatim($httpClient);
});

$geocoding = $registry->get('geocoding');
```

### ServiceFactory
Factory for creating service instances dynamically.

```php
use JeroenGerits\Support\Shared\Clients\ServiceFactory;

$geocoding = ServiceFactory::createGeocodingClient('nominatim', $config);
$weather = ServiceFactory::createWeatherClient('openweathermap', $config);
```

### Configuration
Environment-based configuration management.

```php
use JeroenGerits\Support\Shared\ValueObjects\Configuration;

$config = Configuration::fromEnvironment();
$nominatimConfig = $config->geocoding['nominatim'];
```

### RetryHelper
Exponential backoff retry mechanism.

```php
use JeroenGerits\Support\Shared\ValueObjects\RetryHelper;

$result = RetryHelper::retry(function () {
    return $someOperation();
}, maxAttempts: 3, baseDelay: 1.0);
```

## Configuration

Set environment variables for shared services:

```bash
# Geocoding
NOMINATIM_USER_AGENT="YourApp/1.0"
NOMINATIM_EMAIL="your@email.com"
NOMINATIM_TIMEOUT=30

# Weather
OPENWEATHER_API_KEY="your_api_key_here"
OPENWEATHER_UNITS="metric"

# HTTP
HTTP_TIMEOUT=30
HTTP_RETRY_ATTEMPTS=3

# Cache
CACHE_TTL=3600
CACHE_ENABLED=true
```

## Exceptions

- `ServiceException`: Base exception for service errors
- `ConfigurationException`: Configuration errors
