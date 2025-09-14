# Coordinates

Provides a system for handling geographic coordinates with proper validation and type safety.

## Usage

```php
// Create Coordinates
coordinates(11.21, 22.34); // Returns a CoordinateValue object

// Calculate distance between Coordinates In Kilometers
coordinates(11,21)->distanceTo(22,34) // Returns distance in kilometers

// Create Latitude
latitude(11.21); // Returns a LatitudeValue object

// Create Longitude
longitude(11.21); // Returns a LongitudeValue object

// Check if Coordinates are equal
coordinates(11, 22)->isEqual(coordinates(11, 22)); // Returns true
latitude(11.21)->isEqual(latitude(11.21)); // Returns true
longitude(11.21)->isEqual(longitude(11.21)); // Returns true
```