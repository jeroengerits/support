
Value object representing a latitude coordinate.

This immutable value object represents a latitude value in decimal degrees,
with automatic validation to ensure the value is within the valid range
of -90.0 to +90.0 degrees.

***

* Full name: `\JeroenGerits\Support\Coordinates\ValueObjects\Latitude`
* This class implements:
  [`\JeroenGerits\Support\Contracts\Equatable`](../../Contracts/Equatable),
  `Stringable`

## Properties

### value

```php
public float $value
```

***

## Methods

### __construct

Create a new Latitude instance.

```php
public __construct(float $value): mixed
```

**Parameters:**

| Parameter | Type      | Description                                            |
|-----------|-----------|--------------------------------------------------------|
| `$value`  | **float** | The latitude value in decimal degrees (-90.0 to +90.0) |

**Throws:**

When latitude value is outside valid range
- [`InvalidCoordinatesException`](../Exceptions/InvalidCoordinatesException)

***

### __toString

Get the string representation of the latitude.

```php
public __toString(): string
```

**Return Value:**

The latitude as a string

***

### toString

Convert the latitude value to a string.

```php
public toString(): string
```

**Return Value:**

The latitude value as a string

***

### isEqual

Check if this latitude is equal to another.

```php
public isEqual(\JeroenGerits\Support\Contracts\Equatable $other): bool
```

**Parameters:**

| Parameter | Type                                          | Description                 |
|-----------|-----------------------------------------------|-----------------------------|
| `$other`  | **\JeroenGerits\Support\Contracts\Equatable** | The other object to compare |

**Return Value:**

True if the latitudes are equal

***
