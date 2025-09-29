<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Exception;

class AuthenticationException extends ApiException
{
    public function __construct(string $message = 'Authentication failed', ?Exception $previous = null)
    {
        parent::__construct($message, 401, null, $previous);
    }
}