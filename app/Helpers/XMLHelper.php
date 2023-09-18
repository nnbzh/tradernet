<?php

namespace App\Helpers;

class XMLHelper
{
    public static function parse(string $xml): ?array
    {
        return json_decode(json_encode(simplexml_load_string($xml)), true);
    }
}
