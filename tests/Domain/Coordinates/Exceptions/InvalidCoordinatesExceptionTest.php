<?php

declare(strict_types=1);

use JeroenGerits\Support\Domain\Coordinates\Exceptions\InvalidCoordinatesException;

test('extends support exception', function (): void {
    $exception = new InvalidCoordinatesException;
    expect($exception)->toBeInstanceOf(Exception::class);
});

test('can be instantiated without message', function (): void {
    $exception = new InvalidCoordinatesException;
    expect($exception)->toBeInstanceOf(InvalidCoordinatesException::class)
        ->and($exception->getMessage())->toBe('');
});

test('can be instantiated with message', function (): void {
    $message = 'Invalid coordinates value';
    $exception = new InvalidCoordinatesException($message);
    expect($exception)->toBeInstanceOf(InvalidCoordinatesException::class)
        ->and($exception->getMessage())->toBe($message);
});

test('can be instantiated with message and code', function (): void {
    $message = 'Invalid coordinates value';
    $code = 400;
    $exception = new InvalidCoordinatesException($message, $code);
    expect($exception)->toBeInstanceOf(InvalidCoordinatesException::class)
        ->and($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code);
});

test('can be instantiated with message code and previous', function (): void {
    $message = 'Invalid coordinates value';
    $code = 400;
    $previous = new Exception('Previous exception');
    $exception = new InvalidCoordinatesException($message, $code, $previous);
    expect($exception)->toBeInstanceOf(InvalidCoordinatesException::class)
        ->and($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBe($previous);
});

test('can be thrown and caught', function (): void {
    expect(function (): never {
        throw new InvalidCoordinatesException('Test coordinates error');
    })->toThrow(InvalidCoordinatesException::class, 'Test coordinates error');
});
test('can be caught as generic exception', function (): void {
    expect(function (): never {
        throw new InvalidCoordinatesException('Test coordinates error');
    })->toThrow(Exception::class, 'Test coordinates error');
});

test('exception properties work correctly', function (): void {
    $exception = new InvalidCoordinatesException('Test message', 123);
    expect($exception->getMessage())->toBe('Test message')
        ->and($exception->getCode())->toBe(123);
});

test('to string includes class name', function (): void {
    $exception = new InvalidCoordinatesException('Test message');
    $string = (string) $exception;

    expect($string)->toContain('InvalidCoordinatesException')
        ->and($string)->toContain('Test message');
});
