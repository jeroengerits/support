<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\ValueObjects;

use JeroenGerits\Support\Cache\Contracts\CacheStatsInterface;
use JeroenGerits\Support\Shared\Contracts\Equatable;

/**
 * Value object representing cache statistics.
 */
class CacheStats implements CacheStatsInterface, Equatable
{
    public function __construct(
        public readonly int $hits,
        public readonly int $misses,
        public readonly int $items,
        public readonly int $maxItems
    ) {
        $this->validate();
    }

    /**
     * Create cache statistics from individual values.
     *
     * @param  int  $hits     Number of cache hits
     * @param  int  $misses   Number of cache misses
     * @param  int  $items    Number of cached items
     * @param  int  $maxItems Maximum number of items
     * @return self The cache statistics instance
     */
    public static function create(int $hits, int $misses, int $items, int $maxItems): self
    {
        return new self($hits, $misses, $items, $maxItems);
    }

    /**
     * Get the number of cache hits.
     *
     * @return int Number of cache hits
     */
    public function getHits(): int
    {
        return $this->hits;
    }

    /**
     * Get the number of cache misses.
     *
     * @return int Number of cache misses
     */
    public function getMisses(): int
    {
        return $this->misses;
    }

    /**
     * Get the total number of cache requests (hits + misses).
     *
     * @return int Total number of requests
     */
    public function getTotalRequests(): int
    {
        return $this->hits + $this->misses;
    }

    /**
     * Get the cache hit ratio (hits / total requests).
     *
     * @return float Cache hit ratio between 0.0 and 1.0
     */
    public function getHitRatio(): float
    {
        $total = $this->getTotalRequests();

        return $total === 0 ? 0.0 : $this->hits / $total;
    }

    /**
     * Get the number of items currently in cache.
     *
     * @return int Number of cached items
     */
    public function getItems(): int
    {
        return $this->items;
    }

    /**
     * Get the maximum number of items the cache can hold.
     *
     * @return int Maximum number of items
     */
    public function getMaxItems(): int
    {
        return $this->maxItems;
    }

    /**
     * Get cache utilization (items / max items).
     *
     * @return float Cache utilization ratio between 0.0 and 1.0
     */
    public function getUtilization(): float
    {
        return $this->maxItems === 0 ? 0.0 : $this->items / $this->maxItems;
    }

    /**
     * Check if this cache statistics object is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the statistics are equal, false otherwise
     */
    public function isEqual(Equatable $other): bool
    {
        return $other instanceof self
            && $this->hits === $other->hits
            && $this->misses === $other->misses
            && $this->items === $other->items
            && $this->maxItems === $other->maxItems;
    }

    /**
     * Get the string representation of the cache statistics.
     *
     * @return string The statistics in a readable format
     */
    public function __toString(): string
    {
        $hitRatio = round($this->getHitRatio() * 100, 2);
        $utilization = round($this->getUtilization() * 100, 2);

        return "Cache Stats: {$this->hits} hits, {$this->misses} misses, {$hitRatio}% hit ratio, {$this->items}/{$this->maxItems} items ({$utilization}% utilization)";
    }

    /**
     * Validate the cache statistics.
     */
    private function validate(): void
    {
        if ($this->hits < 0) {
            throw new \InvalidArgumentException('Cache hits cannot be negative');
        }

        if ($this->misses < 0) {
            throw new \InvalidArgumentException('Cache misses cannot be negative');
        }

        if ($this->items < 0) {
            throw new \InvalidArgumentException('Cache items cannot be negative');
        }

        if ($this->maxItems < 0) {
            throw new \InvalidArgumentException('Cache max items cannot be negative');
        }

        if ($this->items > $this->maxItems) {
            throw new \InvalidArgumentException('Cache items cannot exceed max items');
        }
    }
}
