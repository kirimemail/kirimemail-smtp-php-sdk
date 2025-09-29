<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Exception;

use Exception;

class ApiException extends Exception
{
    protected ?array $errors;

    public function __construct(string $message, int $code = 0, ?array $errors = null, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getFirstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }

        foreach ($this->errors as $fieldErrors) {
            if (is_array($fieldErrors) && !empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }

        return null;
    }

    public function getFieldErrors(string $field): ?array
    {
        return $this->errors[$field] ?? null;
    }

    public function hasFieldError(string $field): bool
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }
}