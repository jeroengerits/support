<?php

declare(strict_types=1);

use JeroenGerits\Support\Weather\ValueObjects\WeatherInformation;

describe('Weather Domain', function (): void {
    describe('WeatherInformation', function (): void {
        it('creates with all properties', function (): void {
            $timestamp = new DateTimeImmutable('2024-01-15 14:30:00');
            $weather = new WeatherInformation(
                temperature: 15.2,
                description: 'Clear sky',
                humidity: 65,
                pressure: 1013.25,
                windSpeed: 3.5,
                windDirection: 270,
                icon: '01d',
                location: 'Amsterdam',
                timestamp: $timestamp
            );

            expect($weather->temperature)->toBe(15.2)
                ->and($weather->description)->toBe('Clear sky')
                ->and($weather->humidity)->toBe(65)
                ->and($weather->pressure)->toBe(1013.25)
                ->and($weather->windSpeed)->toBe(3.5)
                ->and($weather->windDirection)->toBe(270)
                ->and($weather->icon)->toBe('01d')
                ->and($weather->location)->toBe('Amsterdam')
                ->and($weather->timestamp)->toBe($timestamp);
        });

        it('creates with minimal properties', function (): void {
            $weather = new WeatherInformation(
                temperature: 20.0,
                description: 'Sunny',
                humidity: 50,
                pressure: 1013.0,
                windSpeed: 2.0,
                windDirection: 180
            );

            expect($weather->temperature)->toBe(20.0)
                ->and($weather->description)->toBe('Sunny')
                ->and($weather->humidity)->toBe(50)
                ->and($weather->pressure)->toBe(1013.0)
                ->and($weather->windSpeed)->toBe(2.0)
                ->and($weather->windDirection)->toBe(180)
                ->and($weather->icon)->toBeNull()
                ->and($weather->location)->toBeNull()
                ->and($weather->timestamp)->toBeNull();
        });

        it('implements equality correctly', function (): void {
            $weather1 = new WeatherInformation(
                temperature: 15.2,
                description: 'Clear sky',
                humidity: 65,
                pressure: 1013.25,
                windSpeed: 3.5,
                windDirection: 270,
                location: 'Amsterdam'
            );

            $weather2 = new WeatherInformation(
                temperature: 15.2,
                description: 'Clear sky',
                humidity: 65,
                pressure: 1013.25,
                windSpeed: 3.5,
                windDirection: 270,
                location: 'Amsterdam'
            );

            $weather3 = new WeatherInformation(
                temperature: 15.2,
                description: 'Clear sky',
                humidity: 65,
                pressure: 1013.25,
                windSpeed: 3.5,
                windDirection: 270,
                location: 'London'
            );

            expect($weather1->isEqual($weather2))->toBeTrue()
                ->and($weather1->isEqual($weather3))->toBeFalse();
        });

        it('converts to string correctly', function (): void {
            $weather = new WeatherInformation(
                temperature: 15.2,
                description: 'Clear sky',
                humidity: 65,
                pressure: 1013.25,
                windSpeed: 3.5,
                windDirection: 270
            );

            expect((string) $weather)->toBe('15.2°C, Clear sky');
        });

        it('converts temperature to Fahrenheit correctly', function (): void {
            $weather = new WeatherInformation(
                temperature: 0.0,
                description: 'Freezing',
                humidity: 50,
                pressure: 1013.0,
                windSpeed: 0.0,
                windDirection: 0
            );

            expect($weather->getTemperatureInFahrenheit())->toBe(32.0);
        });

        it('converts temperature to Fahrenheit with decimal precision', function (): void {
            $weather = new WeatherInformation(
                temperature: 20.0,
                description: 'Warm',
                humidity: 50,
                pressure: 1013.0,
                windSpeed: 0.0,
                windDirection: 0
            );

            expect($weather->getTemperatureInFahrenheit())->toBe(68.0);
        });

        it('provides wind direction names correctly', function (): void {
            $testCases = [
                0 => 'N',
                45 => 'NE',
                90 => 'E',
                135 => 'SE',
                180 => 'S',
                225 => 'SW',
                270 => 'W',
                315 => 'NW',
                360 => 'N',
            ];

            foreach ($testCases as $degrees => $expectedDirection) {
                $weather = new WeatherInformation(
                    temperature: 20.0,
                    description: 'Test',
                    humidity: 50,
                    pressure: 1013.0,
                    windSpeed: 0.0,
                    windDirection: $degrees
                );

                expect($weather->getWindDirectionName())->toBe($expectedDirection);
            }
        });

        it('provides formatted location', function (): void {
            $weather = new WeatherInformation(
                temperature: 20.0,
                description: 'Test',
                humidity: 50,
                pressure: 1013.0,
                windSpeed: 0.0,
                windDirection: 0,
                location: 'Amsterdam'
            );

            expect($weather->getFormattedLocation())->toBe('Amsterdam');
        });

        it('handles null location in formatted location', function (): void {
            $weather = new WeatherInformation(
                temperature: 20.0,
                description: 'Test',
                humidity: 50,
                pressure: 1013.0,
                windSpeed: 0.0,
                windDirection: 0
            );

            expect($weather->getFormattedLocation())->toBe('Unknown Location');
        });
    });
});
