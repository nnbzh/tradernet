<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CbrRepository
{
    public function __construct(private string $url = '')
    {
        $this->url = env('CBR_RATES_URL');
    }

    public function getRates(string $date): array|string
    {
        $query = [
            'date_req' => $date
        ];

        return Cache::remember("cbr-rates/$date", 84600, fn() => Http::get($this->url, $query)->body());

    }
}
