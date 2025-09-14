<?php

declare(strict_types=1);

use JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException;

it('creates exception with default message', function (): void {
    $exception = new InvalidCoordinatesException;

    expect($exception->getMessage())->toBe('Invalid coordinates provided')
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
    $exception = new InvalidCoordinatesException($message, $code, $previous);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code)
        ->and($exception->getPrevious())->toBe($previous);
});

it('extends Exception class', function (): void {
    $exception = new InvalidCoordinatesException;

    expect($exception)->toBeInstanceOf(Exception::class);
});

it('creates exception for latitude out of range', function (): void {
    $exception = InvalidCoordinatesException::latitudeOutOfRange(100.0);

    expect($exception->getMessage())->toBe('Latitude value 100 is outside the valid range of -90.0 to +90.0 degrees')
        ->and($exception->getCode())->toBe(1002);
});

it('creates exception for longitude out of range', function (): void {
    $exception = InvalidCoordinatesException::longitudeOutOfRange(200.0);

    expect($exception->getMessage())->toBe('Longitude value 200 is outside the valid range of -180.0 to +180.0 degrees')
        ->and($exception->getCode())->toBe(1002);
});

it('creates exception for invalid latitude type', function (): void {
    $exception = InvalidCoordinatesException::invalidType('invalid', 'latitude');

    expect($exception->getMessage())->toBe('Invalid latitude type: string. Expected: float, int, string, or Latitude')
        ->and($exception->getCode())->toBe(1003);
});

it('creates exception for invalid longitude type', function (): void {
    $exception = InvalidCoordinatesException::invalidType('invalid', 'longitude');

    expect($exception->getMessage())->toBe('Invalid longitude type: string. Expected: float, int, string, or Longitude')
        ->and($exception->getCode())->toBe(1003);
});

it('creates exception for invalid latitude format', function (): void {
    $exception = InvalidCoordinatesException::invalidFormat('not-a-number', 'latitude');

    expect($exception->getMessage())->toBe("Invalid latitude format: 'not-a-number'. Expected decimal degrees (e.g., '40.7128')")
        ->and($exception->getCode())->toBe(1005);
});

it('creates exception for invalid longitude format', function (): void {
    $exception = InvalidCoordinatesException::invalidFormat('not-a-number', 'longitude');

    expect($exception->getMessage())->toBe("Invalid longitude format: 'not-a-number'. Expected decimal degrees (e.g., '-74.0060')")
        ->and($exception->getCode())->toBe(1005);
});

it('creates exception for missing latitude from array', function (): void {
    $array = ['lng' => -74.0060];
    $exception = InvalidCoordinatesException::missingFromArray($array, 'latitude');

    expect($exception->getMessage())->toBe('latitude missing from coordinate array. Available keys: lng')
        ->and($exception->getCode())->toBe(1004);
});

it('creates exception for missing longitude from array', function (): void {
    $array = ['lat' => 40.7128];
    $exception = InvalidCoordinatesException::missingFromArray($array, 'longitude');

    expect($exception->getMessage())->toBe('longitude missing from coordinate array. Available keys: lat')
        ->and($exception->getCode())->toBe(1004);
});

it('creates exception for invalid array structure', function (): void {
    $array = ['invalid' => 'structure'];
    $exception = InvalidCoordinatesException::invalidArrayStructure($array);

    expect($exception->getMessage())->toBe('Invalid coordinate array structure. Expected: [lat, lng] or [latitude, longitude] or [lat => x, lng => y]')
        ->and($exception->getCode())->toBe(1005);
});
