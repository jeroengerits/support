<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Traits;

use DateInterval;
use JeroenGerits\Support\Cache\Contracts\CacheAdapter;

/**
 * Trait providing caching functionality to classes.
 *
 * This trait allows any class to easily integrate caching capabilities
 * with automatic key generation and TTL support.
 */
trait HasCache
{
    private ?CacheAdapter $cache = null;

    private ?string $cacheNamespace = null;

    private ?int $cacheDefaultTtl = null;

    /**
     * Get the current cache adapter.
     *
     * @return CacheAdapter|null The current cache adapter
     */
    public function getCache(): ?CacheAdapter
    {
        return $this->cache;
    }

    /**
     * Set the cache adapter for this instance.
     *
     * @param CacheAdapter|null $cache The cache adapter to use
     */
    public function setCache(?CacheAdapter $cache): static
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get the current cache namespace.
     *
     * @return string|null The current cache namespace
     */
    public function getCacheNamespace(): ?string
    {
        return $this->cacheNamespace;
    }

    /**
     * Set the cache namespace for this instance.
     *
     * @param string|null $namespace The cache namespace
     */
    public function setCacheNamespace(?string $namespace): static
    {
        $this->cacheNamespace = $namespace;

        return $this;
    }

    /**
     * Get the current default TTL.
     *
     * @return int|null The current default TTL in seconds
     */
    public function getCacheDefaultTtl(): ?int
    {
        return $this->cacheDefaultTtl;
    }

    /**
     * Set the default TTL for cache operations.
     *
     * @param int|null $ttl The default TTL in seconds
     */
    public function setCacheDefaultTtl(?int $ttl): static
    {
        $this->cacheDefaultTtl = $ttl;

        return $this;
    }

    /**
     * Public method for testing cache key generation.
     *
     * @param  string ...$parts The key parts to combine
     * @return string The generated cache key
     *
     * @internal This method is for testing purposes only
     */
    public function testGenerateCacheKey(string ...$parts): string
    {
        return $this->generateCacheKey(...$parts);
    }

