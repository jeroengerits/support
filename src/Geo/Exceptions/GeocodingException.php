<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Geo\Exceptions;

use Exception;

class GeocodingException extends Exception
{
    public const int CODE_SERVICE_UNAVAILABLE = 2001;

    public const int CODE_RATE_LIMIT_EXCEEDED = 2002;

    public const int CODE_INVALID_RESPONSE = 2003;

    public const int CODE_INVALID_COORDINATES = 2004;

    public function __construct(
        string $message = 'Geocoding operation failed',
        int $code = self::CODE_SERVICE_UNAVAILABLE,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function serviceUnavailable(string $provider): self
    {
        return new self(
            message: "Geocoding service '{$provider}' is currently unavailable",
            code: self::CODE_SERVICE_UNAVAILABLE
        );
    }

    public static function rateLimitExceeded(string $provider): self
    {
        return new self(
            message: "Rate limit exceeded for geocoding service '{$provider}'",
            code: self::CODE_RATE_LIMIT_EXCEEDED
        );
    }

    public static function invalidResponse(string $provider, string $reason): self
    {
        return new self(
            message: "Invalid response from geocoding service '{$provider}': {$reason}",
            code: self::CODE_INVALID_RESPONSE
        );
    }
}
