<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Contracts;

interface CacheAdapterInterface extends \Psr\SimpleCache\CacheInterface
{
    /**
     * Get cache statistics.
     *
     * @return CacheStatsInterface Current cache statistics
     */
    public function getStats(): CacheStatsInterface;

    /**
     * Get the cache namespace/prefix.
     *
     * @return string The cache namespace
     */
    public function getNamespace(): string;
}
