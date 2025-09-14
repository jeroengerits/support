<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Shared\ValueObjects;

class RetryHelper
{
    public static function retry(callable $callback, int $maxAttempts = 3, float $baseDelay = 1.0): mixed
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxAttempts) {
            try {
                return $callback();
            } catch (\Exception $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt >= $maxAttempts) {
                    break;
                }

                $delay = $baseDelay * pow(2, $attempt - 1); // Exponential backoff
                usleep((int) ($delay * 1000000));
            }
        }

        throw $lastException;
    }
}
