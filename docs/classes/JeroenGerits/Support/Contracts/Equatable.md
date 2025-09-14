
Interface for objects that can be compared for equality.

This interface defines a contract for objects that can determine
whether they are equal to another object of the same type. This is
particularly useful for value objects where equality is based on
the object's content rather than its identity.

***

* Full name: `\JeroenGerits\Support\Contracts\Equatable`

## Methods

### isEqual

Check if this object is equal to another.

```php
public isEqual(\JeroenGerits\Support\Contracts\Equatable $other): bool
```

This method should implement value-based equality comparison,
where two objects are considered equal if they represent the
same value, regardless of their object identity.

**Parameters:**

| Parameter | Type                                          | Description                 |
|-----------|-----------------------------------------------|-----------------------------|
| `$other`  | **\JeroenGerits\Support\Contracts\Equatable** | The other object to compare |

**Return Value:**

True if objects are equal, false otherwise

***
