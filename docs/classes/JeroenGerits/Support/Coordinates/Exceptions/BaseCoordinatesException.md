
Base exception class for coordinate-related errors.

This abstract class provides a foundation for all coordinate-related
exceptions with standardized error codes and consistent error handling.
All coordinate exceptions should extend this class to maintain
consistent error reporting across the library.

***

* Full name: `\JeroenGerits\Support\Coordinates\Exceptions\BaseCoordinatesException`
* Parent class: [`Exception`](../../../../Exception)
* This class is an **Abstract class**

## Constants

| Constant              | Visibility | Type | Value |
|-----------------------|------------|------|-------|
| `CODE_INVALID_VALUE`  | public     |      | 1001  |
| `CODE_OUT_OF_RANGE`   | public     |      | 1002  |
| `CODE_INVALID_TYPE`   | public     |      | 1003  |
| `CODE_MISSING_VALUE`  | public     |      | 1004  |
| `CODE_INVALID_FORMAT` | public     |      | 1005  |

## Methods

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
