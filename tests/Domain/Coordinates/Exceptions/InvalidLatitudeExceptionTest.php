<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLatitudeException;

test('extends support exception', function (): void {
    $exception = new InvalidLatitudeException;
    expect($exception)->toBeInstanceOf(Exception::class);
});

test('can be instantiated without message', function (): void {
    $exception = new InvalidLatitudeException;
    expect($exception)->toBeInstanceOf(InvalidLatitudeException::class)
        ->and($exception->getMessage())->toBe('');
});

test('can be instantiated with message', function (): void {
    $message = 'Invalid latitude value';
    $exception = new InvalidLatitudeException($message);
    expect($exception)->toBeInstanceOf(InvalidLatitudeException::class)
        ->and($exception->getMessage())->toBe($message);
});

test('can be instantiated with message and code', function (): void {
    $message = 'Invalid latitude value';
    $code = 400;
    $exception = new InvalidLatitudeException($message, $code);
    expect($exception)->toBeInstanceOf(InvalidLatitudeException::class)
        ->and($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code);
});

test('can be instantiated with message code and previous', function (): void {
    $message = 'Invalid latitude value';
    $code = 400;
    $previous = new Exception('Previous exception');
    $exception = new InvalidLatitudeException($message, $code, $previous);
    expect($exception)->toBeInstanceOf(InvalidLatitudeException::class)
        ->and($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBe($previous);
});

test('can be thrown and caught', function (): void {
    expect(function (): never {
        throw new InvalidLatitudeException('Test latitude error');
    })->toThrow(InvalidLatitudeException::class, 'Test latitude error');
});

test('can be caught as generic exception', function (): void {
    expect(function (): never {
        throw new InvalidLatitudeException('Test latitude error');
    })->toThrow(Exception::class, 'Test latitude error');
});

test('exception properties work correctly', function (): void {
    $exception = new InvalidLatitudeException('Test message', 123);
    expect($exception->getMessage())->toBe('Test message')
        ->and($exception->getCode())->toBe(123);
});

test('to string includes class name', function (): void {
    $exception = new InvalidLatitudeException('Test message');
    $string = (string) $exception;

    expect($string)->toContain('InvalidLatitudeException')
        ->and($string)->toContain('Test message');
});
