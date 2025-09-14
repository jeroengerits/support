# Examples

Complete usage examples and integration guides.

## Basic Examples

### **Simple Coordinate Calculation**

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

$amsterdam = Coordinates::create(52.3676, 4.9041);
$london = Coordinates::create(51.5074, -0.1278);

$distance = $amsterdam->distanceTo($london);
echo "Distance: {$distance} km\n";
```

### **Basic Geocoding**

```php
use JeroenGerits\Support\Geo\Clients\Nominatim;
use JeroenGerits\Support\Http\Clients\Guzzle;

$httpClient = new Guzzle();
$geocoding = new Nominatim($httpClient);

$coordinates = Coordinates::create(52.3676, 4.9041);
$location = $geocoding->reverseGeocode($coordinates);
echo "Location: {$location}\n";
```

### **Weather Information**

```php
use JeroenGerits\Support\Weather\Clients\OpenWeatherMap;

$weather = new OpenWeatherMap($httpClient, [
    'api_key' => 'your_api_key_here'
]);

$weatherInfo = $weather->getCurrentWeather($coordinates);
echo "Weather: {$weatherInfo}\n";
```

## Integration Examples

### **Multi-Service Workflow**

```php
use JeroenGerits\Support\Shared\Clients\Registry;
use JeroenGerits\Support\Shared\ValueObjects\Configuration;

$httpClient = new Guzzle();
$config = Configuration::fromEnvironment();
$registry = new Registry();

// Register services
$registry->register('http', function () {
    return new Guzzle();
});

$registry->register('geocoding', function () use ($registry) {
    $httpClient = $registry->get('http');
    return new Nominatim($httpClient);
});

$registry->register('weather', function () use ($registry) {
    $httpClient = $registry->get('http');
    return new OpenWeatherMap($httpClient, [
        'api_key' => 'your_api_key_here'
    ]);
});

// Use services
$coordinates = Coordinates::create(52.3676, 4.9041);

$geocoding = $registry->get('geocoding');
$location = $geocoding->reverseGeocode($coordinates);
echo "Location: {$location}\n";

$weather = $registry->get('weather');
$weatherInfo = $weather->getCurrentWeather($coordinates);
echo "Weather: {$weatherInfo}\n";
```

### **Error Handling with Retry**

```php
use JeroenGerits\Support\Shared\ValueObjects\RetryHelper;
use JeroenGerits\Support\Geo\Exceptions\GeocodingException;

try {
    $location = RetryHelper::retry(function () use ($geocoding, $coordinates) {
        return $geocoding->reverseGeocode($coordinates);
    }, maxAttempts: 3);
    
    echo "Location: {$location}\n";
} catch (GeocodingException $e) {
    echo "Geocoding failed: " . $e->getMessage() . "\n";
}
```

## Complete Applications

### **Location-Based Weather App**

```php
class WeatherApp
{
    public function __construct(
        private Registry $registry
    ) {}

    public function getWeatherForAddress(string $address): array
    {
        $geocoding = $this->registry->get('geocoding');
        $weather = $this->registry->get('weather');

        // Get coordinates from address
        $coordinates = $geocoding->geocode($address);
        if (!$coordinates) {
            throw new Exception("Address not found: {$address}");
        }

        // Get weather information
        $weatherInfo = $weather->getCurrentWeather($coordinates);

        return [
            'address' => $address,
            'coordinates' => $coordinates,
            'weather' => $weatherInfo
        ];
    }
}

// Use application
$app = new WeatherApp($registry);
$result = $app->getWeatherForAddress('Amsterdam, Netherlands');

echo "Address: {$result['address']}\n";
echo "Coordinates: {$result['coordinates']}\n";
echo "Weather: {$result['weather']}\n";
```

### **Distance Calculator**

```php
class DistanceCalculator
{
    public function __construct(
        private Nominatim $geocoding
    ) {}

    public function calculateDistanceBetweenAddresses(
        string $address1,
        string $address2
    ): array {
        $coordinates1 = $this->geocoding->geocode($address1);
        $coordinates2 = $this->geocoding->geocode($address2);

        if (!$coordinates1 || !$coordinates2) {
            throw new Exception("Could not find coordinates for one or both addresses");
        }

        $distance = $coordinates1->distanceTo($coordinates2);

        return [
            'address1' => $address1,
            'address2' => $address2,
            'distance' => $distance
        ];
    }
}

$calculator = new DistanceCalculator($geocoding);
$result = $calculator->calculateDistanceBetweenAddresses(
    'Amsterdam, Netherlands',
    'London, United Kingdom'
);

echo "Distance: {$result['distance']} km\n";
```

## Performance Examples

### **Batch Processing**

```php
$addresses = [
    'Amsterdam, Netherlands',
    'London, United Kingdom',
    'Paris, France',
    'Berlin, Germany'
];

$results = [];

foreach ($addresses as $address) {
    $coordinates = $geocoding->geocode($address);
    if ($coordinates) {
        $results[$address] = $coordinates;
        echo "Found: {$address} -> {$coordinates}\n";
    }
    
    // Rate limiting
    sleep(1);
}
```

### **Caching**

```php
class CachedGeocodingService
{
    private array $cache = [];

    public function reverseGeocode(Coordinates $coordinates): LocationInformation
    {
        $key = md5($coordinates->latitude . ',' . $coordinates->longitude);
        
        if (isset($this->cache[$key])) {
            echo "From cache: ";
            return $this->cache[$key];
        }
        
        echo "From API: ";
        $location = $this->geocoding->reverseGeocode($coordinates);
        $this->cache[$key] = $location;
        
        return $location;
    }
}
```

## Testing Examples

### **Unit Testing with Mocks**

```php
use Mockery;

// Mock HTTP client
$mockHttpClient = Mockery::mock(HttpClient::class);
$mockResponse = Mockery::mock(HttpResponse::class);

$mockResponse->shouldReceive('isSuccessful')->andReturn(true);
$mockResponse->shouldReceive('getJson')->andReturn([
    'display_name' => 'Amsterdam, Noord-Holland, Nederland'
]);

$mockHttpClient->shouldReceive('get')->andReturn($mockResponse);

// Test geocoding service
$geocoding = new Nominatim($mockHttpClient);
$coordinates = Coordinates::create(52.3676, 4.9041);
$location = $geocoding->reverseGeocode($coordinates);

expect($location->city)->toBe('Amsterdam');
```