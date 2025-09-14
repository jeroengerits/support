# Contracts

This page documents the shared interfaces and contracts used throughout the Support package.

## Equatable

The `Equatable` interface provides a standardized way for objects to compare themselves for equality. This is particularly useful for value objects where equality should be based on the object's content rather than its identity.

### Interface Definition

```php
interface Equatable
{
    public function isEqual(Equatable $other): bool;
}
```

### Basic Usage

You may implement the `Equatable` interface on any class that needs custom equality comparison:

```php
use JeroenGerits\Support\Shared\Contracts\Equatable;

class ExampleValue implements Equatable
{
    public function __construct(public readonly string $value) {}
    
    public function isEqual(Equatable $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }
}
```

### Implementation Guidelines

When implementing the `Equatable` interface, you should:

- Always check if the other object is an instance of the same class
- Compare the meaningful content of the objects
- Handle null or invalid inputs gracefully
- Keep equality checks efficient for frequently compared objects

### Real-World Example

Here's how the `Coordinates` class implements the `Equatable` interface:

```php
class Coordinates implements Equatable
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude
    ) {}
    
    public function isEqual(Equatable $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        
        return $this->latitude === $other->latitude 
            && $this->longitude === $other->longitude;
    }
}
```

### Available Methods

#### `isEqual(Equatable $other)`

Determine if the current object is equal to another object.

**Parameters:**
- `Equatable $other` - The other object to compare

**Returns:** `bool` - `true` if the objects are equal, `false` otherwise
