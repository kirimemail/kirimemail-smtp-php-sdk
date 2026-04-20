<?php

if (!function_exists('kirimemail_client')) {
    function kirimemail_client(): \KirimEmail\Smtp\Client\SmtpClient
    {
        return app(\KirimEmail\Smtp\Client\SmtpClient::class);
    }
}

if (!function_exists('kirimemail_messages')) {
    function kirimemail_messages(): \KirimEmail\Smtp\Api\MessagesApi
    {
        return app(\KirimEmail\Smtp\Api\MessagesApi::class);
    }
}