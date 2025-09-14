<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Exceptions\InvalidLatitudeException;

it('creates exception with default message', function (): void {
    $exception = new InvalidLatitudeException;

    expect($exception->getMessage())->toBe('Invalid latitude value provided')
        ->and($exception->getCode())->toBe(0)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message', function (): void {
    $message = 'Custom latitude error message';
    $exception = new InvalidLatitudeException($message);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe(0)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message and code', function (): void {
    $message = 'Custom latitude error message';
    $code = 123;
    $exception = new InvalidLatitudeException($message, $code);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBeNull();
});

it('creates exception with custom message, code and previous exception', function (): void {
    $message = 'Custom latitude error message';
    $code = 123;
    $previous = new Exception('Previous exception');
    $exception = new InvalidLatitudeException($message, $code, $previous);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBe($previous);
});

it('extends Exception class', function (): void {
    $exception = new InvalidLatitudeException;

    expect($exception)->toBeInstanceOf(Exception::class);
});
