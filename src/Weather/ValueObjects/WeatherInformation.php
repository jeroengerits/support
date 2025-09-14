<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Weather\ValueObjects;

use JeroenGerits\Support\Shared\Contracts\Equatable;
use Stringable;

class WeatherInformation implements Equatable, Stringable
{
    public function __construct(
        public readonly float $temperature,
        public readonly string $description,
        public readonly int $humidity,
        public readonly float $pressure,
        public readonly float $windSpeed,
        public readonly int $windDirection,
        public readonly ?string $icon = null,
        public readonly ?string $location = null,
        public readonly ?\DateTimeImmutable $timestamp = null
    ) {}

    public function isEqual(Equatable $other): bool
    {
        return $other instanceof self
            && $this->temperature === $other->temperature
            && $this->description === $other->description
            && $this->location === $other->location;
    }

    public function __toString(): string
    {
        return sprintf('%.1f°C, %s', $this->temperature, $this->description);
    }

    public function getTemperatureInFahrenheit(): float
    {
        return ($this->temperature * 9 / 5) + 32;
    }

    public function getWindDirectionName(): string
    {
        $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
        $index = round($this->windDirection / 22.5) % 16;

        return $directions[$index];
    }

    public function getFormattedLocation(): string
    {
        return $this->location ?? 'Unknown Location';
    }
}
