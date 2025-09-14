
Value object representing geographic coordinates.

This immutable value object represents a point on Earth using latitude
and longitude values. It provides methods for distance calculations,
equality comparison, and string representation.

***

* Full name: `\JeroenGerits\Support\Coordinates\ValueObjects\Coordinates`
* This class implements:
  [`\JeroenGerits\Support\Contracts\Equatable`](../../Contracts/Equatable),
  `Stringable`

## Properties

### latitude

```php
public \JeroenGerits\Support\Coordinates\ValueObjects\Latitude $latitude
```

***

### longitude

```php
public \JeroenGerits\Support\Coordinates\ValueObjects\Longitude $longitude
```

***

## Methods

### __construct

Create a new Coordinates instance.

```php
public __construct(\JeroenGerits\Support\Coordinates\ValueObjects\Latitude $latitude, \JeroenGerits\Support\Coordinates\ValueObjects\Longitude $longitude): mixed
```

**Parameters:**

| Parameter    | Type                                                         | Description                |
|--------------|--------------------------------------------------------------|----------------------------|
| `$latitude`  | **\JeroenGerits\Support\Coordinates\ValueObjects\Latitude**  | The latitude value object  |
| `$longitude` | **\JeroenGerits\Support\Coordinates\ValueObjects\Longitude** | The longitude value object |

***

### __toString

Get the string representation of the coordinates.

```php
public __toString(): string
```

**Return Value:**

The coordinates as "latitude,longitude"

***

### isEqual

Check if this coordinates object is equal to another.

```php
public isEqual(\JeroenGerits\Support\Contracts\Equatable $other): bool
```

**Parameters:**

| Parameter | Type                                          | Description                 |
|-----------|-----------------------------------------------|-----------------------------|
| `$other`  | **\JeroenGerits\Support\Contracts\Equatable** | The other object to compare |

**Return Value:**

True if the coordinates are equal

***

### distanceBetween

Calculate the distance between two coordinates.

```php
public distanceBetween(mixed $latitude, mixed $longitude = null, \JeroenGerits\Support\Coordinates\Enums\DistanceUnit $unit = DistanceUnit::KILOMETERS): float
```

**Parameters:**

| Parameter    | Type                                                     | Description                                                           |
|--------------|----------------------------------------------------------|-----------------------------------------------------------------------|
| `$latitude`  | **mixed**                                                | The latitude value or Coordinates object                              |
| `$longitude` | **mixed**                                                | The longitude value (optional when $latitude is a Coordinates object) |
| `$unit`      | **\JeroenGerits\Support\Coordinates\Enums\DistanceUnit** | The unit of distance to return                                        |

**Return Value:**

The distance between the two coordinates

***

### distanceBetweenInMiles

Calculate the distance between two coordinates in miles.

```php
public distanceBetweenInMiles(mixed $latitude, mixed $longitude = null): float
```

**Parameters:**

| Parameter    | Type      | Description                                                           |
|--------------|-----------|-----------------------------------------------------------------------|
| `$latitude`  | **mixed** | The latitude value or Coordinates object                              |
| `$longitude` | **mixed** | The longitude value (optional when $latitude is a Coordinates object) |

**Return Value:**

The distance between the two coordinates in miles

***

### distanceBetweenInKilometers

Calculate the distance between two coordinates in kilometers.

```php
public distanceBetweenInKilometers(mixed $latitude, mixed $longitude = null): float
```

**Parameters:**

| Parameter    | Type      | Description                                                           |
|--------------|-----------|-----------------------------------------------------------------------|
| `$latitude`  | **mixed** | The latitude value or Coordinates object                              |
| `$longitude` | **mixed** | The longitude value (optional when $latitude is a Coordinates object) |

**Return Value:**

The distance between the two coordinates in kilometers

***
