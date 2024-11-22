<?php

namespace App\Exceptions;

class ApiException extends \Exception{
    function __construct(string $message = "", int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}