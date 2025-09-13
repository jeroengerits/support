<?php

if (! function_exists('longitude')) {
    /**
     * Create a Longitude instance from various input types.
     *
     * @param float|string|array $value The longitude value
     *
     * @throws \JeroenGerits\Support\Exception\InvalidLongitudeException
     */
    function longitude(float|string|array $value): \JeroenGerits\Support\ValueObject\Longitude
    {
        return match (true) {
            is_float($value) => new \JeroenGerits\Support\ValueObject\Longitude($value),
            is_string($value) => \JeroenGerits\Support\ValueObject\Longitude::fromString($value),
            is_array($value) && isset($value['longitude']) => new \JeroenGerits\Support\ValueObject\Longitude($value['longitude']),
            is_array($value) && isset($value[0]) => new \JeroenGerits\Support\ValueObject\Longitude($value[0]),
            default => throw new \JeroenGerits\Support\Exception\InvalidLongitudeException('Invalid longitude value provided')
        };
    }
}
