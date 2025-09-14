<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Exceptions\InvalidLongitudeException;

it('creates exception with default message', function (): void {
    $exception = new InvalidLongitudeException;

    expect($exception->getMessage())->toBe('Invalid longitude value provided')
        ->and($exception->getCode())->toBe(1001)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message', function (): void {
    $message = 'Custom longitude error message';
    $exception = new InvalidLongitudeException($message);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe(1001)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message and code', function (): void {
    $message = 'Custom longitude error message';
    $code = 123;
    $exception = new InvalidLongitudeException($message, $code);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message, code and previous exception', function (): void {
    $message = 'Custom longitude error message';
    $code = 123;
    $previous = new Exception('Previous exception');
    $exception = new InvalidLongitudeException($message, $code, null, [], $previous);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBe($previous);
});

it('extends Exception class', function (): void {
    $exception = new InvalidLongitudeException;

    expect($exception)->toBeInstanceOf(Exception::class);
});
