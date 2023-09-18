<?php

namespace App\Exceptions;

use Exception;

class InternalException extends Exception
{
    public static function new(
        ExceptionCode $code,
        ?string       $message = null,
        ?int          $statusCode = null
    ): static
    {
        return new static(
            $message ?? $code->getMessage(),
            $statusCode ?? $code->getHttpCode(),
        );
    }
}
