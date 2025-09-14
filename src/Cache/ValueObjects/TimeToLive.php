<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Cache\ValueObjects;

use JeroenGerits\Support\Cache\Exceptions\InvalidTimeToLiveException;
use JeroenGerits\Support\Shared\Contracts\Equatable;
use Stringable;

/**
 * Value object representing cache time-to-live (TTL).
 */
class TimeToLive implements Equatable, Stringable
{
    public const int DEFAULT_SECONDS = 3600; // 1 hour

    public const int SECONDS_PER_MINUTE = 60;

    public const int SECONDS_PER_HOUR = 3600;

    public const int SECONDS_PER_DAY = 86400;

    public function __construct(
        public readonly int $seconds
    ) {
        $this->validate();
    }

    /**
     * Validate the TTL value.
     *
     * @throws InvalidTimeToLiveException When the TTL is invalid
     */
    private function validate(): void
    {
        if ($this->seconds < 0) {
            throw InvalidTimeToLiveException::negativeValue($this->seconds);
        }
    }

    /**
     * Create a TTL from seconds.
     *
     * @param  int  $seconds Number of seconds
     * @return self The TTL instance
     */
    public static function fromSeconds(int $seconds): self
    {
        return new self($seconds);
    }

    /**
     * Create a TTL from minutes.
     *
     * @param  int  $minutes Number of minutes
     * @return self The TTL instance
     */
    public static function fromMinutes(int $minutes): self
    {
        return new self($minutes * self::SECONDS_PER_MINUTE);
    }

    /**
     * Create a TTL from hours.
     *
     * @param  int  $hours Number of hours
     * @return self The TTL instance
     */
    public static function fromHours(int $hours): self
    {
        return new self($hours * self::SECONDS_PER_HOUR);
    }

    /**
     * Create a TTL from days.
     *
     * @param  int  $days Number of days
     * @return self The TTL instance
     */
    public static function fromDays(int $days): self
    {
        return new self($days * self::SECONDS_PER_DAY);
    }

    /**
     * Create a TTL with default duration.
     *
     * @return self The TTL instance with default duration
     */
    public static function default(): self
    {
        return new self(self::DEFAULT_SECONDS);
    }

    /**
     * Check if this TTL is equal to another.
     *
     * @param  Equatable $other The other object to compare
     * @return bool      True if the TTL values are equal, false otherwise
     */
    public function isEqual(Equatable $other): bool
    {
        return $other instanceof self && $this->seconds === $other->seconds;
    }

    /**
     * Get the string representation of the TTL.
     *
     * @return string The TTL in "Xs" format
     */
    public function __toString(): string
    {
        return "{$this->seconds}s";
    }
}
