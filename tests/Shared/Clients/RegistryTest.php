<?php

declare(strict_types=1);

use JeroenGerits\Support\Shared\Clients\Registry;
use JeroenGerits\Support\Shared\Exceptions\ServiceException;

describe('Shared Domain', function (): void {
    describe('Registry', function (): void {
        it('registers and retrieves services', function (): void {
            $registry = new Registry;
            $service = new stdClass;

            $registry->register('test-service', fn (): \stdClass => $service);

            expect($registry->get('test-service'))->toBe($service);
        });

        it('registers services with factory functions', function (): void {
            $registry = new Registry;
            $counter = 0;

            $registry->register('counter', function () use (&$counter): int {
                return ++$counter;
            });

            expect($registry->get('counter'))->toBe(1)
                ->and($registry->get('counter'))->toBe(2)
                ->and($registry->get('counter'))->toBe(3);
        });

        it('checks if service exists', function (): void {
            $registry = new Registry;

            expect($registry->has('non-existent'))->toBeFalse();

            $registry->register('test-service', fn (): \stdClass => new stdClass);

            expect($registry->has('test-service'))->toBeTrue();
        });

        it('lists registered services', function (): void {
            $registry = new Registry;

            expect($registry->list())->toBe([]);

            $registry->register('service1', fn (): \stdClass => new stdClass);
            $registry->register('service2', fn (): \stdClass => new stdClass);

            $services = $registry->list();
            expect($services)->toHaveCount(2)
                ->and($services)->toContain('service1')
                ->and($services)->toContain('service2');
        });

        it('throws exception for non-existent service', function (): void {
            $registry = new Registry;

            expect(fn (): mixed => $registry->get('non-existent'))
                ->toThrow(ServiceException::class, 'Service \'non-existent\' not found');
        });

        it('allows overwriting services', function (): void {
            $registry = new Registry;
            $service1 = new stdClass;
            $service2 = new stdClass;

            $registry->register('test', fn (): \stdClass => $service1);
            expect($registry->get('test'))->toBe($service1);

            $registry->register('test', fn (): \stdClass => $service2);
            expect($registry->get('test'))->toBe($service2);
        });
    });
});
