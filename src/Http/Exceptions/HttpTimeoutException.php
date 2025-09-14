<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Http\Exceptions;

class HttpTimeoutException extends HttpException
{
    public function __construct(string $url, int $timeout)
    {
        parent::__construct(
            message: "HTTP request to '{$url}' timed out after {$timeout} seconds",
            code: self::CODE_TIMEOUT
        );
    }
}
