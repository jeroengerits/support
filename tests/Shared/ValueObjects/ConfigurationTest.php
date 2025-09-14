<?php

declare(strict_types=1);

use JeroenGerits\Support\Shared\ValueObjects\Configuration;

describe('Shared Domain', function (): void {
    describe('Configuration', function (): void {
        it('creates with default values', function (): void {
            $config = new Configuration;

            expect($config->geocoding)->toBe([])
                ->and($config->weather)->toBe([])
                ->and($config->http)->toBe([])
                ->and($config->cache)->toBe([]);
        });

        it('creates with custom values', function (): void {
            $config = new Configuration(
                geocoding: ['test' => 'value'],
                weather: ['api_key' => 'test'],
                http: ['timeout' => 30],
                cache: ['ttl' => 3600]
            );

            expect($config->geocoding)->toBe(['test' => 'value'])
                ->and($config->weather)->toBe(['api_key' => 'test'])
                ->and($config->http)->toBe(['timeout' => 30])
                ->and($config->cache)->toBe(['ttl' => 3600]);
        });

        it('loads configuration from environment', function (): void {
            // Set environment variables
            $_ENV['NOMINATIM_USER_AGENT'] = 'TestApp/1.0';
            $_ENV['NOMINATIM_EMAIL'] = 'test@example.com';
            $_ENV['NOMINATIM_TIMEOUT'] = '60';
            $_ENV['OPENWEATHER_API_KEY'] = 'test_api_key';
            $_ENV['OPENWEATHER_UNITS'] = 'imperial';
            $_ENV['HTTP_TIMEOUT'] = '45';
            $_ENV['HTTP_RETRY_ATTEMPTS'] = '5';
            $_ENV['CACHE_TTL'] = '7200';
            $_ENV['CACHE_ENABLED'] = 'false';

            $config = Configuration::fromEnvironment();

            expect($config->geocoding['nominatim']['user_agent'])->toBe('TestApp/1.0')
                ->and($config->geocoding['nominatim']['email'])->toBe('test@example.com')
                ->and($config->geocoding['nominatim']['timeout'])->toBe(60)
                ->and($config->weather['openweathermap']['api_key'])->toBe('test_api_key')
                ->and($config->weather['openweathermap']['units'])->toBe('imperial')
                ->and($config->http['timeout'])->toBe(45)
                ->and($config->http['retry_attempts'])->toBe(5)
                ->and($config->cache['ttl'])->toBe(7200)
                ->and($config->cache['enabled'])->toBeFalse();
        });

        it('uses default values when environment variables are not set', function (): void {
            // Clear environment variables
            unset($_ENV['NOMINATIM_USER_AGENT']);
            unset($_ENV['NOMINATIM_EMAIL']);
            unset($_ENV['NOMINATIM_TIMEOUT']);
            unset($_ENV['OPENWEATHER_API_KEY']);
            unset($_ENV['OPENWEATHER_UNITS']);
            unset($_ENV['HTTP_TIMEOUT']);
            unset($_ENV['HTTP_RETRY_ATTEMPTS']);
            unset($_ENV['CACHE_TTL']);
            unset($_ENV['CACHE_ENABLED']);

            $config = Configuration::fromEnvironment();

            expect($config->geocoding['nominatim']['user_agent'])->toBe('Support Package/1.0')
                ->and($config->geocoding['nominatim']['email'])->toBeNull()
                ->and($config->geocoding['nominatim']['timeout'])->toBe(30)
                ->and($config->weather['openweathermap']['api_key'])->toBeNull()
                ->and($config->weather['openweathermap']['units'])->toBe('metric')
                ->and($config->http['timeout'])->toBe(30)
                ->and($config->http['retry_attempts'])->toBe(3)
                ->and($config->cache['ttl'])->toBe(3600)
                ->and($config->cache['enabled'])->toBeTrue();
        });

        it('converts string values to appropriate types', function (): void {
            $_ENV['NOMINATIM_TIMEOUT'] = '90';
            $_ENV['HTTP_TIMEOUT'] = '120';
            $_ENV['HTTP_RETRY_ATTEMPTS'] = '7';
            $_ENV['CACHE_TTL'] = '1800';
            $_ENV['CACHE_ENABLED'] = 'true';

            $config = Configuration::fromEnvironment();

            expect($config->geocoding['nominatim']['timeout'])->toBe(90)
                ->and($config->http['timeout'])->toBe(120)
                ->and($config->http['retry_attempts'])->toBe(7)
                ->and($config->cache['ttl'])->toBe(1800)
                ->and($config->cache['enabled'])->toBeTrue();
        });

        it('handles boolean conversion correctly', function (): void {
            $testCases = [
                'true' => true,
                'false' => false,
                '1' => true,
                '0' => false,
                'yes' => true,
                'no' => false,
                'on' => true,
                'off' => false,
            ];

            foreach ($testCases as $value => $expected) {
                $_ENV['CACHE_ENABLED'] = $value;
                $config = Configuration::fromEnvironment();
                expect($config->cache['enabled'])->toBe($expected);
            }
        });
    });
});
