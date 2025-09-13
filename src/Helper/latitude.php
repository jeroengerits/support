<?php

if (! function_exists('latitude')) {
    /**
     * Create a Latitude instance from various input types.
     *
     * @param float|string|array $value The latitude value
     *
     * @throws \JeroenGerits\Support\Exception\InvalidLatitudeException
     */
    function latitude(float|string|array $value): \JeroenGerits\Support\ValueObject\Latitude
    {
        return match (true) {
            is_float($value) => new \JeroenGerits\Support\ValueObject\Latitude($value),
            is_string($value) => \JeroenGerits\Support\ValueObject\Latitude::fromString($value),
            is_array($value) && isset($value['latitude']) => new \JeroenGerits\Support\ValueObject\Latitude($value['latitude']),
            is_array($value) && isset($value[0]) => new \JeroenGerits\Support\ValueObject\Latitude($value[0]),
            default => throw new \JeroenGerits\Support\Exception\InvalidLatitudeException('Invalid latitude value provided')
        };
    }
}
