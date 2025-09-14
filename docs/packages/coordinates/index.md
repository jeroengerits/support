# Coordinates

The `Coordinates` class provides a simple, fluent interface for working with geographic coordinates and calculating distances between points.

## Usage

You may create `Coordinates` instances using the `create` method:

```php
Coordinates::create(52.3676, 4.9041); // Returns Coordinates
```

## Distance

The `distanceTo` method calculates the distance between two `Coordinates`:

```php
$amsterdam = Coordinates::create(52.3676, 4.9041);
$london = Coordinates::create(51.5074, -0.1278);

$amsterdam->distanceTo($london); // Returns: 357.89 (kilometers)
```

To calculate distances in miles, pass `DistanceUnit::MILES` to the `distanceTo` method.

```php
$amsterdam->distanceTo($london, DistanceUnit::MILES); // Returns: 222.38 (miles)
```

## Compare 

The `Coordinates` class implements the `Equatable` interface, allowing you to compare coordinates for equality

```php
$amsterdam = Coordinates::create(52.3676, 4.9041);
$notAmsterdam = Coordinates::create(1,2);

$amsterdam->isEqual($amsterdam); // Returns: true
$amsterdam->isEqual($notAmsterdam); // Returns: false
```

## Advanced 

### Latitude & Longitude

You may also create a single `Latitude` or `Longitude` coordinate:

```php
Latitude::create(52.3676); // Returns Latitude
Longitude::create(4.9041); // Returns Longitude
```
