# Cache Package

Clean, PSR-16 compliant caching solution with TTL support and comprehensive statistics.

## Features

- **PSR-16 Compliant**: Standard cache interface
- **TTL Support**: Automatic expiration with configurable time-to-live and accurate DateInterval handling
- **Statistics**: Built-in performance monitoring
- **Memory Management**: LRU eviction prevents memory leaks
- **Optimized Performance**: Efficient `has()` method and proper DateTime calculations
- **Testable**: Dependency injection for easy mocking

## Quick Start

```php
use JeroenGerits\Support\Cache\CacheFactory;
use JeroenGerits\Support\Cache\ValueObjects\TimeToLive;

// Create cache
$cache = CacheFactory::createArrayCache('my-app', 1000);

// Store with TTL
$cache->set('user:123', ['name' => 'John'], TimeToLive::fromHours(1)->seconds);

// Retrieve
$user = $cache->get('user:123'); // Returns user data or null if expired

// Check existence
if ($cache->has('user:123')) {
    echo "User is cached";
}
```

## Multiple Operations

```php
// Store multiple values
$cache->setMultiple([
    'user:1' => ['name' => 'Alice'],
    'user:2' => ['name' => 'Bob'],
], TimeToLive::fromMinutes(30)->seconds);

// Retrieve multiple values
$users = $cache->getMultiple(['user:1', 'user:2'], []);
```

## Cache Statistics

```php
$stats = $cache->getStats();

echo "Hit ratio: " . round($stats->getHitRatio() * 100, 2) . "%\n";
echo "Cached items: " . $stats->getItems() . "/" . $stats->getMaxItems() . "\n";

// String representation
echo $stats; // "Cache Stats: 150 hits, 25 misses, 85.7% hit ratio, 175/1000 items (17.5% utilization)"
```

## Time-to-Live (TTL)

```php
use JeroenGerits\Support\Cache\ValueObjects\TimeToLive;
use DateInterval;

// Different TTL formats
$ttl = TimeToLive::fromSeconds(60);
$ttl = TimeToLive::fromMinutes(5);
$ttl = TimeToLive::fromHours(2);
$ttl = TimeToLive::fromDays(1);
$ttl = TimeToLive::default(); // 1 hour

// Use in cache operations
$cache->set('key', 'value', $ttl->seconds);

// Support for DateInterval (accurate calculations)
$interval = new DateInterval('PT2H30M'); // 2 hours 30 minutes
$cache->set('key', 'value', $interval);
```

## Testing

```php
// Use null cache for testing (no-op operations)
$testCache = CacheFactory::createNullCache('test');
$yourClass->setCache($testCache);

// Use small cache for unit tests
$testCache = CacheFactory::createArrayCache('test', 10);
```

## Integration with Coordinates

```php
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

// Coordinates automatically uses cache for performance
$ny = Coordinates::create(40.7128, -74.0060);
$london = Coordinates::create(51.5074, -0.1278);
$distance = $ny->distanceTo($london); // Uses cached trigonometric calculations

// View cache performance
$stats = Coordinates::getCacheStats();
echo "Cache performance: " . $stats->getHitRatio() * 100 . "% hit ratio";
```

## Cache Adapters

### ArrayCacheAdapter (Default)

In-memory cache with TTL and LRU eviction:

```php
$cache = CacheFactory::createArrayCache(
    namespace: 'my-app',
    maxItems: 1000
);
```

**Performance Features:**

- Optimized `has()` method for efficient existence checks
- Accurate DateInterval to timestamp conversion using DateTime
- Efficient memory management with LRU eviction

### NullCacheAdapter

No-op cache for testing:

```php
$cache = CacheFactory::createNullCache('test');
// All operations are no-ops
```

## HasCache Trait

The `HasCache` trait provides a clean interface for adding caching capabilities to any class:

```php
use JeroenGerits\Support\Cache\Traits\HasCache;
use JeroenGerits\Support\Cache\CacheFactory;

class MyService
{
    use HasCache;

    public function __construct()
    {
        $this->setCache(CacheFactory::createArrayCache('my-service', 1000))
             ->setCacheNamespace('my-service')
             ->setCacheDefaultTtl(3600); // 1 hour
    }

    public function getExpensiveData(string $id): array
    {
        return $this->cacheRemember("data_{$id}", function () use ($id) {
            // Expensive operation here
            return $this->fetchDataFromDatabase($id);
        });
    }

    public function updateAndCache(string $id, array $data): array
    {
        return $this->cachePut("data_{$id}", function () use ($id, $data) {
            $this->saveToDatabase($id, $data);
            return $data;
        });
    }
}
```

### Trait Methods

- `cacheRemember($key, $callback, $ttl)` - Execute callback if cache miss, cache and return result
- `cachePut($key, $callback, $ttl)` - Always execute callback and cache result
- `cacheGet($key, $default)` - Retrieve cached value
- `cacheSet($key, $value, $ttl)` - Store value in cache
- `cacheHas($key)` - Check if key exists
- `cacheDelete($key)` - Remove key from cache
- `generateCacheKey(...$parts)` - Generate namespaced cache key

## Best Practices

1. **Use Namespaces**: Organize cache keys with meaningful namespaces
2. **Set Appropriate TTL**: Don't cache frequently changing data
3. **Monitor Statistics**: Use cache stats to optimize performance
4. **Handle Cache Misses**: Always handle null returns from cache operations
5. **Test with Null Cache**: Use null cache adapter in tests to avoid side effects
6. **Use HasCache Trait**: Leverage the trait for clean caching integration
