<?php

declare(strict_types=1);

use JeroenGerits\Support\Contracts\ValueObject;

/**
 * Test implementation of ValueObject for testing purposes.
 */
final readonly class TestValueObject implements ValueObject
{
    public function __construct(
        private mixed $value
    ) {}

    public function equals(ValueObject $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toArray(): array
    {
        return ['value' => $this->value];
    }

    public function __toString(): string
    {
        if (is_object($this->value)) {
            return get_class($this->value);
        }

        return (string) $this->value;
    }
}

it('defines the required methods', function (): void {
    $reflection = new ReflectionClass(ValueObject::class);

    expect($reflection->isInterface())->toBeTrue()
        ->and($reflection->hasMethod('equals'))->toBeTrue()
        ->and($reflection->hasMethod('toArray'))->toBeTrue();
});

it('extends Stringable interface', function (): void {
    $reflection = new ReflectionClass(ValueObject::class);
    $interfaces = $reflection->getInterfaceNames();

    expect($interfaces)->toContain(Stringable::class);
});

it('can be implemented by a concrete class', function (): void {
    $valueObject = new TestValueObject('test');

    expect($valueObject)->toBeInstanceOf(ValueObject::class)
        ->and($valueObject->toArray())->toBe(['value' => 'test'])
        ->and((string) $valueObject)->toBe('test');
});

it('supports equality comparison', function (): void {
    $value1 = new TestValueObject('test');
    $value2 = new TestValueObject('test');
    $value3 = new TestValueObject('different');

    expect($value1->equals($value2))->toBeTrue()
        ->and($value1->equals($value3))->toBeFalse();
});

it('supports different value types', function (mixed $value): void {
    $valueObject = new TestValueObject($value);
    $expectedString = is_object($value) ? get_class($value) : (string) $value;

    expect($valueObject->toArray())->toBe(['value' => $value])
        ->and((string) $valueObject)->toBe($expectedString);
})->with([
    'string',
    123,
    45.67,
    true,
    false,
    null,
    ['array', 'value'],
    (object) ['key' => 'value'],
]);
