<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;

it('creates exception with default message', function (): void {
    $exception = new InvalidCoordinatesException;

    expect($exception->getMessage())->toBe('Invalid coordinates values provided')
        ->and($exception->getCode())->toBe(1001)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message', function (): void {
    $message = 'Custom coordinates error message';
    $exception = new InvalidCoordinatesException($message);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe(1001)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message and code', function (): void {
    $message = 'Custom coordinates error message';
    $code = 123;
    $exception = new InvalidCoordinatesException($message, $code);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message, code and previous exception', function (): void {
    $message = 'Custom coordinates error message';
    $code = 123;
    $previous = new Exception('Previous exception');
    $exception = new InvalidCoordinatesException($message, $code, null, [], $previous);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBe($previous);
});

it('extends Exception class', function (): void {
    $exception = new InvalidCoordinatesException;

    expect($exception)->toBeInstanceOf(Exception::class);
});

it('maintains exception chain with previous exception', function (): void {
    $previous = new Exception('Root cause');
    $exception = new InvalidCoordinatesException('Coordinates error', 1001, null, [], $previous);

    expect($exception->getPrevious())->toBe($previous)
        ->and($exception->getPrevious()->getMessage())->toBe('Root cause');
});

it('has proper string representation', function (): void {
    $exception = new InvalidCoordinatesException('Test coordinates error');

    expect((string) $exception)->toContain('InvalidCoordinatesException')
        ->and((string) $exception)->toContain('Test coordinates error');
});
