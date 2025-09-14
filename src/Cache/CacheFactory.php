<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache;

use JeroenGerits\Support\Cache\Adapters\ArrayCacheAdapter;
use JeroenGerits\Support\Cache\Adapters\NullCacheAdapter;
use JeroenGerits\Support\Cache\Contracts\CacheAdapterInterface;

/**
 * Factory for creating cache adapters.
 */
class CacheFactory
{
    /**
     * Create a new array-based cache adapter.
     *
     * @param  string                $namespace The cache namespace
     * @param  int                   $maxItems  Maximum number of items to cache
     * @return CacheAdapterInterface The cache adapter
     */
    public static function createArrayCache(
        string $namespace = 'default',
        int $maxItems = 1000
    ): CacheAdapterInterface {
        return new ArrayCacheAdapter($namespace, $maxItems);
    }

    /**
     * Create a null cache adapter (no-op, useful for testing).
     *
     * @param  string                $namespace The cache namespace
     * @return CacheAdapterInterface The null cache adapter
     */
    public static function createNullCache(string $namespace = 'default'): CacheAdapterInterface
    {
        return new NullCacheAdapter($namespace);
    }
}
