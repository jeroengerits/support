
This is an automatically generated documentation for **Support**.

## Namespaces

### \

#### Functions

| Function                                   | Description                                                           |
|--------------------------------------------|-----------------------------------------------------------------------|
| [`coordinates()`](./functions/coordinates) | Create a new Coordinates instance from latitude and longitude values. |
| [`latitude()`](./functions/latitude)       | Create a new Latitude instance from various input types.              |
| [`longitude()`](./functions/longitude)     | Create a new Longitude instance from various input types.             |

### \JeroenGerits\Support\Contracts

#### Interfaces

| Interface                                                         | Description                                              |
|-------------------------------------------------------------------|----------------------------------------------------------|
| [`Equatable`](./classes/JeroenGerits/Support/Contracts/Equatable) | Interface for objects that can be compared for equality. |

### \JeroenGerits\Support\Coordinates

#### Classes

| Class                                                                                       | Description                                                      |
|---------------------------------------------------------------------------------------------|------------------------------------------------------------------|
| [`CoordinatesCalculator`](./classes/JeroenGerits/Support/Coordinates/CoordinatesCalculator) | Optimized calculator for coordinate-based distance calculations. |
| [`CoordinatesFactory`](./classes/JeroenGerits/Support/Coordinates/CoordinatesFactory)       | Factory class for creating coordinate-related objects.           |

### \JeroenGerits\Support\Coordinates\Exceptions

#### Classes

| Class                                                                                                              | Description                                             |
|--------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------|
| [`BaseCoordinatesException`](./classes/JeroenGerits/Support/Coordinates/Exceptions/BaseCoordinatesException)       | Base exception class for coordinate-related errors.     |
| [`InvalidCoordinatesException`](./classes/JeroenGerits/Support/Coordinates/Exceptions/InvalidCoordinatesException) | Exception thrown when invalid coordinates are provided. |

### \JeroenGerits\Support\Coordinates\ValueObjects

#### Classes

| Class                                                                                | Description                                       |
|--------------------------------------------------------------------------------------|---------------------------------------------------|
| [`Coordinates`](./classes/JeroenGerits/Support/Coordinates/ValueObjects/Coordinates) | Value object representing geographic coordinates. |
| [`Latitude`](./classes/JeroenGerits/Support/Coordinates/ValueObjects/Latitude)       | Value object representing a latitude coordinate.  |
| [`Longitude`](./classes/JeroenGerits/Support/Coordinates/ValueObjects/Longitude)     | Value object representing a longitude coordinate. |
