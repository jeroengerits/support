<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\Exceptions;

/**
 * Exception thrown when invalid coordinates are provided.
 *
 * This exception is thrown when coordinate values are outside
 * valid ranges or in invalid formats.
 */
class InvalidCoordinatesException extends \Exception {}
