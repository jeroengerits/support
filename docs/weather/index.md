# Weather

Get weather information using external weather services.

## Quick Start

```php
use JeroenGerits\Support\Weather\Clients\OpenWeatherMap;
use JeroenGerits\Support\Http\Clients\Guzzle;

$httpClient = new Guzzle();
$weather = new OpenWeatherMap($httpClient, [
    'api_key' => 'your_api_key_here'
]);

$coordinates = Coordinates::create(52.3676, 4.9041);
$weatherInfo = $weather->getCurrentWeather($coordinates);
echo "Weather: {$weatherInfo}\n";
```

## Configuration

```bash
OPENWEATHER_API_KEY="your_api_key_here"
OPENWEATHER_UNITS="metric"
```

## Weather Information

```php
$weatherInfo = $weather->getCurrentWeather($coordinates);

echo "Temperature: {$weatherInfo->temperature}°C\n";
echo "Description: {$weatherInfo->description}\n";
echo "Humidity: {$weatherInfo->humidity}%\n";
echo "Pressure: {$weatherInfo->pressure} hPa\n";
echo "Wind: {$weatherInfo->windSpeed} m/s\n";
```

## Units

```php
// Metric (default)
$weather = new OpenWeatherMap($httpClient, [
    'api_key' => 'your_api_key',
    'units' => 'metric' // Celsius, m/s, hPa
]);

// Imperial
$weather = new OpenWeatherMap($httpClient, [
    'api_key' => 'your_api_key',
    'units' => 'imperial' // Fahrenheit, mph, inHg
]);
```

## Error Handling

```php
use JeroenGerits\Support\Weather\Exceptions\WeatherException;

try {
    $weatherInfo = $weather->getCurrentWeather($coordinates);
} catch (WeatherException $e) {
    echo "Weather failed: " . $e->getMessage() . "\n";
}
```

## Service Management

```php
use JeroenGerits\Support\Shared\Clients\Registry;
use JeroenGerits\Support\Shared\Clients\ServiceFactory;

$registry = new Registry();

$registry->register('weather', function () use ($httpClient) {
    return ServiceFactory::createWeatherClient('openweathermap', [
        'httpClient' => $httpClient,
        'api_key' => 'your_api_key_here'
    ]);
});

$weather = $registry->get('weather');
```

## API Reference

### OpenWeatherMap Client

| Method | Description |
|--------|-------------|
| `getCurrentWeather($coordinates)` | Get current weather |
| `isAvailable()` | Check service availability |

### WeatherInformation

| Property | Type | Description |
|----------|------|-------------|
| `temperature` | `float` | Temperature in configured units |
| `description` | `string` | Weather description |
| `humidity` | `int` | Humidity percentage |
| `pressure` | `float` | Atmospheric pressure |
| `windSpeed` | `float` | Wind speed |