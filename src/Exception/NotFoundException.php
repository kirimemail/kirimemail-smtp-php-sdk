<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Exception;

class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Resource not found', ?Exception $previous = null)
    {
        parent::__construct($message, 404, null, $previous);
    }
}