# HTTP Client

HTTP client abstraction with error handling and response management.

## Quick Start

```php
use JeroenGerits\Support\Http\Clients\Guzzle;

$httpClient = new Guzzle();

$response = $httpClient->get('https://api.example.com/data');
if ($response->isSuccessful()) {
    $data = $response->getJson();
    echo "Data: " . json_encode($data) . "\n";
}
```

## HTTP Methods

```php
// GET request
$response = $httpClient->get('https://api.example.com/data');

// POST request
$response = $httpClient->post('https://api.example.com/submit', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// PUT request
$response = $httpClient->put('https://api.example.com/users/123', [
    'name' => 'Jane Doe'
]);

// DELETE request
$response = $httpClient->delete('https://api.example.com/users/123');
```

## Custom Options

```php
$response = $httpClient->get('https://api.example.com/data', [
    'headers' => [
        'Authorization' => 'Bearer token123',
        'User-Agent' => 'MyApp/1.0'
    ],
    'timeout' => 60
]);
```

## Response Handling

```php
$response = $httpClient->get('https://api.example.com/data');

$statusCode = $response->getStatusCode();
$body = $response->getBody();
$json = $response->getJson();
$headers = $response->getHeaders();

echo "Status: {$statusCode}\n";
echo "Body: {$body}\n";
```

## Error Handling

```php
use JeroenGerits\Support\Http\Exceptions\HttpException;
use JeroenGerits\Support\Http\Exceptions\HttpTimeoutException;
use JeroenGerits\Support\Http\Exceptions\HttpConnectionException;

try {
    $response = $httpClient->get('https://api.example.com/data');
} catch (HttpTimeoutException $e) {
    echo "Request timed out: " . $e->getMessage() . "\n";
} catch (HttpConnectionException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch (HttpException $e) {
    echo "HTTP error: " . $e->getMessage() . "\n";
}
```

## API Reference

### Guzzle Client

| Method | Description |
|--------|-------------|
| `get($url, $options)` | GET request |
| `post($url, $options)` | POST request |
| `put($url, $options)` | PUT request |
| `delete($url, $options)` | DELETE request |

### HttpResponse

| Method | Description |
|--------|-------------|
| `getStatusCode()` | HTTP status code |
| `getBody()` | Response body |
| `getJson()` | Parsed JSON |
| `isSuccessful()` | Check if successful |