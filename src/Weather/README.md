# Weather Domain

The Weather domain provides weather information for specific coordinates.

## Features

- **Current Weather**: Get current weather conditions
- **Rich Weather Data**: Temperature, humidity, pressure, wind, etc.
- **Multiple Providers**: Support for different weather services
- **Unit Conversion**: Automatic temperature and unit conversions

## Available Clients

### OpenWeatherMap
- **Provider**: OpenWeatherMap API
- **API Key**: Required
- **Rate Limits**: 1000 calls/day (free tier)
- **Features**: Current weather, forecasts, historical data

## Usage

```php
use JeroenGerits\Support\Weather\Clients\OpenWeatherMap;
use JeroenGerits\Support\Http\Clients\Guzzle;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

$httpClient = new Guzzle();
$weather = new OpenWeatherMap($httpClient, [
    'api_key' => 'your_api_key_here'
]);

$coordinates = Coordinates::create(52.3676, 4.9041);
$weatherInfo = $weather->getCurrentWeather($coordinates);

echo $weatherInfo; // "15.2°C, Clear sky"
echo $weatherInfo->getTemperatureInFahrenheit(); // 59.4
echo $weatherInfo->getWindDirectionName(); // "NW"
```

## Configuration

Set environment variables for OpenWeatherMap:

```bash
OPENWEATHER_API_KEY="your_api_key_here"
OPENWEATHER_UNITS="metric"  # metric, imperial, kelvin
```

## Value Objects

### WeatherInformation
Represents weather data with the following properties:
- `temperature`: Temperature in Celsius
- `description`: Weather description
- `humidity`: Humidity percentage
- `pressure`: Atmospheric pressure
- `windSpeed`: Wind speed
- `windDirection`: Wind direction in degrees
- `icon`: Weather icon code
- `location`: Location name
- `timestamp`: Observation timestamp

## Exceptions

- `WeatherException`: Base exception for weather errors
- `WeatherApiKeyException`: Invalid API key
- `WeatherRateLimitException`: Rate limit exceeded
