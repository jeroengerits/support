<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Shared\Exceptions;

use Exception;

class ServiceException extends Exception
{
    public const int CODE_SERVICE_NOT_FOUND = 4001;

    public const int CODE_SERVICE_UNAVAILABLE = 4002;

    public const int CODE_INVALID_CONFIGURATION = 4003;

    public function __construct(
        string $message = 'Service operation failed',
        int $code = self::CODE_SERVICE_UNAVAILABLE,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function notFound(string $serviceName): self
    {
        return new self(
            message: "Service '{$serviceName}' not found",
            code: self::CODE_SERVICE_NOT_FOUND
        );
    }

    public static function unavailable(string $serviceName): self
    {
        return new self(
            message: "Service '{$serviceName}' is currently unavailable",
            code: self::CODE_SERVICE_UNAVAILABLE
        );
    }

    public static function invalidConfiguration(string $reason): self
    {
        return new self(
            message: "Invalid service configuration: {$reason}",
            code: self::CODE_INVALID_CONFIGURATION
        );
    }
}
