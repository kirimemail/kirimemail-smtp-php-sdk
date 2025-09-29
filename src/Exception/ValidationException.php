<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Exception;

class ValidationException extends ApiException
{
    public function __construct(string $message = 'Validation failed', ?array $errors = null, ?Exception $previous = null)
    {
        parent::__construct($message, 422, $errors, $previous);
    }
}