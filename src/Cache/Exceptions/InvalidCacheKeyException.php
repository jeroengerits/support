<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Exceptions;

use Exception;

/**
 * Exception thrown when a cache key is invalid.
 */
class InvalidCacheKeyException extends CacheException
{
    /**
     * @param string         $message  The exception message
     * @param int            $code     The exception code
     * @param Exception|null $previous The previous exception for chaining
     */
    public function __construct(
        string $message = 'Invalid cache key provided',
        int $code = self::CODE_INVALID_KEY,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create an exception for empty cache keys.
     *
     * @return static A new InvalidCacheKeyException instance
     */
    public static function emptyKey(): static
    {
        return new static(
            'Cache key cannot be empty or null',
            self::CODE_INVALID_KEY
        );
    }

    /**
     * Create an exception for cache keys that are too long.
     *
     * @param  string $key       The cache key that is too long
     * @param  int    $maxLength The maximum allowed length
     * @return static A new InvalidCacheKeyException instance
     */
    public static function tooLong(string $key, int $maxLength): static
    {
        $actualLength = strlen($key);

        return new static(
            "Cache key is too long: {$actualLength} characters. Maximum allowed: {$maxLength} characters",
            self::CODE_INVALID_KEY
        );
    }

    /**
     * Create an exception for cache keys with invalid characters.
     *
     * @param  string $key          The cache key with invalid characters
     * @param  string $invalidChars The invalid characters found
     * @return static A new InvalidCacheKeyException instance
     */
    public static function invalidCharacters(string $key, string $invalidChars): static
    {
        return new static(
            "Cache key contains invalid characters: '{$invalidChars}'. ".
            'Cache keys should only contain alphanumeric characters, hyphens, underscores, and dots',
            self::CODE_INVALID_KEY
        );
    }

    /**
     * Create an exception for cache keys that are not strings.
     *
     * @param  mixed  $key The invalid key value that was provided
     * @return static A new InvalidCacheKeyException instance
     */
    public static function invalidType(mixed $key): static
    {
        $actualType = get_debug_type($key);

        return new static(
            "Invalid cache key type: {$actualType}. Expected: string",
            self::CODE_INVALID_KEY
        );
    }

    /**
     * Create an exception for cache keys that start with reserved prefixes.
     *
     * @param  string $key    The cache key that starts with a reserved prefix
     * @param  string $prefix The reserved prefix that was found
     * @return static A new InvalidCacheKeyException instance
     */
    public static function reservedPrefix(string $key, string $prefix): static
    {
        return new static(
            "Cache key '{$key}' starts with reserved prefix '{$prefix}'. ".
            'Reserved prefixes are not allowed for user-defined cache keys',
            self::CODE_INVALID_KEY
        );
    }

    /**
     * Create an exception for cache keys that contain only whitespace.
     *
     * @param  string $key The cache key that contains only whitespace
     * @return static A new InvalidCacheKeyException instance
     */
    public static function whitespaceOnly(string $key): static
    {
        return new static(
            "Cache key contains only whitespace characters: '{$key}'",
            self::CODE_INVALID_KEY
        );
    }
}
