# HTTP Domain

The HTTP domain provides HTTP client functionality for making external API requests with comprehensive error handling and response management.

## Features

- **Multiple HTTP Methods**: GET, POST, PUT, DELETE
- **Response Handling**: Structured response objects with JSON parsing
- **Error Handling**: Specific HTTP exceptions with detailed error messages
- **Timeout Management**: Configurable request timeouts
- **Retry Logic**: Built-in retry mechanisms with exponential backoff
- **Multiple Implementations**: Guzzle and cURL support

## Available Clients

### Guzzle Client
- **Provider**: Guzzle HTTP Client
- **Features**: Full HTTP support, timeouts, retries, connection pooling
- **Dependencies**: guzzlehttp/guzzle ^7.0
- **Performance**: High-performance with connection reuse

### cURL Client (Coming Soon)
- **Provider**: PHP cURL extension
- **Features**: Lightweight HTTP client
- **Dependencies**: PHP cURL extension
- **Performance**: Minimal overhead, good for simple requests

## Basic Usage

```php
use JeroenGerits\Support\Http\Clients\Guzzle;
use JeroenGerits\Support\Http\Contracts\HttpClient;

$httpClient = new Guzzle();

// GET request
$response = $httpClient->get('https://api.example.com/data');

if ($response->isSuccessful()) {
    $data = $response->getJson();
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Data: " . json_encode($data) . "\n";
}
```

## Advanced Usage

### POST Request with JSON Data

```php
$response = $httpClient->post('https://api.example.com/submit', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

if ($response->isSuccessful()) {
    $result = $response->getJson();
    echo "Created: " . $result['id'] . "\n";
}
```

### Custom Headers and Options

```php
$response = $httpClient->get('https://api.example.com/data', [
    'headers' => [
        'Authorization' => 'Bearer token123',
        'User-Agent' => 'MyApp/1.0'
    ],
    'timeout' => 60
]);
```

### PUT and DELETE Requests

```php
// Update resource
$response = $httpClient->put('https://api.example.com/users/123', [
    'name' => 'Jane Doe',
    'email' => 'jane@example.com'
]);

// Delete resource
$response = $httpClient->delete('https://api.example.com/users/123');
```

### Custom HTTP Methods

```php
$response = $httpClient->request('PATCH', 'https://api.example.com/users/123', [
    'json' => ['status' => 'active']
]);
```

## Response Handling

### Response Object Methods

```php
$response = $httpClient->get('https://api.example.com/data');

// Status information
$statusCode = $response->getStatusCode();        // 200
$reasonPhrase = $response->getReasonPhrase();    // "OK"
$isSuccessful = $response->isSuccessful();       // true
$isClientError = $response->isClientError();     // false
$isServerError = $response->isServerError();     // false

// Response data
$body = $response->getBody();                    // Raw response body
$json = $response->getJson();                    // Parsed JSON array
$headers = $response->getHeaders();              // Response headers array
```

### Error Handling

```php
try {
    $response = $httpClient->get('https://api.example.com/data');
    
    if (!$response->isSuccessful()) {
        throw new Exception("HTTP Error: " . $response->getStatusCode());
    }
    
    $data = $response->getJson();
} catch (HttpTimeoutException $e) {
    echo "Request timed out: " . $e->getMessage();
} catch (HttpConnectionException $e) {
    echo "Connection failed: " . $e->getMessage();
} catch (HttpException $e) {
    echo "HTTP error: " . $e->getMessage();
}
```

## Configuration

### Environment Variables

```bash
# HTTP Client Configuration
HTTP_TIMEOUT=30              # Default timeout in seconds
HTTP_RETRY_ATTEMPTS=3        # Number of retry attempts
HTTP_CONNECT_TIMEOUT=10      # Connection timeout
HTTP_READ_TIMEOUT=30         # Read timeout
```

### Programmatic Configuration

```php
use JeroenGerits\Support\Shared\ValueObjects\Configuration;

$config = Configuration::fromEnvironment();

$httpClient = new Guzzle([], [
    'timeout' => $config->http['timeout'],
    'connect_timeout' => $config->http['connect_timeout'] ?? 10,
    'headers' => [
        'User-Agent' => 'MyApp/1.0'
    ]
]);
```

## Exception Hierarchy

### HttpException (Base Exception)
- `CODE_TIMEOUT = 1001`: Request timeout
- `CODE_CONNECTION_ERROR = 1002`: Connection failed
- `CODE_INVALID_RESPONSE = 1003`: Invalid response format
- `CODE_REQUEST_FAILED = 1004`: General request failure

### Specific Exceptions
- `HttpTimeoutException`: Request timeout with URL and timeout details
- `HttpConnectionException`: Connection failure with URL and reason

### Exception Factory Methods

```php
// Timeout exception
$exception = HttpException::timeout('https://api.example.com', 30);

// Connection error exception
$exception = HttpException::connectionError('https://api.example.com', 'DNS resolution failed');

// Invalid response exception
$exception = HttpException::invalidResponse('https://api.example.com', 500);
```

## Testing

The HTTP domain includes comprehensive test coverage:

```bash
composer test tests/Http/
```

### Test Examples

```php
// Mock HTTP client for testing
$mockClient = Mockery::mock(GuzzleClient::class);
$mockResponse = new Response(200, [], '{"success": true}');

$mockClient->shouldReceive('request')
    ->with('GET', 'https://api.example.com/test', [])
    ->andReturn($mockResponse);

$guzzle = new Guzzle($mockClient);
$response = $guzzle->get('https://api.example.com/test');

expect($response->isSuccessful())->toBeTrue();
```

## Performance Considerations

### Connection Pooling
The Guzzle client automatically handles connection pooling for better performance when making multiple requests to the same host.

### Timeout Configuration
- **Connect Timeout**: Time to establish connection
- **Read Timeout**: Time to read response data
- **Total Timeout**: Maximum time for entire request

### Retry Logic
Built-in retry mechanisms with exponential backoff help handle temporary network issues.

## Security Features

### Input Validation
All URLs and options are validated before making requests.

### Error Message Sanitization
Error messages don't expose sensitive information like API keys or internal details.

### HTTPS Enforcement
Recommend using HTTPS for all external API calls.

## Best Practices

1. **Use HTTPS**: Always use HTTPS for external API calls
2. **Set Timeouts**: Configure appropriate timeouts for your use case
3. **Handle Errors**: Always handle HTTP exceptions properly
4. **Validate Responses**: Check response status before processing data
5. **Use Retries**: Implement retry logic for transient failures
6. **Log Requests**: Log HTTP requests for debugging and monitoring

## Examples

### Complete API Integration Example

```php
use JeroenGerits\Support\Http\Clients\Guzzle;
use JeroenGerits\Support\Shared\ValueObjects\RetryHelper;

class ApiClient
{
    public function __construct(
        private HttpClient $httpClient
    ) {}

    public function fetchUserData(int $userId): array
    {
        return RetryHelper::retry(function () use ($userId) {
            $response = $this->httpClient->get("/api/users/{$userId}");
            
            if (!$response->isSuccessful()) {
                throw new Exception("Failed to fetch user data");
            }
            
            return $response->getJson();
        }, maxAttempts: 3);
    }
}
```
