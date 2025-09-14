<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Adapters;

use JeroenGerits\Support\Cache\Contracts\CacheAdapterInterface;
use JeroenGerits\Support\Cache\Contracts\CacheStatsInterface;
use JeroenGerits\Support\Cache\ValueObjects\CacheStats;

/**
 * Null cache adapter that performs no operations (useful for testing).
 */
class NullCacheAdapter implements CacheAdapterInterface
{
    public function __construct(
        private readonly string $namespace = 'default'
    ) {
        $this->validate();
    }

    /**
     * Get a value from the cache (always returns default).
     *
     * @param  string $key     The cache key
     * @param  mixed  $default Default value if key not found
     * @return mixed  Always returns the default value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $default;
    }

    /**
     * Set a value in the cache (no-op).
     *
     * @param  string                 $key   The cache key
     * @param  mixed                  $value The value to cache
     * @param  null|int|\DateInterval $ttl   Time to live
     * @return bool                   Always returns true
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        return true;
    }

    /**
     * Delete a value from the cache (no-op).
     *
     * @param  string $key The cache key
     * @return bool   Always returns false (nothing to delete)
     */
    public function delete(string $key): bool
    {
        return false;
    }

    /**
     * Clear all values from the cache (no-op).
     *
     * @return bool Always returns true
     */
    public function clear(): bool
    {
        return true;
    }

    /**
     * Get multiple values from the cache (always returns defaults).
     *
     * @param  iterable $keys    Array of keys to retrieve
     * @param  mixed    $default Default value for missing keys
     * @return iterable Associative array of key => default value pairs
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $default;
        }

        return $result;
    }

    /**
     * Set multiple values in the cache (no-op).
     *
     * @param  iterable               $values Associative array of key => value pairs
     * @param  null|int|\DateInterval $ttl    Time to live
     * @return bool                   Always returns true
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        return true;
    }

    /**
     * Delete multiple values from the cache (no-op).
     *
     * @param  iterable $keys Array of keys to delete
     * @return bool     Always returns true
     */
    public function deleteMultiple(iterable $keys): bool
    {
        return true;
    }

    /**
     * Check if a key exists in the cache (always returns false).
     *
     * @param  string $key The cache key
     * @return bool   Always returns false
     */
    public function has(string $key): bool
    {
        return false;
    }

    /**
     * Get cache statistics (always returns empty stats).
     *
     * @return CacheStatsInterface Empty cache statistics
     */
    public function getStats(): CacheStatsInterface
    {
        return new CacheStats(
            hits: 0,
            misses: 0,
            items: 0,
            maxItems: 0
        );
    }

    /**
     * Get the cache namespace.
     *
     * @return string The cache namespace
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Validate the cache configuration.
     */
    private function validate(): void
    {
        if ($this->namespace === '' || $this->namespace === '0') {
            throw new \InvalidArgumentException('Cache namespace cannot be empty');
        }
    }
}
