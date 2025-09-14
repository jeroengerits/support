<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\ValueObjects;

use JeroenGerits\Support\Cache\Exceptions\InvalidCacheKeyException;
use JeroenGerits\Support\Shared\Contracts\Equatable;
use Stringable;

/**
 * Value object representing a cache key with namespace.
 */
class CacheKey implements Equatable, Stringable
{
    public function __construct(
        public readonly string $key,
        public readonly string $namespace = 'default'
    ) {
        $this->validate();
    }

    /**
     * Create a new cache key instance.
     *
     * @param  string $key       The cache key
     * @param  string $namespace The cache namespace
     * @return self   The new cache key instance
     *
     * @throws InvalidCacheKeyException When the key is invalid
     */
    public static function create(string $key, string $namespace = 'default'): self
    {
        return new self($key, $namespace);
    }

    /**
     * Check if this cache key is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the cache keys are equal, false otherwise
     */
    public function isEqual(Equatable $other): bool
    {
        return $other instanceof self
            && $this->key === $other->key
            && $this->namespace === $other->namespace;
    }

    /**
     * Get the string representation of the cache key.
     *
     * @return string The cache key in "namespace:key" format
     */
    public function __toString(): string
    {
        return "{$this->namespace}:{$this->key}";
    }

    /**
     * Validate the cache key.
     *
     * @throws InvalidCacheKeyException When the key is invalid
     */
    private function validate(): void
    {
        if ($this->key === '' || $this->key === '0') {
            throw new InvalidCacheKeyException('Cache key cannot be empty');
        }

        if (str_contains($this->key, ':')) {
            throw new InvalidCacheKeyException('Cache key cannot contain colons');
        }

        if ($this->namespace === '' || $this->namespace === '0') {
            throw new InvalidCacheKeyException('Cache namespace cannot be empty');
        }

        if (str_contains($this->namespace, ':')) {
            throw new InvalidCacheKeyException('Cache namespace cannot contain colons');
        }
    }
}
