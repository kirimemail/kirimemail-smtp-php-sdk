<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Exception;

class ServerException extends ApiException
{
    public function __construct(string $message = 'Server error', ?Exception $previous = null)
    {
        parent::__construct($message, 500, null, $previous);
    }
}