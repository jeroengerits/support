<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\Exceptions;

use Exception;

/**
 * Exception thrown when a time-to-live value is invalid.
 */
class InvalidTimeToLiveException extends CacheException
{
    /**
     * @param string         $message  The exception message
     * @param int            $code     The exception code
     * @param Exception|null $previous The previous exception for chaining
     */
    public function __construct(
        string $message = 'Invalid time-to-live value provided',
        int $code = self::CODE_INVALID_TTL,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create an exception for negative time-to-live values.
     *
     * @param  int|float $ttl The negative TTL value that was provided
     * @return static    A new InvalidTimeToLiveException instance
     */
    public static function negativeValue(int|float $ttl): static
    {
        return new static(
            "Time-to-live cannot be negative: {$ttl}. Must be 0 or positive",
            self::CODE_INVALID_TTL
        );
    }

    /**
     * Create an exception for time-to-live values that are too large.
     *
     * @param  int|float $ttl    The TTL value that is too large
     * @param  int|float $maxTtl The maximum allowed TTL value
     * @return static    A new InvalidTimeToLiveException instance
     */
    public static function tooLarge(int|float $ttl, int|float $maxTtl): static
    {
        return new static(
            "Time-to-live is too large: {$ttl}. Maximum allowed: {$maxTtl}",
            self::CODE_INVALID_TTL
        );
    }

    /**
     * Create an exception for invalid time-to-live data types.
     *
     * @param  mixed  $ttl The invalid TTL value that was provided
     * @return static A new InvalidTimeToLiveException instance
     */
    public static function invalidType(mixed $ttl): static
    {
        $actualType = get_debug_type($ttl);

        return new static(
            "Invalid time-to-live type: {$actualType}. Expected: int or float",
            self::CODE_INVALID_TTL
        );
    }

    /**
     * Create an exception for non-numeric time-to-live values.
     *
     * @param  mixed  $ttl The non-numeric TTL value that was provided
     * @return static A new InvalidTimeToLiveException instance
     */
    public static function nonNumeric(mixed $ttl): static
    {
        $actualType = get_debug_type($ttl);

        return new static(
            "Time-to-live must be numeric: '{$ttl}' (type: {$actualType}). ".
            'Expected: int or float',
            self::CODE_INVALID_TTL
        );
    }

    /**
     * Create an exception for infinite time-to-live values when not allowed.
     *
     * @param  int|float $ttl The infinite TTL value that was provided
     * @return static    A new InvalidTimeToLiveException instance
     */
    public static function infiniteNotAllowed(int|float $ttl): static
    {
        return new static(
            "Infinite time-to-live is not allowed: {$ttl}. ".
            'Use a specific positive value or 0 for immediate expiration',
            self::CODE_INVALID_TTL
        );
    }

    /**
     * Create an exception for time-to-live values that are not integers when required.
     *
     * @param  float  $ttl The float TTL value that was provided
     * @return static A new InvalidTimeToLiveException instance
     */
    public static function mustBeInteger(float $ttl): static
    {
        return new static(
            "Time-to-live must be an integer: {$ttl}. ".
            'Fractional seconds are not supported',
            self::CODE_INVALID_TTL
        );
    }

    /**
     * Create an exception for time-to-live values outside valid range.
     *
     * @param  int|float $ttl    The TTL value that is out of range
     * @param  int|float $minTtl The minimum allowed TTL value
     * @param  int|float $maxTtl The maximum allowed TTL value
     * @return static    A new InvalidTimeToLiveException instance
     */
    public static function outOfRange(int|float $ttl, int|float $minTtl, int|float $maxTtl): static
    {
        return new static(
            "Time-to-live value {$ttl} is outside the valid range of {$minTtl} to {$maxTtl} seconds",
            self::CODE_INVALID_TTL
        );
    }
}
