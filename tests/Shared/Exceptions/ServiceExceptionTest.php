<?php

declare(strict_types=1);

use JeroenGerits\Support\Shared\Exceptions\ServiceException;

describe('Shared Domain', function (): void {
    describe('ServiceException', function (): void {
        it('creates with default values', function (): void {
            $exception = new ServiceException;

            expect($exception->getMessage())->toBe('Service operation failed')
                ->and($exception->getCode())->toBe(ServiceException::CODE_SERVICE_UNAVAILABLE);
        });

        it('creates with custom message and code', function (): void {
            $exception = new ServiceException('Custom error', ServiceException::CODE_SERVICE_NOT_FOUND);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(ServiceException::CODE_SERVICE_NOT_FOUND);
        });

        it('creates with previous exception', function (): void {
            $previous = new Exception('Previous error');
            $exception = new ServiceException('Custom error', ServiceException::CODE_INVALID_CONFIGURATION, $previous);

            expect($exception->getMessage())->toBe('Custom error')
                ->and($exception->getCode())->toBe(ServiceException::CODE_INVALID_CONFIGURATION)
                ->and($exception->getPrevious())->toBe($previous);
        });

        it('creates service not found exception', function (): void {
            $exception = ServiceException::notFound('test-service');

            expect($exception->getMessage())->toBe('Service \'test-service\' not found')
                ->and($exception->getCode())->toBe(ServiceException::CODE_SERVICE_NOT_FOUND);
        });

        it('creates service unavailable exception', function (): void {
            $exception = ServiceException::unavailable('test-service');

            expect($exception->getMessage())->toBe('Service \'test-service\' is currently unavailable')
                ->and($exception->getCode())->toBe(ServiceException::CODE_SERVICE_UNAVAILABLE);
        });

        it('creates invalid configuration exception', function (): void {
            $exception = ServiceException::invalidConfiguration('Missing required parameter');

            expect($exception->getMessage())->toBe('Invalid service configuration: Missing required parameter')
                ->and($exception->getCode())->toBe(ServiceException::CODE_INVALID_CONFIGURATION);
        });
    });
});
