<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Contracts;

/**
 * Interface for cache statistics.
 */
interface CacheStatsInterface
{
    /**
     * Get the number of cache hits.
     *
     * @return int Number of cache hits
     */
    public function getHits(): int;

    /**
     * Get the number of cache misses.
     *
     * @return int Number of cache misses
     */
    public function getMisses(): int;

    /**
     * Get the total number of cache requests (hits + misses).
     *
     * @return int Total number of requests
     */
    public function getTotalRequests(): int;

    /**
     * Get the cache hit ratio (hits / total requests).
     *
     * @return float Cache hit ratio between 0.0 and 1.0
     */
    public function getHitRatio(): float;

    /**
     * Get the number of items currently in cache.
     *
     * @return int Number of cached items
     */
    public function getItems(): int;

    /**
     * Get the maximum number of items the cache can hold.
     *
     * @return int Maximum number of items
     */
    public function getMaxItems(): int;

    /**
     * Get cache utilization (items / max items).
     *
     * @return float Cache utilization ratio between 0.0 and 1.0
     */
    public function getUtilization(): float;
}
