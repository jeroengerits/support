<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Adapters;

use DateInterval;
use InvalidArgumentException;
use JeroenGerits\Support\Cache\Contracts\CacheAdapter;
use JeroenGerits\Support\Cache\Exceptions\InvalidCacheKeyException;
use JeroenGerits\Support\Cache\ValueObjects\CacheKey;
use JeroenGerits\Support\Cache\ValueObjects\CacheStats;
use JeroenGerits\Support\Cache\ValueObjects\TimeToLive;

/**
 * In-memory array-based cache adapter with TTL support and LRU eviction.
 */
class ArrayCacheAdapter implements CacheAdapter
{
    /** @var array<string, array{value: mixed, expires: int}> */
    private array $cache = [];

    private int $hits = 0;

    private int $misses = 0;

    /**
     * @var callable(): int|null Optional time provider for testing
     */
    private $timeProvider;

    public function __construct(
        private readonly string $namespace = 'default',
        private readonly int $maxItems = 1000,
        ?callable $timeProvider = null
    ) {
        $this->timeProvider = $timeProvider;
        $this->validate();
    }

    /**
     * Validate the cache configuration.
     */
    private function validate(): void
    {
        if ($this->namespace === '' || $this->namespace === '0') {
            throw new InvalidArgumentException('Cache namespace cannot be empty');
        }

        if ($this->maxItems <= 0) {
            throw new InvalidArgumentException('Cache max items must be greater than 0');
        }
    }

    /**
     * Get the current time, using the time provider if available.
     */
    private function getCurrentTime(): int
    {
        return $this->timeProvider !== null ? ($this->timeProvider)() : time();
    }

    /**
     * Clear all values from the cache.
     *
     * @return bool True on success, false on failure
     */
    public function clear(): bool
    {
        $this->cache = [];
        $this->hits = 0;
        $this->misses = 0;

        return true;
    }

    /**
     * Get multiple values from the cache.
     *
     * @param  iterable $keys    Array of keys to retrieve
     * @param  mixed    $default Default value for missing keys
     * @return iterable Associative array of key => value pairs
     *
     * @throws InvalidCacheKeyException
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * Get a value from the cache.
     *
     * @param  string $key     The cache key
     * @param  mixed  $default Default value if key not found
     * @return mixed  The cached value or default
     *
     * @throws InvalidCacheKeyException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = CacheKey::create($key, $this->namespace);
        $fullKey = (string) $cacheKey;

        if (! isset($this->cache[$fullKey])) {
            $this->misses++;

            return $default;
        }

        $item = $this->cache[$fullKey];

        if ($this->isExpired($item['expires'])) {
            unset($this->cache[$fullKey]);
            $this->misses++;

            return $default;
        }

        $this->hits++;

        return $item['value'];
    }

    /**
     * Check if a cache item has expired.
     *
     * @param  int  $expires Expiration timestamp
     * @return bool True if expired, false otherwise
     */
    private function isExpired(int $expires): bool
    {
        return $expires > 0 && $this->getCurrentTime() > $expires;
    }

    /**
     * Set multiple values in the cache.
     *
     * @param  iterable              $values Associative array of key => value pairs
     * @param  null|int|DateInterval $ttl    Time to live
     * @return bool                  True on success, false on failure
     *
     * @throws InvalidCacheKeyException
     */
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        $success = true;

        foreach ($values as $key => $value) {
            if (! $this->set($key, $value, $ttl)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Set a value in the cache.
     *
     * @param  string                $key   The cache key
     * @param  mixed                 $value The value to cache
     * @param  null|int|DateInterval $ttl   Time to live
     * @return bool                  True on success, false on failure
     *
     * @throws InvalidCacheKeyException
     */
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $cacheKey = CacheKey::create($key, $this->namespace);
        $fullKey = (string) $cacheKey;

        $expires = $this->calculateExpiration($ttl);

        $this->cache[$fullKey] = [
            'value' => $value,
            'expires' => $expires,
        ];

        $this->evictIfNeeded();

        return true;
    }

    /**
     * Calculate expiration timestamp from TTL.
     *
     * @param  null|int|DateInterval $ttl Time to live
     * @return int                   Expiration timestamp
     */
    private function calculateExpiration(null|int|DateInterval $ttl): int
    {
        if ($ttl === null) {
            return TimeToLive::default()->seconds + $this->getCurrentTime();
        }

        if ($ttl instanceof DateInterval) {
            // Use proper DateTime calculations for accurate DateInterval conversion
            $now = new \DateTime;
            $expires = (clone $now)->add($ttl);

            return $expires->getTimestamp();
        }

        return $ttl + $this->getCurrentTime();
    }

    /**
     * Evict items if cache exceeds maximum size (simple LRU).
     */
    private function evictIfNeeded(): void
    {
        if (count($this->cache) <= $this->maxItems) {
            return;
        }

        // Simple LRU: remove oldest items
        $itemsToRemove = count($this->cache) - $this->maxItems;
        $keys = array_keys($this->cache);

        for ($i = 0; $i < $itemsToRemove; $i++) {
            unset($this->cache[$keys[$i]]);
        }
    }

    /**
     * Delete multiple values from the cache.
     *
     * @param  iterable $keys Array of keys to delete
     * @return bool     True on success, false on failure
     *
     * @throws InvalidCacheKeyException
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $success = true;

        foreach ($keys as $key) {
            if (! $this->delete($key)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Delete a value from the cache.
     *
     * @param  string $key The cache key
     * @return bool   True if the key was deleted, false if not found
     *
     * @throws InvalidCacheKeyException
     */
    public function delete(string $key): bool
    {
        $cacheKey = CacheKey::create($key, $this->namespace);
        $fullKey = (string) $cacheKey;

        if (isset($this->cache[$fullKey])) {
            unset($this->cache[$fullKey]);

            return true;
        }

        return false;
    }

    /**
     * Check if a key exists in the cache.
     *
     * @param  string $key The cache key
     * @return bool   True if key exists and is not expired, false otherwise
     *
     * @throws InvalidCacheKeyException
     */
    public function has(string $key): bool
    {
        $cacheKey = CacheKey::create($key, $this->namespace);
        $fullKey = (string) $cacheKey;

        if (! isset($this->cache[$fullKey])) {
            return false;
        }

        $item = $this->cache[$fullKey];

        return ! $this->isExpired($item['expires']);
    }

    /**
     * Get cache statistics.
     *
     * @return CacheStats Current cache statistics
     */
    public function getStats(): CacheStats
    {
        return new CacheStats(
            hits: $this->hits,
            misses: $this->misses,
            items: count($this->cache),
            maxItems: $this->maxItems
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
}
