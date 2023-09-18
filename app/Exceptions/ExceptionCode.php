<?php

namespace App\Exceptions;

enum ExceptionCode: int
{
    case CBR_ERROR = 10_000;
    case CBR_NO_CURRENCY = 10_001;

    public function getMessage(): string
    {
        $key         = "exceptions.$this->value.message";
        $translation = __($key);

        if ($key == $translation) {
            return __('exceptions.default.message');
        }

        return $translation;
    }

    public function getHttpCode(): int
    {
        $value = $this->value;

        return match (true) {
            $value >= 10_000 => 503,
            default => 500
        };
    }
}
