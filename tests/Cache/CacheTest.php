<?php

declare(strict_types=1);

use JeroenGerits\Support\Cache\CacheFactory;
use JeroenGerits\Support\Cache\Exceptions\InvalidCacheKeyException;
use JeroenGerits\Support\Cache\Exceptions\InvalidTimeToLiveException;
use JeroenGerits\Support\Cache\Traits\HasCache;
use JeroenGerits\Support\Cache\ValueObjects\CacheKey;
use JeroenGerits\Support\Cache\ValueObjects\CacheStats;
use JeroenGerits\Support\Cache\ValueObjects\TimeToLive;
use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;

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
            $currentTime = time();
            $cache = CacheFactory::createArrayCacheWithTimeProvider('test', 10, function () use (&$currentTime): int {
                return $currentTime;
            });

            $cache->set('key1', 'value1', 1); // 1 second TTL
            expect($cache->get('key1'))->toBe('value1');

            // Simulate time passing by 2 seconds
            $currentTime += 2;
            expect($cache->get('key1'))->toBeNull();
        });

        it('supports TTL with DateInterval', function (): void {
            $currentTime = time();
            $cache = CacheFactory::createArrayCacheWithTimeProvider('test', 10, function () use (&$currentTime): int {
                return $currentTime;
            });

            // Test with DateInterval - should use accurate DateTime calculations
            $interval = new DateInterval('PT1S'); // 1 second
            $cache->set('key1', 'value1', $interval);
            expect($cache->get('key1'))->toBe('value1');

            // Simulate time passing by 2 seconds
            $currentTime += 2;
            expect($cache->get('key1'))->toBeNull();
        });

        it('has() method is optimized and does not use get() internally', function (): void {
            $currentTime = time();
            $cache = CacheFactory::createArrayCacheWithTimeProvider('test', 10, function () use (&$currentTime): int {
                return $currentTime;
            });

            $cache->set('key1', 'value1');
            expect($cache->has('key1'))->toBeTrue();

            $cache->delete('key1');
            expect($cache->has('key1'))->toBeFalse();

            // Test with expired item
            $cache->set('key1', 'value1', 1);
            expect($cache->has('key1'))->toBeTrue();

            // Simulate time passing by 2 seconds
            $currentTime += 2;
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
            expect(fn (): TimeToLive => new TimeToLive(-1))->toThrow(InvalidTimeToLiveException::class);
        });
    });

    describe('CacheKey Value Object', function (): void {
        it('creates with key and namespace', function (): void {
            $key = CacheKey::create('test-key', 'namespace');

            expect($key->key)->toBe('test-key');
            expect($key->namespace)->toBe('namespace');
            expect((string) $key)->toBe('namespace:test-key');
        });

        it('validates non-empty key', function (): void {
            expect(fn (): CacheKey => CacheKey::create('', 'namespace'))
                ->toThrow(InvalidCacheKeyException::class);
        });

        it('validates key without colons', function (): void {
            expect(fn (): CacheKey => CacheKey::create('key:with:colons', 'namespace'))
                ->toThrow(InvalidCacheKeyException::class);
        });
    });

    describe('CacheStats Value Object', function (): void {
        it('calculates hit ratio correctly', function (): void {
            $stats = CacheStats::create(8, 2, 5, 10);

            expect($stats->getHitRatio())->toBe(0.8);
            expect($stats->getTotalRequests())->toBe(10);
            expect($stats->getUtilization())->toBe(0.5);
        });

        it('handles zero requests', function (): void {
            $stats = CacheStats::create(0, 0, 0, 10);

            expect($stats->getHitRatio())->toBe(0.0);
            expect($stats->getTotalRequests())->toBe(0);
        });

        it('validates stats values', function (): void {
            expect(fn (): CacheStats => CacheStats::create(-1, 0, 0, 10))
                ->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Integration with Coordinates', function (): void {
        it('uses cache for trigonometric calculations', function (): void {
            $amsterdam = Coordinates::create(52.3676, 4.9041);
            $london = Coordinates::create(51.5074, -0.1278);

            // First calculation - should populate cache
            $distance1 = $amsterdam->distanceTo($london);

            // Second calculation - should use cache
            $distance2 = $amsterdam->distanceTo($london);

            expect($distance1)->toBe($distance2);

            $stats = Coordinates::getCache()->getStats();
            expect($stats->getHits())->toBeGreaterThan(0);
        });

        it('allows custom cache configuration', function (): void {
            $nullCache = CacheFactory::createNullCache('test');
            Coordinates::setCache($nullCache);

            $amsterdam = Coordinates::create(52.3676, 4.9041);
            $london = Coordinates::create(51.5074, -0.1278);

            $distance = $amsterdam->distanceTo($london);
            expect($distance)->toBeGreaterThan(0);

            // With null cache, all operations should be cache misses
            $stats = Coordinates::getCache()->getStats();
            expect($stats->getHits())->toBe(0);
            expect($stats->getItems())->toBe(0);

            // Reset to default cache
            Coordinates::setCache(null);
        });
    });

    describe('HasCache Trait', function (): void {
        /**
         * Test class that uses the HasCache trait.
         */
        class TestCachedClass
        {
            use HasCache;

            public function __construct(
                private readonly string $id
            ) {}

            /**
             * Get the ID for cache key generation.
             */
            public function getId(): string
            {
                return $this->id;
            }

            /**
             * Example method that uses caching.
             */
            public function getExpensiveData(string $type): array
            {
                return $this->testCacheRemember("expensive_data_{$type}", function () use ($type): array {
                    // Simulate expensive operation
                    return [
                        'type' => $type,
                        'id' => $this->id,
                        'computed_at' => time(),
                        'data' => str_repeat('x', 1000), // Simulate large data
                    ];
                });
            }

            /**
             * Example method that always computes and caches.
             */
            public function updateAndCache(string $type, array $data): array
            {
                return $this->testCachePut("updated_data_{$type}", function () use ($type, $data): array {
                    return array_merge($data, [
                        'type' => $type,
                        'id' => $this->id,
                        'updated_at' => time(),
                    ]);
                });
            }
        }

        beforeEach(function (): void {
            $this->currentTime = time();
            $this->cache = CacheFactory::createArrayCacheWithTimeProvider('test', 100, function () {
                return $this->currentTime;
            });
            $this->cachedClass = new TestCachedClass('test-id');
            $this->cachedClass
                ->setCache($this->cache)
                ->setCacheNamespace('test-class')
                ->setCacheDefaultTtl(3600);
        });

        describe('Configuration', function (): void {
            it('sets and gets cache adapter', function (): void {
                $nullCache = CacheFactory::createNullCache('test');
                $this->cachedClass->setCache($nullCache);

                expect($this->cachedClass->getCache())->toBe($nullCache);
            });

            it('sets and gets cache namespace', function (): void {
                $this->cachedClass->setCacheNamespace('custom-namespace');

                expect($this->cachedClass->getCacheNamespace())->toBe('custom-namespace');
            });

            it('sets and gets default TTL', function (): void {
                $this->cachedClass->setCacheDefaultTtl(7200);

                expect($this->cachedClass->getCacheDefaultTtl())->toBe(7200);
            });

            it('checks if cache is enabled', function (): void {
                expect($this->cachedClass->isCacheEnabled())->toBeTrue();

                $this->cachedClass->setCache(null);
                expect($this->cachedClass->isCacheEnabled())->toBeFalse();

                $this->cachedClass->setCache($this->cache)->setCacheNamespace(null);
                expect($this->cachedClass->isCacheEnabled())->toBeFalse();
            });
        });

        describe('Cache Key Generation', function (): void {
            it('generates cache keys with namespace', function (): void {
                $key = $this->cachedClass->testGenerateCacheKey('test', 'data', '123');

                expect($key)->toBe('test-class_test_data_123');
            });

            it('throws exception when namespace is not set', function (): void {
                $this->cachedClass->setCacheNamespace(null);

                expect(fn (): string => $this->cachedClass->testGenerateCacheKey('test'))
                    ->toThrow(RuntimeException::class, 'Cache namespace must be set before generating cache keys');
            });
        });

        describe('Basic Cache Operations', function (): void {
            it('stores and retrieves values', function (): void {
                $result = $this->cachedClass->testCacheSet('test-key', 'test-value');

                expect($result)->toBeTrue();
                expect($this->cachedClass->testCacheGet('test-key'))->toBe('test-value');
            });

            it('returns default value for missing keys', function (): void {
                $result = $this->cachedClass->testCacheGet('missing-key', 'default-value');

                expect($result)->toBe('default-value');
            });

            it('checks if keys exist', function (): void {
                $this->cachedClass->testCacheSet('test-key', 'test-value');

                expect($this->cachedClass->testCacheHas('test-key'))->toBeTrue();
                expect($this->cachedClass->testCacheHas('missing-key'))->toBeFalse();
            });

            it('deletes keys', function (): void {
                $this->cachedClass->testCacheSet('test-key', 'test-value');
                expect($this->cachedClass->testCacheHas('test-key'))->toBeTrue();

                $result = $this->cachedClass->testCacheDelete('test-key');

                expect($result)->toBeTrue();
                expect($this->cachedClass->testCacheHas('test-key'))->toBeFalse();
            });

            it('uses custom TTL', function (): void {
                $result = $this->cachedClass->testCacheSet('test-key', 'test-value', 1);

                expect($result)->toBeTrue();
                expect($this->cachedClass->testCacheGet('test-key'))->toBe('test-value');

                // Simulate time passing by 2 seconds
                $this->currentTime += 2;
                expect($this->cachedClass->testCacheGet('test-key'))->toBeNull();
            });

            it('uses DateInterval TTL', function (): void {
                $interval = new DateInterval('PT1S'); // 1 second
                $result = $this->cachedClass->testCacheSet('test-key', 'test-value', $interval);

                expect($result)->toBeTrue();
                expect($this->cachedClass->testCacheGet('test-key'))->toBe('test-value');

                // Simulate time passing by 2 seconds
                $this->currentTime += 2;
                expect($this->cachedClass->testCacheGet('test-key'))->toBeNull();
            });
        });

        describe('Multiple Cache Operations', function (): void {
            it('stores and retrieves multiple values', function (): void {
                $values = [
                    'key1' => 'value1',
                    'key2' => 'value2',
                    'key3' => 'value3',
                ];

                $result = $this->cachedClass->testCacheSetMultiple($values);

                expect($result)->toBeTrue();

                $retrieved = $this->cachedClass->testCacheGetMultiple(['key1', 'key2', 'key3']);

                expect($retrieved)->toBe($values);
            });

            it('handles missing keys in multiple retrieval', function (): void {
                $this->cachedClass->testCacheSet('key1', 'value1');

                $retrieved = $this->cachedClass->testCacheGetMultiple(['key1', 'key2'], 'default');

                expect($retrieved)->toBe([
                    'key1' => 'value1',
                    'key2' => 'default',
                ]);
            });

            it('deletes multiple keys', function (): void {
                $this->cachedClass->testCacheSetMultiple([
                    'key1' => 'value1',
                    'key2' => 'value2',
                    'key3' => 'value3',
                ]);

                $result = $this->cachedClass->testCacheDeleteMultiple(['key1', 'key3']);

                expect($result)->toBeTrue();
                expect($this->cachedClass->testCacheHas('key1'))->toBeFalse();
                expect($this->cachedClass->testCacheHas('key2'))->toBeTrue();
                expect($this->cachedClass->testCacheHas('key3'))->toBeFalse();
            });
        });

        describe('Cache Remember Pattern', function (): void {
            it('returns cached value on second call', function (): void {
                $callCount = 0;
                $callback = function () use (&$callCount): string {
                    $callCount++;

                    return "computed-value-{$callCount}";
                };

                // First call should execute callback
                $result1 = $this->cachedClass->testCacheRemember('test-key', $callback);

                expect($result1)->toBe('computed-value-1');
                expect($callCount)->toBe(1);

                // Second call should return cached value
                $result2 = $this->cachedClass->testCacheRemember('test-key', $callback);

                expect($result2)->toBe('computed-value-1');
                expect($callCount)->toBe(1); // Should not increment
            });

            it('uses custom TTL in remember', function (): void {
                $callback = fn (): string => 'test-value';

                $this->cachedClass->testCacheRemember('test-key', $callback, 1);

                expect($this->cachedClass->testCacheGet('test-key'))->toBe('test-value');

                // Simulate time passing by 2 seconds
                $this->currentTime += 2;
                $result = $this->cachedClass->testCacheRemember('test-key', $callback, 1);

                expect($result)->toBe('test-value'); // Should recompute
            });
        });

        describe('Cache Put Pattern', function (): void {
            it('always executes callback and caches result', function (): void {
                $callCount = 0;
                $callback = function () use (&$callCount): string {
                    $callCount++;

                    return "computed-value-{$callCount}";
                };

                // First call
                $result1 = $this->cachedClass->testCachePut('test-key', $callback);

                expect($result1)->toBe('computed-value-1');
                expect($callCount)->toBe(1);

                // Second call should execute callback again
                $result2 = $this->cachedClass->testCachePut('test-key', $callback);

                expect($result2)->toBe('computed-value-2');
                expect($callCount)->toBe(2);
            });
        });

        describe('Cache Statistics', function (): void {
            it('returns cache statistics when enabled', function (): void {
                $this->cachedClass->testCacheSet('test-key', 'test-value');
                $this->cachedClass->testCacheGet('test-key'); // Hit
                $this->cachedClass->testCacheGet('missing-key'); // Miss

                $stats = $this->cachedClass->testGetCacheStats();

                expect($stats)->not->toBeNull();
                expect($stats->getHits())->toBe(1);
                expect($stats->getMisses())->toBe(1);
            });

            it('returns null when cache is disabled', function (): void {
                $this->cachedClass->setCache(null);

                $stats = $this->cachedClass->testGetCacheStats();

                expect($stats)->toBeNull();
            });
        });

        describe('Cache Disabled Behavior', function (): void {
            beforeEach(function (): void {
                $this->cachedClass->setCache(null);
            });

            it('returns false for set operations when cache disabled', function (): void {
                $result = $this->cachedClass->testCacheSet('test-key', 'test-value');

                expect($result)->toBeFalse();
            });

            it('returns default for get operations when cache disabled', function (): void {
                $result = $this->cachedClass->testCacheGet('test-key', 'default');

                expect($result)->toBe('default');
            });

            it('returns false for has operations when cache disabled', function (): void {
                $result = $this->cachedClass->testCacheHas('test-key');

                expect($result)->toBeFalse();
            });

            it('executes callback directly when cache disabled in remember', function (): void {
                $callCount = 0;
                $callback = function () use (&$callCount): string {
                    $callCount++;

                    return 'test-value';
                };

                $result = $this->cachedClass->testCacheRemember('test-key', $callback);

                expect($result)->toBe('test-value');
                expect($callCount)->toBe(1);
            });
        });

        describe('Real-world Usage Example', function (): void {
            it('demonstrates practical caching usage', function (): void {
                $data1 = $this->cachedClass->getExpensiveData('users');
                $data2 = $this->cachedClass->getExpensiveData('users'); // Should be cached

                expect($data1)->toBe($data2);
                expect($data1['type'])->toBe('users');
                expect($data1['id'])->toBe('test-id');

                // Update and cache new data
                $updatedData = $this->cachedClass->updateAndCache('users', ['new_field' => 'new_value']);

                expect($updatedData['new_field'])->toBe('new_value');
                expect($updatedData['type'])->toBe('users');
                expect($updatedData['id'])->toBe('test-id');

                // Verify cache statistics
                $stats = $this->cachedClass->testGetCacheStats();
                expect($stats->getHits())->toBeGreaterThan(0);
            });
        });

        describe('Fluent Interface', function (): void {
            it('supports method chaining', function (): void {
                $result = $this->cachedClass
                    ->setCache($this->cache)
                    ->setCacheNamespace('fluent-test')
                    ->setCacheDefaultTtl(1800);

                expect($result)->toBe($this->cachedClass);
                expect($this->cachedClass->getCacheNamespace())->toBe('fluent-test');
                expect($this->cachedClass->getCacheDefaultTtl())->toBe(1800);
            });
        });
    });
});
