
Exception thrown when invalid coordinates are provided.

This exception is thrown when coordinate values are invalid, out of range,
have incorrect types, or are missing from required data structures.
It provides static factory methods for common error scenarios.

***

* Full name: `\JeroenGerits\Support\Coordinates\Exceptions\InvalidCoordinatesException`
* Parent class: [`\JeroenGerits\Support\Coordinates\Exceptions\BaseCoordinatesException`](./BaseCoordinatesException)

## Methods

### __construct

Create a new InvalidCoordinatesException instance.

```php
public __construct(string $message = 'Invalid coordinates provided', int $code = self::CODE_INVALID_VALUE, \Exception|null $previous = null): mixed
```

**Parameters:**

| Parameter   | Type                 | Description                         |
|-------------|----------------------|-------------------------------------|
| `$message`  | **string**           | The exception message               |
| `$code`     | **int**              | The exception code                  |
| `$previous` | **\Exception\|null** | The previous exception for chaining |

***

### latitudeOutOfRange

Create an exception for latitude values outside the valid range.

```php
public static latitudeOutOfRange(float $value): static
```

* This method is **static**.
**Parameters:**

| Parameter | Type      | Description                             |
|-----------|-----------|-----------------------------------------|
| `$value`  | **float** | The latitude value that is out of range |

**Return Value:**

A new InvalidCoordinatesException instance

***

### longitudeOutOfRange

Create an exception for longitude values outside the valid range.

```php
public static longitudeOutOfRange(float $value): static
```

* This method is **static**.
**Parameters:**

| Parameter | Type      | Description                              |
|-----------|-----------|------------------------------------------|
| `$value`  | **float** | The longitude value that is out of range |

**Return Value:**

A new InvalidCoordinatesException instance

***

### invalidType

Create an exception for invalid coordinate type.

```php
public static invalidType(mixed $value, string $coordinateType = 'coordinate'): static
```

* This method is **static**.
**Parameters:**

| Parameter         | Type       | Description                                                 |
|-------------------|------------|-------------------------------------------------------------|
| `$value`          | **mixed**  | The invalid value that was provided                         |
| `$coordinateType` | **string** | The type of coordinate (latitude, longitude, or coordinate) |

**Return Value:**

A new InvalidCoordinatesException instance

***

### invalidFormat

Create an exception for invalid coordinate format.

```php
public static invalidFormat(string $value, string $coordinateType = 'coordinate'): static
```

* This method is **static**.
**Parameters:**

| Parameter         | Type       | Description                                                 |
|-------------------|------------|-------------------------------------------------------------|
| `$value`          | **string** | The invalid format string that was provided                 |
| `$coordinateType` | **string** | The type of coordinate (latitude, longitude, or coordinate) |

**Return Value:**

A new InvalidCoordinatesException instance

***

### missingFromArray

Create an exception when coordinates are missing from array.

```php
public static missingFromArray(array $array, string $missingKey): static
```

* This method is **static**.
**Parameters:**

| Parameter     | Type       | Description                             |
|---------------|------------|-----------------------------------------|
| `$array`      | **array**  | The array that was provided             |
| `$missingKey` | **string** | The key that was missing from the array |

**Return Value:**

A new InvalidCoordinatesException instance

***

### invalidArrayStructure

Create an exception for invalid array structure.

```php
public static invalidArrayStructure(array $array): static
```

* This method is **static**.
**Parameters:**

| Parameter | Type      | Description                             |
|-----------|-----------|-----------------------------------------|
| `$array`  | **array** | The array that has an invalid structure |

**Return Value:**

A new InvalidCoordinatesException instance

***

## Inherited methods

### __construct

Create a new BaseCoordinatesException instance.

```php
public __construct(string $message = 'Invalid coordinate value provided', int $code = self::CODE_INVALID_VALUE, \Exception|null $previous = null): mixed
```

This constructor provides a consistent way to create coordinate-related
exceptions with standardized error codes and optional exception chaining.

**Parameters:**

| Parameter   | Type                 | Description                                |
|-------------|----------------------|--------------------------------------------|
| `$message`  | **string**           | The exception message describing the error |
| `$code`     | **int**              | The exception code (use class constants)   |
| `$previous` | **\Exception\|null** | The previous exception for chaining        |

***
