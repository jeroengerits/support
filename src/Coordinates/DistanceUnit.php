<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates;

/**
 * Distance unit enumeration for coordinate calculations.
 */
enum DistanceUnit: string
{
    /** Kilometers unit for distance calculations. */
    case KILOMETERS = 'km';

    /** Miles unit for distance calculations. */
    case MILES = 'mi';
}
