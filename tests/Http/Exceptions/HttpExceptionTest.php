<?php

declare(strict_types=1);

use JeroenGerits\Support\Http\Exceptions\HttpConnectionException;
use JeroenGerits\Support\Http\Exceptions\HttpException;
use JeroenGerits\Support\Http\Exceptions\HttpTimeoutException;

describe('HTTP Domain', function (): void {
    describe('HttpException', function (): void {
        it('creates with default values', function (): void {
            $exception = new HttpException;

            expect($exception->getMessage())->toBe('HTTP request failed')
                ->and($exception->getCode())->toBe(HttpException::CODE_REQUEST_FAILED);
        });

        it('creates with custom message and code', function (): void {
            $exception = new HttpException('Custom error', HttpException::CODE_TIMEOUT);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(HttpException::CODE_TIMEOUT);
        });

        it('creates with previous exception', function (): void {
            $previous = new Exception('Previous error');
            $exception = new HttpException('Custom error', HttpException::CODE_CONNECTION_ERROR, $previous);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(HttpException::CODE_CONNECTION_ERROR)
                ->and($exception->getPrevious())->toBe($previous);
        });

        it('creates timeout exception', function (): void {
            $exception = HttpException::timeout('https://api.example.com', 30);

            expect($exception->getMessage())->toBe('HTTP request to \'https://api.example.com\' timed out after 30 seconds')
                ->and($exception->getCode())->toBe(HttpException::CODE_TIMEOUT);
        });

        it('creates connection error exception', function (): void {
            $exception = HttpException::connectionError('https://api.example.com', 'DNS resolution failed');

            expect($exception->getMessage())->toBe('HTTP connection to \'https://api.example.com\' failed: DNS resolution failed')
                ->and($exception->getCode())->toBe(HttpException::CODE_CONNECTION_ERROR);
        });

        it('creates invalid response exception', function (): void {
            $exception = HttpException::invalidResponse('https://api.example.com', 500);

            expect($exception->getMessage())->toBe('Invalid HTTP response from \'https://api.example.com\': 500')
                ->and($exception->getCode())->toBe(HttpException::CODE_INVALID_RESPONSE);
        });
    });

    describe('HttpTimeoutException', function (): void {
        it('creates with URL and timeout', function (): void {
            $exception = new HttpTimeoutException('https://api.example.com', 60);

            expect($exception->getMessage())->toBe('HTTP request to \'https://api.example.com\' timed out after 60 seconds')
                ->and($exception->getCode())->toBe(HttpException::CODE_TIMEOUT);
        });
    });

    describe('HttpConnectionException', function (): void {
        it('creates with URL and reason', function (): void {
            $exception = new HttpConnectionException('https://api.example.com', 'Connection refused');

            expect($exception->getMessage())->toBe('HTTP connection to \'https://api.example.com\' failed: Connection refused')
                ->and($exception->getCode())->toBe(HttpException::CODE_CONNECTION_ERROR);
        });
    });
});
