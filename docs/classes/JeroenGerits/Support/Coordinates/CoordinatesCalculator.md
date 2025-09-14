
Optimized calculator for coordinate-based distance calculations.

This class provides efficient distance calculations between coordinates using
the Haversine formula with caching, multiple Earth models, and batch processing
capabilities for improved performance.

***

* Full name: `\JeroenGerits\Support\Coordinates\CoordinatesCalculator`

## Constants

| Constant                | Visibility | Type                              | Value                                                                                                                                                                                                                                                          |
|-------------------------|------------|-----------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `EARTH_MODEL_SPHERICAL` | public     |                                   | 'spherical'                                                                                                                                                                                                                                                    |
| `EARTH_MODEL_WGS84`     | public     |                                   | 'wgs84'                                                                                                                                                                                                                                                        |
| `EARTH_MODEL_GRS80`     | public     |                                   | 'grs80'                                                                                                                                                                                                                                                        |
| `EARTH_RADII`           | private    | array<string,array<string,float>> | [self::EARTH_MODEL_SPHERICAL => ['km' => 6371.0, 'mi' => 3958.8], self::EARTH_MODEL_WGS84 => [
    'km' => 6371.0088,
    // Mean radius
    'mi' => 3958.7613,
], self::EARTH_MODEL_GRS80 => [
    'km' => 6371.0,
    // Mean radius
    'mi' => 3958.76,
]] |

## Properties

### trigCache

Cache for trigonometric calculations to avoid repeated computations.

```php
private static array<string,float> $trigCache
```

* This property is **static**.

***

### radiusCache

Cache for Earth radius values by unit and model.

```php
private static array<string,float> $radiusCache
```

* This property is **static**.

***

## Methods

### clearCache

Clear the trigonometric cache.

```php
public static clearCache(): void
```

This method can be called to free up memory if the cache becomes too large.
The cache will be rebuilt as needed for future calculations.

* This method is **static**.
***

### getCacheSize

Get the size of the trigonometric cache.

```php
public static getCacheSize(): int
```

* This method is **static**.
**Return Value:**

The number of cached trigonometric values

***

### distanceBetween

Calculate the distance between two coordinates using the Haversine formula.

```php
public distanceBetween(\JeroenGerits\Support\Coordinates\ValueObjects\Coordinates $a, \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates $b, \JeroenGerits\Support\Coordinates\Enums\DistanceUnit $unit = DistanceUnit::KILOMETERS, string $earthModel = self::EARTH_MODEL_WGS84): float
```

This method uses caching for trigonometric calculations and supports
different Earth models for improved accuracy.

**Parameters:**

| Parameter     | Type                                                           | Description                             |
|---------------|----------------------------------------------------------------|-----------------------------------------|
| `$a`          | **\JeroenGerits\Support\Coordinates\ValueObjects\Coordinates** | The first coordinate                    |
| `$b`          | **\JeroenGerits\Support\Coordinates\ValueObjects\Coordinates** | The second coordinate                   |
| `$unit`       | **\JeroenGerits\Support\Coordinates\Enums\DistanceUnit**       | The distance unit (default: KILOMETERS) |
| `$earthModel` | **string**                                                     | The Earth model to use (default: WGS84) |

**Return Value:**

The distance between the coordinates

***

### getEarthRadius

Get the Earth radius for the specified unit and model.

```php
private getEarthRadius(\JeroenGerits\Support\Coordinates\Enums\DistanceUnit $unit, string $earthModel): float
```

**Parameters:**

| Parameter     | Type                                                     | Description            |
|---------------|----------------------------------------------------------|------------------------|
| `$unit`       | **\JeroenGerits\Support\Coordinates\Enums\DistanceUnit** | The distance unit      |
| `$earthModel` | **string**                                               | The Earth model to use |

**Return Value:**

The Earth radius in the specified unit

**Throws:**

When the Earth model is not supported
- [`InvalidArgumentException`](../../../InvalidArgumentException)

***

### getCachedRadians

Get cached radians conversion.

```php
private getCachedRadians(float $degrees): float
```

**Parameters:**

| Parameter  | Type      | Description                             |
|------------|-----------|-----------------------------------------|
| `$degrees` | **float** | The degrees value to convert to radians |

**Return Value:**

The radians value

***

### getCachedSin

Get cached sine value.

```php
private getCachedSin(float $radians): float
```

**Parameters:**

| Parameter  | Type      | Description                             |
|------------|-----------|-----------------------------------------|
| `$radians` | **float** | The radians value to calculate sine for |

**Return Value:**

The sine value

***

### getCachedCos

Get cached cosine value.

```php
private getCachedCos(float $radians): float
```

**Parameters:**

| Parameter  | Type      | Description                               |
|------------|-----------|-------------------------------------------|
| `$radians` | **float** | The radians value to calculate cosine for |

**Return Value:**

The cosine value

***

### batchDistanceCalculation

Calculate distances for multiple coordinate pairs in batch.

```php
public batchDistanceCalculation(array{: \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates, : \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates}[] $coordinatePairs, \JeroenGerits\Support\Coordinates\Enums\DistanceUnit $unit = DistanceUnit::KILOMETERS, string $earthModel = self::EARTH_MODEL_WGS84): float[]
```

This method is optimized for calculating distances between multiple
coordinate pairs efficiently by reusing cached calculations.

**Parameters:**

| Parameter          | Type                                                                                                                                    | Description                             |
|--------------------|-----------------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------|
| `$coordinatePairs` | **array{: \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates, : \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates}[]** | Array of coordinate pairs               |
| `$unit`            | **\JeroenGerits\Support\Coordinates\Enums\DistanceUnit**                                                                                | The distance unit (default: KILOMETERS) |
| `$earthModel`      | **string**                                                                                                                              | The Earth model to use (default: WGS84) |

**Return Value:**

Array of distances corresponding to each coordinate pair

***
