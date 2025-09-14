<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Contracts;

use JeroenGerits\Support\Cache\ValueObjects\CacheStats;
use Psr\SimpleCache\CacheInterface;

interface CacheAdapter extends CacheInterface
{
    /**
     * Get cache statistics.
     *
     * @return CacheStats Current cache statistics
     */
    public function getStats(): CacheStats;

    /**
     * Get the cache namespace/prefix.
     *
     * @return string The cache namespace
     */
    public function getNamespace(): string;
}
