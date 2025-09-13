<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidLongitudeException;

test('extends support exception', function (): void {
    $exception = new InvalidLongitudeException;
    expect($exception)->toBeInstanceOf(Exception::class);
});

test('can be instantiated without message', function (): void {
    $exception = new InvalidLongitudeException;
    expect($exception)->toBeInstanceOf(InvalidLongitudeException::class)
        ->and($exception->getMessage())->toBe('');
});

test('can be instantiated with message', function (): void {
    $message = 'Invalid longitude value';
    $exception = new InvalidLongitudeException($message);
    expect($exception)->toBeInstanceOf(InvalidLongitudeException::class)
        ->and($exception->getMessage())->toBe($message);
});

test('can be instantiated with message and code', function (): void {
    $message = 'Invalid longitude value';
    $code = 400;
    $exception = new InvalidLongitudeException($message, $code);
    expect($exception)->toBeInstanceOf(InvalidLongitudeException::class)
        ->and($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code);
});

test('can be instantiated with message code and previous', function (): void {
    $message = 'Invalid longitude value';
    $code = 400;
    $previous = new Exception('Previous exception');
    $exception = new InvalidLongitudeException($message, $code, $previous);
    expect($exception)->toBeInstanceOf(InvalidLongitudeException::class)
        ->and($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBe($previous);
});

test('can be thrown and caught', function (): void {
    expect(function (): never {
        throw new InvalidLongitudeException('Test longitude error');
    })->toThrow(InvalidLongitudeException::class, 'Test longitude error');
});

test('can be caught as generic exception', function (): void {
    expect(function (): never {
        throw new InvalidLongitudeException('Test longitude error');
    })->toThrow(Exception::class, 'Test longitude error');
});

test('exception properties work correctly', function (): void {
    $exception = new InvalidLongitudeException('Test message', 123);
    expect($exception->getMessage())->toBe('Test message')
        ->and($exception->getCode())->toBe(123);
});

test('to string includes class name', function (): void {
    $exception = new InvalidLongitudeException('Test message');
    $string = (string) $exception;

    expect($string)->toContain('InvalidLongitudeException')
        ->and($string)->toContain('Test message');
});
