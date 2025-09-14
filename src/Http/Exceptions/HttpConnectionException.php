<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Http\Exceptions;

class HttpConnectionException extends HttpException
{
    public function __construct(string $url, string $reason)
    {
        parent::__construct(
            message: "HTTP connection to '{$url}' failed: {$reason}",
            code: self::CODE_CONNECTION_ERROR
        );
    }
}
