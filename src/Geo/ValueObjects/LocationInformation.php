<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Geo\ValueObjects;

use JeroenGerits\Support\Shared\Contracts\Equatable;
use Stringable;

class LocationInformation implements Equatable, Stringable
{
    public function __construct(
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $country = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $formattedAddress = null,
        public readonly ?string $timezone = null,
        public readonly ?float $confidence = null,
        public readonly ?string $countryCode = null
    ) {}

    public function isEqual(Equatable $other): bool
    {
        return $other instanceof self
            && $this->city === $other->city
            && $this->state === $other->state
            && $this->country === $other->country;
    }

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    public function getDisplayName(): string
    {
        $parts = array_filter([$this->city, $this->state, $this->country]);

        return implode(', ', $parts) ?: 'Unknown Location';
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function getConfidence(): ?float
    {
        return $this->confidence;
    }
}
