<?php


namespace App\Exceptions;

class ApiAuthException extends ApiException
{
    public function __construct(string $message = "Authentication exception",int $code)
    {
        parent::__construct($message, $code);
    }
}
