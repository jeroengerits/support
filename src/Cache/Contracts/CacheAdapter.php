<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Contracts;

interface CacheAdapter extends \Psr\SimpleCache\CacheInterface
{
    /**
     * Get cache statistics.
     *
     * @return \JeroenGerits\Support\Cache\ValueObjects\CacheStats Current cache statistics
     */
    public function getStats(): \JeroenGerits\Support\Cache\ValueObjects\CacheStats;

    /**
     * Get the cache namespace/prefix.
     *
     * @return string The cache namespace
     */
    public function getNamespace(): string;
}
