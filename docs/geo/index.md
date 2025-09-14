# Geocoding

Convert between addresses and coordinates using external geocoding services.

## Quick Start

```php
use JeroenGerits\Support\Geo\Clients\Nominatim;
use JeroenGerits\Support\Http\Clients\Guzzle;

$httpClient = new Guzzle();
$geocoding = new Nominatim($httpClient);

// Reverse geocoding (coordinates to address)
$coordinates = Coordinates::create(52.3676, 4.9041);
$location = $geocoding->reverseGeocode($coordinates);
echo "Location: {$location}\n";

// Forward geocoding (address to coordinates)
$coordinates = $geocoding->geocode('Amsterdam, Netherlands');
if ($coordinates) {
    echo "Coordinates: {$coordinates}\n";
}
```

## Configuration

```bash
NOMINATIM_USER_AGENT="YourApp/1.0"
NOMINATIM_EMAIL="your@email.com"
NOMINATIM_TIMEOUT=30
```

## Location Information

```php
$location = $geocoding->reverseGeocode($coordinates);

echo "City: " . $location->city . "\n";
echo "State: " . $location->state . "\n";
echo "Country: " . $location->country . "\n";
echo "Postal Code: " . $location->postalCode . "\n";
```

## Error Handling

```php
use JeroenGerits\Support\Geo\Exceptions\GeocodingException;

try {
    $location = $geocoding->reverseGeocode($coordinates);
} catch (GeocodingException $e) {
    echo "Geocoding failed: " . $e->getMessage() . "\n";
}
```

## Service Management

```php
use JeroenGerits\Support\Shared\Clients\Registry;
use JeroenGerits\Support\Shared\Clients\ServiceFactory;

$registry = new Registry();

$registry->register('geocoding', function () use ($httpClient) {
    return ServiceFactory::createGeocodingClient('nominatim', [
        'httpClient' => $httpClient,
        'user_agent' => 'MyApp/1.0'
    ]);
});

$geocoding = $registry->get('geocoding');
```

## API Reference

### Nominatim Client

| Method | Description |
|--------|-------------|
| `reverseGeocode($coordinates)` | Convert coordinates to address |
| `geocode($address)` | Convert address to coordinates |
| `isAvailable()` | Check service availability |

### LocationInformation

| Property | Type | Description |
|----------|------|-------------|
| `city` | `string\|null` | City name |
| `state` | `string\|null` | State/province |
| `country` | `string\|null` | Country name |
| `postalCode` | `string\|null` | Postal code |