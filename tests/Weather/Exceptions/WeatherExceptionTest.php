<?php

declare(strict_types=1);

use JeroenGerits\Support\Weather\Exceptions\WeatherException;

describe('Weather Domain', function (): void {
    describe('WeatherException', function (): void {
        it('creates with default values', function (): void {
            $exception = new WeatherException;

            expect($exception->getMessage())->toBe('Weather operation failed')
                ->and($exception->getCode())->toBe(WeatherException::CODE_SERVICE_UNAVAILABLE);
        });

        it('creates with custom message and code', function (): void {
            $exception = new WeatherException('Custom error', WeatherException::CODE_API_KEY_INVALID);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(WeatherException::CODE_API_KEY_INVALID);
        });

        it('creates with previous exception', function (): void {
            $previous = new Exception('Previous error');
            $exception = new WeatherException('Custom error', WeatherException::CODE_RATE_LIMIT_EXCEEDED, $previous);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(WeatherException::CODE_RATE_LIMIT_EXCEEDED)
                ->and($exception->getPrevious())->toBe($previous);
        });

        it('creates service unavailable exception', function (): void {
            $exception = WeatherException::serviceUnavailable('OpenWeatherMap');

            expect($exception->getMessage())->toBe('Weather service \'OpenWeatherMap\' is currently unavailable')
                ->and($exception->getCode())->toBe(WeatherException::CODE_SERVICE_UNAVAILABLE);
        });

        it('creates API key invalid exception', function (): void {
            $exception = WeatherException::apiKeyInvalid('OpenWeatherMap');

            expect($exception->getMessage())->toBe('Invalid API key for weather service \'OpenWeatherMap\'')
                ->and($exception->getCode())->toBe(WeatherException::CODE_API_KEY_INVALID);
        });

        it('creates rate limit exceeded exception', function (): void {
            $exception = WeatherException::rateLimitExceeded('OpenWeatherMap');

            expect($exception->getMessage())->toBe('Rate limit exceeded for weather service \'OpenWeatherMap\'')
                ->and($exception->getCode())->toBe(WeatherException::CODE_RATE_LIMIT_EXCEEDED);
        });
    });
});
