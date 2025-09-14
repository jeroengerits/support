<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Weather\Exceptions;

use Exception;

class WeatherException extends Exception
{
    public const int CODE_SERVICE_UNAVAILABLE = 3001;

    public const int CODE_API_KEY_INVALID = 3002;

    public const int CODE_RATE_LIMIT_EXCEEDED = 3003;

    public const int CODE_INVALID_RESPONSE = 3004;

    public function __construct(
        string $message = 'Weather operation failed',
        int $code = self::CODE_SERVICE_UNAVAILABLE,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function serviceUnavailable(string $provider): self
    {
        return new self(
            message: "Weather service '{$provider}' is currently unavailable",
            code: self::CODE_SERVICE_UNAVAILABLE
        );
    }

    public static function apiKeyInvalid(string $provider): self
    {
        return new self(
            message: "Invalid API key for weather service '{$provider}'",
            code: self::CODE_API_KEY_INVALID
        );
    }

    public static function rateLimitExceeded(string $provider): self
    {
        return new self(
            message: "Rate limit exceeded for weather service '{$provider}'",
            code: self::CODE_RATE_LIMIT_EXCEEDED
        );
    }
}
