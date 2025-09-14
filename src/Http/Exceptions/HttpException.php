<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Http\Exceptions;

use Exception;

class HttpException extends Exception
{
    public const int CODE_TIMEOUT = 1001;

    public const int CODE_CONNECTION_ERROR = 1002;

    public const int CODE_INVALID_RESPONSE = 1003;

    public const int CODE_REQUEST_FAILED = 1004;

    public function __construct(
        string $message = 'HTTP request failed',
        int $code = self::CODE_REQUEST_FAILED,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function timeout(string $url, int $timeout): self
    {
        return new self(
            message: "HTTP request to '{$url}' timed out after {$timeout} seconds",
            code: self::CODE_TIMEOUT
        );
    }

    public static function connectionError(string $url, string $reason): self
    {
        return new self(
            message: "HTTP connection to '{$url}' failed: {$reason}",
            code: self::CODE_CONNECTION_ERROR
        );
    }

    public static function invalidResponse(string $url, int $statusCode): self
    {
        return new self(
            message: "Invalid HTTP response from '{$url}': {$statusCode}",
            code: self::CODE_INVALID_RESPONSE
        );
    }
}
