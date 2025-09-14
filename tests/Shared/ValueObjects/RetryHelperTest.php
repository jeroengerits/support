<?php

declare(strict_types=1);

use JeroenGerits\Support\Shared\ValueObjects\RetryHelper;

describe('Shared Domain', function (): void {
    describe('RetryHelper', function (): void {
        it('executes callback successfully on first attempt', function (): void {
            $counter = 0;
            $result = RetryHelper::retry(function () use (&$counter): string {
                $counter++;

                return 'success';
            });

            expect($result)->toBe('success')
                ->and($counter)->toBe(1);
        });

        it('retries on failure and succeeds on second attempt', function (): void {
            $counter = 0;
            $result = RetryHelper::retry(function () use (&$counter): string {
                $counter++;
                if ($counter === 1) {
                    throw new Exception('First attempt failed');
                }

                return 'success';
            });

            expect($result)->toBe('success')
                ->and($counter)->toBe(2);
        });

        it('retries multiple times with exponential backoff', function (): void {
            $counter = 0;
            $startTime = microtime(true);

            $result = RetryHelper::retry(function () use (&$counter): string {
                $counter++;
                if ($counter < 3) {
                    throw new Exception("Attempt {$counter} failed");
                }

                return 'success';
            }, maxAttempts: 3, baseDelay: 0.01);

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            expect($result)->toBe('success')
                ->and($counter)->toBe(3)
                ->and($duration)->toBeGreaterThan(0.01); // Should have some delay
        });

        it('throws exception after max attempts', function (): void {
            $counter = 0;

            try {
                RetryHelper::retry(function () use (&$counter): void {
                    $counter++;

                    throw new Exception("Attempt {$counter} failed");
                }, maxAttempts: 2);
                expect(false)->toBeTrue('Expected exception was not thrown');
            } catch (Exception $e) {
                expect($e->getMessage())->toBe('Attempt 2 failed');
            }

            expect($counter)->toBe(2);
        });

        it('uses custom max attempts', function (): void {
            $counter = 0;

            try {
                RetryHelper::retry(function () use (&$counter): void {
                    $counter++;

                    throw new Exception("Attempt {$counter} failed");
                }, maxAttempts: 5);
                expect(false)->toBeTrue('Expected exception was not thrown');
            } catch (Exception $e) {
                expect($e->getMessage())->toBe('Attempt 5 failed');
            }

            expect($counter)->toBe(5);
        });

        it('uses custom base delay', function (): void {
            $counter = 0;
            $startTime = microtime(true);

            try {
                RetryHelper::retry(function () use (&$counter): void {
                    $counter++;

                    throw new Exception("Attempt {$counter} failed");
                }, maxAttempts: 2, baseDelay: 0.1);
                expect(false)->toBeTrue('Expected exception was not thrown');
            } catch (Exception $e) {
                expect($e->getMessage())->toBe('Attempt 2 failed');
            }

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            expect($duration)->toBeGreaterThan(0.1); // Should have at least the base delay
        });

        it('handles different exception types', function (): void {
            try {
                RetryHelper::retry(function (): void {
                    throw new RuntimeException('Runtime error');
                }, maxAttempts: 1);
                expect(false)->toBeTrue('Expected exception was not thrown');
            } catch (RuntimeException $e) {
                expect($e->getMessage())->toBe('Runtime error');
            }
        });
    });
});
