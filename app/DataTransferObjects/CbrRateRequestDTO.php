<?php

namespace App\DataTransferObjects;

use App\Http\Requests\GetRatesRequest;

class CbrRateRequestDTO
{
    public function __construct(
        public readonly string $currency,
        public readonly string $baseCurrency,
        public readonly string $date,
        public readonly string $previousDate
    )
    {
    }

    public static function fromRequest(GetRatesRequest $request): CbrRateRequestDTO
    {
        return new self(
            $request->get('currency'),
            $request->get('base_currency', env('CBR_BASE_CURRENCY')),
            $request->get('date', now()->format('d/m/Y')),
            $request->get('previous_date', now()->subDay()->format('d/m/Y')),
        );
    }
}