    /**
     * Generate a cache key from the given parts.
     *
     * @param  string ...$parts The key parts to combine
     * @return string The generated cache key
     */
    protected function generateCacheKey(string ...$parts): string
    {
        if ($this->cacheNamespace === null) {
            throw new \RuntimeException('Cache namespace must be set before generating cache keys');
        }

        // Use underscores instead of colons to avoid invalid characters
        return $this->cacheNamespace.'_'.implode('_', $parts);
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  string                $key   The cache key
     * @param  mixed                 $value The value to cache
     * @param  null|int|DateInterval $ttl   Time to live
     * @return bool                  True on success, false on failure
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheSet(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        return $this->cacheSet($key, $value, $ttl);
    }

    /**
     * Store a value in the cache.
     *
     * @param  string                $key   The cache key
     * @param  mixed                 $value The value to cache
     * @param  null|int|DateInterval $ttl   Time to live (uses default if null)
     * @return bool                  True on success, false on failure
     */
    protected function cacheSet(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        $fullKey = $this->generateCacheKey($key);
        $effectiveTtl = $ttl ?? $this->cacheDefaultTtl;

        return $this->cache->set($fullKey, $value, $effectiveTtl);
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  string $key     The cache key
     * @param  mixed  $default Default value if key not found
     * @return mixed  The cached value or default
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheGet(string $key, mixed $default = null): mixed
    {
        return $this->cacheGet($key, $default);
    }

    /**
     * Retrieve a value from the cache.
     *
     * @param  string $key     The cache key
     * @param  mixed  $default Default value if key not found
     * @return mixed  The cached value or default
     */
    protected function cacheGet(string $key, mixed $default = null): mixed
    {
        if (! $this->isCacheEnabled()) {
            return $default;
        }

        $fullKey = $this->generateCacheKey($key);

        return $this->cache->get($fullKey, $default);
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  string $key The cache key
     * @return bool   True if key exists and is not expired, false otherwise
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheHas(string $key): bool
    {
        return $this->cacheHas($key);
    }

    /**
     * Check if a key exists in the cache.
     *
     * @param  string $key The cache key
     * @return bool   True if key exists and is not expired, false otherwise
     */
    protected function cacheHas(string $key): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        $fullKey = $this->generateCacheKey($key);

        return $this->cache->has($fullKey);
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  string $key The cache key
     * @return bool   True if the key was deleted, false if not found
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheDelete(string $key): bool
    {
        return $this->cacheDelete($key);
    }

    /**
     * Delete a value from the cache.
     *
     * @param  string $key The cache key
     * @return bool   True if the key was deleted, false if not found
     */
    protected function cacheDelete(string $key): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        $fullKey = $this->generateCacheKey($key);

        return $this->cache->delete($fullKey);
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  array<string, mixed>  $values Associative array of key => value pairs
     * @param  null|int|DateInterval $ttl    Time to live
     * @return bool                  True on success, false on failure
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheSetMultiple(array $values, null|int|DateInterval $ttl = null): bool
    {
        return $this->cacheSetMultiple($values, $ttl);
    }

    /**
     * Store multiple values in the cache.
     *
     * @param  array<string, mixed>  $values Associative array of key => value pairs
     * @param  null|int|DateInterval $ttl    Time to live (uses default if null)
     * @return bool                  True on success, false on failure
     */
    protected function cacheSetMultiple(array $values, null|int|DateInterval $ttl = null): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        $effectiveTtl = $ttl ?? $this->cacheDefaultTtl;
        $prefixedValues = [];

        foreach ($values as $key => $value) {
            $prefixedValues[$this->generateCacheKey($key)] = $value;
        }

        return $this->cache->setMultiple($prefixedValues, $effectiveTtl);
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  array<string>        $keys    Array of keys to retrieve
     * @param  mixed                $default Default value for missing keys
     * @return array<string, mixed> Associative array of key => value pairs
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheGetMultiple(array $keys, mixed $default = null): array
    {
        return $this->cacheGetMultiple($keys, $default);
    }

    /**
     * Retrieve multiple values from the cache.
     *
     * @param  array<string>        $keys    Array of keys to retrieve
     * @param  mixed                $default Default value for missing keys
     * @return array<string, mixed> Associative array of key => value pairs
     */
    protected function cacheGetMultiple(array $keys, mixed $default = null): array
    {
        if (! $this->isCacheEnabled()) {
            return array_fill_keys($keys, $default);
        }

        $prefixedKeys = array_map([$this, 'generateCacheKey'], $keys);
        $prefixedValues = $this->cache->getMultiple($prefixedKeys, $default);

        // Convert back to original keys
        $result = [];
        foreach ($keys as $key) {
            $prefixedKey = $this->generateCacheKey($key);
            $result[$key] = $prefixedValues[$prefixedKey] ?? $default;
        }

        return $result;
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  array<string> $keys Array of keys to delete
     * @return bool          True on success, false on failure
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheDeleteMultiple(array $keys): bool
    {
        return $this->cacheDeleteMultiple($keys);
    }

    /**
     * Delete multiple values from the cache.
     *
     * @param  array<string> $keys Array of keys to delete
     * @return bool          True on success, false on failure
     */
    protected function cacheDeleteMultiple(array $keys): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        $prefixedKeys = array_map([$this, 'generateCacheKey'], $keys);

        return $this->cache->deleteMultiple($prefixedKeys);
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  string                $key      The cache key
     * @param  callable              $callback The callback to execute if cache miss
     * @param  null|int|DateInterval $ttl      Time to live
     * @return mixed                 The cached or computed value
     *
     * @internal This method is for testing purposes only
     */
    public function testCacheRemember(string $key, callable $callback, null|int|DateInterval $ttl = null): mixed
    {
        return $this->cacheRemember($key, $callback, $ttl);
    }

    /**
     * Execute a callback with caching support.
     *
     * If the key exists in cache, return the cached value.
     * Otherwise, execute the callback, cache the result, and return it.
     *
     * @param  string                $key      The cache key
     * @param  callable              $callback The callback to execute if cache miss
     * @param  null|int|DateInterval $ttl      Time to live (uses default if null)
     * @return mixed                 The cached or computed value
     */
    protected function cacheRemember(string $key, callable $callback, null|int|DateInterval $ttl = null): mixed
    {
        if (! $this->isCacheEnabled()) {
            return $callback();
        }

        $fullKey = $this->generateCacheKey($key);
        $cached = $this->cache->get($fullKey);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $effectiveTtl = $ttl ?? $this->cacheDefaultTtl;
        $this->cache->set($fullKey, $value, $effectiveTtl);

        return $value;
    }

    /**
     * Public method for testing cache operations.
     *
     * @param  string                $key      The cache key
     * @param  callable              $callback The callback to execute
     * @param  null|int|DateInterval $ttl      Time to live
     * @return mixed                 The computed value
     *
     * @internal This method is for testing purposes only
     */
    public function testCachePut(string $key, callable $callback, null|int|DateInterval $ttl = null): mixed
    {
        return $this->cachePut($key, $callback, $ttl);
    }

    /**
     * Execute a callback and cache the result.
     *
     * Always executes the callback and caches the result.
     *
     * @param  string                $key      The cache key
     * @param  callable              $callback The callback to execute
     * @param  null|int|DateInterval $ttl      Time to live (uses default if null)
     * @return mixed                 The computed value
     */
    protected function cachePut(string $key, callable $callback, null|int|DateInterval $ttl = null): mixed
    {
        if (! $this->isCacheEnabled()) {
            return $callback();
        }

        $value = $callback();
        $effectiveTtl = $ttl ?? $this->cacheDefaultTtl;
        $this->cacheSet($key, $value, $effectiveTtl);

        return $value;
    }

    /**
     * Public method for testing cache statistics.
     *
     * @return \JeroenGerits\Support\Cache\ValueObjects\CacheStats|null Cache statistics or null if cache is disabled
     *
     * @internal This method is for testing purposes only
     */
    public function testGetCacheStats(): ?\JeroenGerits\Support\Cache\ValueObjects\CacheStats
    {
        return $this->getCacheStats();
    }

    /**
     * Get cache statistics for this instance.
     *
     * @return \JeroenGerits\Support\Cache\ValueObjects\CacheStats|null Cache statistics or null if cache is disabled
     */
    protected function getCacheStats(): ?\JeroenGerits\Support\Cache\ValueObjects\CacheStats
    {
        if (! $this->isCacheEnabled()) {
            return null;
        }

        return $this->cache->getStats();
    }

    /**
     * Clear all cache entries for this instance.
     *
     * @return bool True on success, false on failure
     */
    protected function cacheClear(): bool
    {
        if (! $this->isCacheEnabled()) {
            return false;
        }

        return $this->cache->clear();
    }

    /**
     * Check if caching is enabled.
     *
     * @return bool True if cache is available and namespace is set
     */
    public function isCacheEnabled(): bool
    {
        return $this->cache !== null && $this->cacheNamespace !== null;
    }
}
