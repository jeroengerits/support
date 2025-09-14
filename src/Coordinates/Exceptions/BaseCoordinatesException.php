<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Coordinates\Exceptions;

use Exception;

/**
 * Base exception class for coordinate-related errors.
 *
 * This class provides common functionality for all coordinate exceptions,
 * including standardized error codes and context information.
 */
abstract class BaseCoordinatesException extends Exception
{
    /**
     * Error codes for different types of coordinate exceptions.
     */
    public const CODE_INVALID_VALUE = 1001;

    public const CODE_OUT_OF_RANGE = 1002;

    public const CODE_INVALID_TYPE = 1003;

    public const CODE_MISSING_VALUE = 1004;

    public const CODE_INVALID_FORMAT = 1005;

    /**
     * The problematic value that caused the exception.
     */
    protected mixed $problematicValue = null;

    /**
     * Additional context about the error.
     */
    protected array $context = [];

    /**
     * Create a new BaseCoordinatesException instance.
     *
     * @param string         $message          The exception message
     * @param int            $code             The exception code
     * @param mixed          $problematicValue The value that caused the exception
     * @param array          $context          Additional context about the error
     * @param Exception|null $previous         The previous exception
     */
    public function __construct(
        string $message = 'Invalid coordinate value provided',
        int $code = self::CODE_INVALID_VALUE,
        mixed $problematicValue = null,
        array $context = [],
        ?Exception $previous = null
    ) {
        $this->problematicValue = $problematicValue;
        $this->context = $context;

        // Enhance message with context if available
        $enhancedMessage = $this->enhanceMessage($message);

        parent::__construct($enhancedMessage, $code, $previous);
    }

    /**
     * Enhance the message with context information.
     */
    protected function enhanceMessage(string $message): string
    {
        $enhancedMessage = $message;

        if ($this->problematicValue !== null) {
            $valueString = $this->formatValue($this->problematicValue);
            $enhancedMessage .= " (Value: {$valueString})";
        }

        if ($this->context !== []) {
            $contextStrings = [];
            foreach ($this->context as $key => $value) {
                $contextStrings[] = "{$key}: {$this->formatValue($value)}";
            }
            $enhancedMessage .= ' [Context: '.implode(', ', $contextStrings).']';
        }

        return $enhancedMessage;
    }

    /**
     * Format a value for display in error messages.
     */
    protected function formatValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_string($value)) {
            return "'{$value}'";
        }

        if (is_array($value)) {
            return 'array('.count($value).')';
        }

        if (is_object($value)) {
            return get_class($value).' object';
        }

        return (string) $value;
    }

    /**
     * Get the problematic value that caused the exception.
     */
    public function getProblematicValue(): mixed
    {
        return $this->problematicValue;
    }

    /**
     * Get additional context about the error.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get a specific context value.
     */
    public function getContextValue(string $key, mixed $default = null): mixed
    {
        return $this->context[$key] ?? $default;
    }
}
