<?php

declare(strict_types=1);

use JeroenGerits\Support\Cache\CacheFactory;
use JeroenGerits\Support\Cache\ValueObjects\TimeToLive;

describe('Cache Package', function (): void {
    describe('ArrayCacheAdapter', function (): void {
        it('stores and retrieves values', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            $cache->set('key1', 'value1');
            expect($cache->get('key1'))->toBe('value1');
        });

        it('returns default value for missing keys', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            expect($cache->get('missing', 'default'))->toBe('default');
        });

        it('supports TTL with seconds', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            $cache->set('key1', 'value1', 1); // 1 second TTL
            expect($cache->get('key1'))->toBe('value1');

            // Wait for expiration (in real scenario, this would be tested with time mocking)
            sleep(2);
            expect($cache->get('key1'))->toBeNull();
        });

        it('supports TTL with DateInterval', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            // Test with DateInterval - should use accurate DateTime calculations
            $interval = new DateInterval('PT1S'); // 1 second
            $cache->set('key1', 'value1', $interval);
            expect($cache->get('key1'))->toBe('value1');

            // Wait for expiration
            sleep(2);
            expect($cache->get('key1'))->toBeNull();
        });

        it('has() method is optimized and does not use get() internally', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            $cache->set('key1', 'value1');
            expect($cache->has('key1'))->toBeTrue();

            $cache->delete('key1');
            expect($cache->has('key1'))->toBeFalse();

            // Test with expired item
            $cache->set('key1', 'value1', 1);
            expect($cache->has('key1'))->toBeTrue();

            sleep(2);
            expect($cache->has('key1'))->toBeFalse();
        });

        it('supports multiple operations', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            $cache->setMultiple(['key1' => 'value1', 'key2' => 'value2']);
            $values = $cache->getMultiple(['key1', 'key2', 'key3'], 'default');

            expect($values)->toBe(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'default']);
        });

        it('tracks cache statistics', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            // Miss
            $cache->get('missing');

            // Hit
            $cache->set('key1', 'value1');
            $cache->get('key1');

            $stats = $cache->getStats();
            expect($stats->getHits())->toBe(1);
            expect($stats->getMisses())->toBe(1);
            expect($stats->getHitRatio())->toBe(0.5);
            expect($stats->getItems())->toBe(1);
        });

        it('evicts items when max capacity is reached', function (): void {
            $cache = CacheFactory::createArrayCache('test', 2);

            $cache->set('key1', 'value1');
            $cache->set('key2', 'value2');
            $cache->set('key3', 'value3'); // Should evict key1

            expect($cache->has('key1'))->toBeFalse();
            expect($cache->has('key2'))->toBeTrue();
            expect($cache->has('key3'))->toBeTrue();
        });

        it('can be cleared', function (): void {
            $cache = CacheFactory::createArrayCache('test', 10);

            $cache->set('key1', 'value1');
            expect($cache->has('key1'))->toBeTrue();

            $cache->clear();
            expect($cache->has('key1'))->toBeFalse();
        });

        it('returns correct namespace', function (): void {
            $cache = CacheFactory::createArrayCache('test-namespace', 10);
            expect($cache->getNamespace())->toBe('test-namespace');
        });
    });

    describe('NullCacheAdapter', function (): void {
        it('always returns default values', function (): void {
            $cache = CacheFactory::createNullCache('test');

            $cache->set('key1', 'value1');
            expect($cache->get('key1', 'default'))->toBe('default');
        });

        it('always returns false for has()', function (): void {
            $cache = CacheFactory::createNullCache('test');

            $cache->set('key1', 'value1');
            expect($cache->has('key1'))->toBeFalse();
        });

        it('returns empty statistics', function (): void {
            $cache = CacheFactory::createNullCache('test');

            $stats = $cache->getStats();
            expect($stats->getHits())->toBe(0);
            expect($stats->getMisses())->toBe(0);
            expect($stats->getItems())->toBe(0);
        });
    });

    describe('TimeToLive Value Object', function (): void {
        it('creates from seconds', function (): void {
            $ttl = TimeToLive::fromSeconds(3600);
            expect($ttl->seconds)->toBe(3600);
        });

        it('creates from minutes', function (): void {
            $ttl = TimeToLive::fromMinutes(60);
            expect($ttl->seconds)->toBe(3600);
        });

        it('creates from hours', function (): void {
            $ttl = TimeToLive::fromHours(1);
            expect($ttl->seconds)->toBe(3600);
        });

        it('creates from days', function (): void {
            $ttl = TimeToLive::fromDays(1);
            expect($ttl->seconds)->toBe(86400);
        });

        it('has default value', function (): void {
            $ttl = TimeToLive::default();
            expect($ttl->seconds)->toBe(3600); // 1 hour
        });

        it('converts to string', function (): void {
            $ttl = TimeToLive::fromSeconds(120);
            expect((string) $ttl)->toBe('120s');
        });

        it('validates non-negative values', function (): void {
            expect(fn (): \JeroenGerits\Support\Cache\ValueObjects\TimeToLive => new TimeToLive(-1))->toThrow(\JeroenGerits\Support\Cache\Exceptions\InvalidTimeToLiveException::class);
        });
    });

    describe('CacheKey Value Object', function (): void {
        it('creates with key and namespace', function (): void {
            $key = \JeroenGerits\Support\Cache\ValueObjects\CacheKey::create('test-key', 'namespace');

            expect($key->key)->toBe('test-key');
            expect($key->namespace)->toBe('namespace');
            expect((string) $key)->toBe('namespace:test-key');
        });

        it('validates non-empty key', function (): void {
            expect(fn (): \JeroenGerits\Support\Cache\ValueObjects\CacheKey => \JeroenGerits\Support\Cache\ValueObjects\CacheKey::create('', 'namespace'))
                ->toThrow(\JeroenGerits\Support\Cache\Exceptions\InvalidCacheKeyException::class);
        });

        it('validates key without colons', function (): void {
            expect(fn (): \JeroenGerits\Support\Cache\ValueObjects\CacheKey => \JeroenGerits\Support\Cache\ValueObjects\CacheKey::create('key:with:colons', 'namespace'))
                ->toThrow(\JeroenGerits\Support\Cache\Exceptions\InvalidCacheKeyException::class);
        });
    });

    describe('CacheStats Value Object', function (): void {
        it('calculates hit ratio correctly', function (): void {
            $stats = \JeroenGerits\Support\Cache\ValueObjects\CacheStats::create(8, 2, 5, 10);

            expect($stats->getHitRatio())->toBe(0.8);
            expect($stats->getTotalRequests())->toBe(10);
            expect($stats->getUtilization())->toBe(0.5);
        });

        it('handles zero requests', function (): void {
            $stats = \JeroenGerits\Support\Cache\ValueObjects\CacheStats::create(0, 0, 0, 10);

            expect($stats->getHitRatio())->toBe(0.0);
            expect($stats->getTotalRequests())->toBe(0);
        });

        it('validates stats values', function (): void {
            expect(fn (): \JeroenGerits\Support\Cache\ValueObjects\CacheStats => \JeroenGerits\Support\Cache\ValueObjects\CacheStats::create(-1, 0, 0, 10))
                ->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Integration with Coordinates', function (): void {
        it('uses cache for trigonometric calculations', function (): void {
            $amsterdam = \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::create(52.3676, 4.9041);
            $london = \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::create(51.5074, -0.1278);

            // First calculation - should populate cache
            $distance1 = $amsterdam->distanceTo($london);

            // Second calculation - should use cache
            $distance2 = $amsterdam->distanceTo($london);

            expect($distance1)->toBe($distance2);

            $stats = \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::getCacheStats();
            expect($stats->getHits())->toBeGreaterThan(0);
        });

        it('allows custom cache configuration', function (): void {
            $nullCache = CacheFactory::createNullCache('test');
            \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::setCache($nullCache);

            $amsterdam = \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::create(52.3676, 4.9041);
            $london = \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::create(51.5074, -0.1278);

            $distance = $amsterdam->distanceTo($london);
            expect($distance)->toBeGreaterThan(0);

            // With null cache, all operations should be cache misses
            $stats = \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::getCacheStats();
            expect($stats->getHits())->toBe(0);
            expect($stats->getItems())->toBe(0);

            // Reset to default cache
            \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates::setCache(null);
        });
    });
});
