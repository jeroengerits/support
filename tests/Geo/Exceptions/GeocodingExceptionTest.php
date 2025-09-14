<?php

declare(strict_types=1);

use JeroenGerits\Support\Geo\Exceptions\GeocodingException;

describe('Geo Domain', function (): void {
    describe('GeocodingException', function (): void {
        it('creates with default values', function (): void {
            $exception = new GeocodingException;

            expect($exception->getMessage())->toBe('Geocoding operation failed')
                ->and($exception->getCode())->toBe(GeocodingException::CODE_SERVICE_UNAVAILABLE);
        });

        it('creates with custom message and code', function (): void {
            $exception = new GeocodingException('Custom error', GeocodingException::CODE_RATE_LIMIT_EXCEEDED);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(GeocodingException::CODE_RATE_LIMIT_EXCEEDED);
        });

        it('creates with previous exception', function (): void {
            $previous = new Exception('Previous error');
            $exception = new GeocodingException('Custom error', GeocodingException::CODE_INVALID_RESPONSE, $previous);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(GeocodingException::CODE_INVALID_RESPONSE)
                ->and($exception->getPrevious())->toBe($previous);
        });

        it('creates service unavailable exception', function (): void {
            $exception = GeocodingException::serviceUnavailable('Nominatim');

            expect($exception->getMessage())->toBe('Geocoding service \'Nominatim\' is currently unavailable')
                ->and($exception->getCode())->toBe(GeocodingException::CODE_SERVICE_UNAVAILABLE);
        });

        it('creates rate limit exceeded exception', function (): void {
            $exception = GeocodingException::rateLimitExceeded('Nominatim');

            expect($exception->getMessage())->toBe('Rate limit exceeded for geocoding service \'Nominatim\'')
                ->and($exception->getCode())->toBe(GeocodingException::CODE_RATE_LIMIT_EXCEEDED);
        });

        it('creates invalid response exception', function (): void {
            $exception = GeocodingException::invalidResponse('Nominatim', 'Invalid JSON format');

            expect($exception->getMessage())->toBe('Invalid response from geocoding service \'Nominatim\': Invalid JSON format')
                ->and($exception->getCode())->toBe(GeocodingException::CODE_INVALID_RESPONSE);
        });
    });
});
