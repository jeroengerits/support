# Coordinates

Provides a system for handling geographic coordinates with proper validation and type safety.

## Usage

```php
// Create Coordinates
coordinates(11.21, 22.34); // Returns a Coordinate Value object

// Calculate distance between Coordinates In Kilometers
coordinates(11,21)->distanceBetweenInKilometers(22,34) // Returns distance in kilometers
coordinates(11,21)->distanceBetweenInMiles(22,34) // Returns distance in miles

// Create Latitude
latitude(11.21); // Returns a Latitude Value object

// Create Longitude
longitude(11.21); // Returns a Longitude Value object

// Check if Coordinates are equal
coordinates(11, 22)->isEqual(coordinates(11, 22)); // Returns true
latitude(11.21)->isEqual(latitude(11.21)); // Returns true
longitude(11.21)->isEqual(longitude(11.21)); // Returns true
```