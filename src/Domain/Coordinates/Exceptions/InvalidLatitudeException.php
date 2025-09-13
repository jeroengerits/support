<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Domain\Coordinates\Exceptions;

/**
 * Exception thrown when invalid latitude values are provided.
 *
 * This exception is thrown when latitude values are outside
 * the valid range of -90 to 90 degrees.
 */
class InvalidLatitudeException extends \Exception {}
