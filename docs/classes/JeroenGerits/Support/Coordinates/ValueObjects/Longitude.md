
Value object representing a longitude coordinate.

This immutable value object represents a longitude value in decimal degrees,
with automatic validation to ensure the value is within the valid range
of -180.0 to +180.0 degrees.

***

* Full name: `\JeroenGerits\Support\Coordinates\ValueObjects\Longitude`
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

Create a new Longitude instance.

```php
public __construct(float $value): mixed
```

**Parameters:**

| Parameter | Type      | Description                                               |
|-----------|-----------|-----------------------------------------------------------|
| `$value`  | **float** | The longitude value in decimal degrees (-180.0 to +180.0) |

**Throws:**

When longitude value is outside valid range
- [`InvalidCoordinatesException`](../Exceptions/InvalidCoordinatesException)

***

### __toString

Get the string representation of the longitude.

```php
public __toString(): string
```

**Return Value:**

The longitude as a string

***

### toString

Convert the longitude value to a string.

```php
public toString(): string
```

**Return Value:**

The longitude value as a string

***

### isEqual

Check if this longitude is equal to another.

```php
public isEqual(\JeroenGerits\Support\Contracts\Equatable $other): bool
```

**Parameters:**

| Parameter | Type                                          | Description                 |
|-----------|-----------------------------------------------|-----------------------------|
| `$other`  | **\JeroenGerits\Support\Contracts\Equatable** | The other object to compare |

**Return Value:**

True if the longitudes are equal

***
