# Geo Domain

The Geo domain provides geocoding functionality for converting between coordinates and addresses.

## Features

- **Reverse Geocoding**: Convert coordinates to location information
- **Forward Geocoding**: Convert addresses to coordinates
- **Multiple Providers**: Support for different geocoding services
- **Rich Location Data**: Detailed location information including timezone, confidence, etc.

## Available Clients

### Nominatim
- **Provider**: OpenStreetMap Nominatim
- **Free**: No API key required
- **Rate Limits**: 1 request per second
- **Features**: Reverse and forward geocoding

## Usage

```php
use JeroenGerits\Support\Geo\Clients\Nominatim;
use JeroenGerits\Support\Http\Clients\Guzzle;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

$httpClient = new Guzzle();
$geocoding = new Nominatim($httpClient);

$coordinates = Coordinates::create(52.3676, 4.9041);
$location = $geocoding->reverseGeocode($coordinates);

echo $location->getDisplayName(); // "Amsterdam, Netherlands"
echo $location->getCountryCode(); // "NL"
```

## Configuration

Set environment variables for Nominatim:

```bash
NOMINATIM_USER_AGENT="YourApp/1.0"
NOMINATIM_EMAIL="your@email.com"
NOMINATIM_TIMEOUT=30
```

## Value Objects

### LocationInformation
Represents location data with the following properties:
- `city`: City name
- `state`: State or province
- `country`: Country name
- `postalCode`: Postal/ZIP code
- `formattedAddress`: Full formatted address
- `timezone`: Timezone identifier
- `confidence`: Confidence score (0-1)
- `countryCode`: ISO country code

## Exceptions

- `GeocodingException`: Base exception for geocoding errors
- `GeocodingRateLimitException`: Rate limit exceeded
- `GeocodingInvalidResponseException`: Invalid API response
