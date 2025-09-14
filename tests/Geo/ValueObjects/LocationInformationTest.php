<?php

declare(strict_types=1);

use JeroenGerits\Support\Geo\ValueObjects\LocationInformation;

describe('Geo Domain', function (): void {
    describe('LocationInformation', function (): void {
        it('creates with all properties', function (): void {
            $location = new LocationInformation(
                city: 'Amsterdam',
                state: 'Noord-Holland',
                country: 'Netherlands',
                postalCode: '1012',
                formattedAddress: 'Amsterdam, Noord-Holland, Netherlands',
                timezone: 'Europe/Amsterdam',
                confidence: 0.8,
                countryCode: 'NL'
            );

            expect($location->city)->toBe('Amsterdam')
                ->and($location->state)->toBe('Noord-Holland')
                ->and($location->country)->toBe('Netherlands')
                ->and($location->postalCode)->toBe('1012')
                ->and($location->formattedAddress)->toBe('Amsterdam, Noord-Holland, Netherlands')
                ->and($location->timezone)->toBe('Europe/Amsterdam')
                ->and($location->confidence)->toBe(0.8)
                ->and($location->countryCode)->toBe('NL');
        });

        it('creates with minimal properties', function (): void {
            $location = new LocationInformation;

            expect($location->city)->toBeNull()
                ->and($location->state)->toBeNull()
                ->and($location->country)->toBeNull()
                ->and($location->postalCode)->toBeNull()
                ->and($location->formattedAddress)->toBeNull()
                ->and($location->timezone)->toBeNull()
                ->and($location->confidence)->toBeNull()
                ->and($location->countryCode)->toBeNull();
        });

        it('implements equality correctly', function (): void {
            $location1 = new LocationInformation(
                city: 'Amsterdam',
                state: 'Noord-Holland',
                country: 'Netherlands'
            );

            $location2 = new LocationInformation(
                city: 'Amsterdam',
                state: 'Noord-Holland',
                country: 'Netherlands'
            );

            $location3 = new LocationInformation(
                city: 'Amsterdam',
                state: 'Noord-Holland',
                country: 'Germany'
            );

            expect($location1->isEqual($location2))->toBeTrue()
                ->and($location1->isEqual($location3))->toBeFalse();
        });

        it('converts to string correctly', function (): void {
            $location = new LocationInformation(
                city: 'Amsterdam',
                state: 'Noord-Holland',
                country: 'Netherlands'
            );

            expect((string) $location)->toBe('Amsterdam, Noord-Holland, Netherlands');
        });

        it('handles missing city in string conversion', function (): void {
            $location = new LocationInformation(
                state: 'Noord-Holland',
                country: 'Netherlands'
            );

            expect((string) $location)->toBe('Noord-Holland, Netherlands');
        });

        it('handles all null values in string conversion', function (): void {
            $location = new LocationInformation;

            expect((string) $location)->toBe('Unknown Location');
        });

        it('provides display name', function (): void {
            $location = new LocationInformation(
                city: 'Amsterdam',
                state: 'Noord-Holland',
                country: 'Netherlands'
            );

            expect($location->getDisplayName())->toBe('Amsterdam, Noord-Holland, Netherlands');
        });

        it('provides country code', function (): void {
            $location = new LocationInformation(countryCode: 'NL');

            expect($location->getCountryCode())->toBe('NL');
        });

        it('provides timezone', function (): void {
            $location = new LocationInformation(timezone: 'Europe/Amsterdam');

            expect($location->getTimezone())->toBe('Europe/Amsterdam');
        });

        it('provides confidence', function (): void {
            $location = new LocationInformation(confidence: 0.8);

            expect($location->getConfidence())->toBe(0.8);
        });

        it('handles null values in getters', function (): void {
            $location = new LocationInformation;

            expect($location->getCountryCode())->toBeNull()
                ->and($location->getTimezone())->toBeNull()
                ->and($location->getConfidence())->toBeNull();
        });
    });
});
