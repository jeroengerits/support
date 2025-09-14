
Factory class for creating coordinate-related objects.

This factory provides convenient methods for creating Coordinates, Latitude,
and Longitude objects from various input types with automatic validation
and type conversion.

***

* Full name: `\JeroenGerits\Support\Coordinates\CoordinatesFactory`

## Methods

### createCoordinates

Create a new Coordinates instance from latitude and longitude values.

```php
public static createCoordinates(mixed $latitude = null, mixed $longitude = null): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates
```

This method provides flexible input handling for creating coordinate
objects from various data types including arrays, individual values,
or existing coordinate objects.

* This method is **static**.
**Parameters:**

| Parameter    | Type      | Description                                               |
|--------------|-----------|-----------------------------------------------------------|
| `$latitude`  | **mixed** | The latitude value or array containing both coordinates   |
| `$longitude` | **mixed** | The longitude value (optional when $latitude is an array) |

**Throws:**

When latitude or longitude values are invalid
- [`\InvalidArgumentException|\JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException`](../../../InvalidArgumentException|/JeroenGerits/Support/Coordinates/Exceptions/InvalidCoordinatesException)

***

### createFromArray

Create a new Coordinates instance from an array.

```php
public static createFromArray(array $array): \JeroenGerits\Support\Coordinates\ValueObjects\Coordinates
```

This method supports multiple array formats including named keys
(lat/lng, latitude/longitude) and numeric indexed arrays.

* This method is **static**.
**Parameters:**

| Parameter | Type      | Description                          |
|-----------|-----------|--------------------------------------|
| `$array`  | **array** | The array containing coordinate data |

**Throws:**

When coordinate values are missing or malformed
- [`InvalidCoordinatesException`](./Exceptions/InvalidCoordinatesException)

***

### createLatitude

Create a new Latitude instance from various input types.

```php
public static createLatitude(mixed $value): \JeroenGerits\Support\Coordinates\ValueObjects\Latitude
```

This method handles automatic type conversion and validation for
latitude values, ensuring they fall within the valid range of
-90.0 to +90.0 degrees.

* This method is **static**.
**Parameters:**

| Parameter | Type      | Description                                                   |
|-----------|-----------|---------------------------------------------------------------|
| `$value`  | **mixed** | The latitude value (float, string, int, or Latitude instance) |

**Throws:**

When latitude value is invalid
- [`\InvalidArgumentException|\JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException`](../../../InvalidArgumentException|/JeroenGerits/Support/Coordinates/Exceptions/InvalidCoordinatesException)

***

### createLongitude

Create a new Longitude instance from various input types.

```php
public static createLongitude(mixed $value): \JeroenGerits\Support\Coordinates\ValueObjects\Longitude
```

This method handles automatic type conversion and validation for
longitude values, ensuring they fall within the valid range of
-180.0 to +180.0 degrees.

* This method is **static**.
**Parameters:**

| Parameter | Type      | Description                                                     |
|-----------|-----------|-----------------------------------------------------------------|
| `$value`  | **mixed** | The longitude value (float, string, int, or Longitude instance) |

**Throws:**

When longitude value is invalid
- [`\InvalidArgumentException|\JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException`](../../../InvalidArgumentException|/JeroenGerits/Support/Coordinates/Exceptions/InvalidCoordinatesException)

***
