## Introduction

Simple geographic coordinates with distance calculations.

## Creating Coordinates
```php
$amsterdam = Coordinates::create(52.3676, 4.9041);
$london = Coordinates::create(51.5074, -0.1278);
```

## Calculating Distance

```php
$amsterdam = Coordinates::create(52.3676, 4.9041);
$london = Coordinates::create(51.5074, -0.1278);

$distance = $amsterdam->distanceTo($london); 
$distanceMiles = $amsterdam->distanceTo($london, DistanceUnit::MILES);
```