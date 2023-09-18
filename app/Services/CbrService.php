<?php

namespace App\Services;

use App\DataTransferObjects\CbrRateRequestDTO;
use App\Exceptions\ExceptionCode;
use App\Exceptions\InternalException;
use App\Helpers\XMLHelper;
use App\Repositories\CbrRepository;

class CbrService
{
    public function __construct(private readonly CbrRepository $repository)
    {
    }

    public function getDifferenceBetweenRates(CbrRateRequestDTO $dto): array
    {
        $rates = $this->getRates($dto->date, $dto->previousDate);

        if (! $this->isAvailable($rates, $dto->baseCurrency, $dto->currency)) {
            throw InternalException::new(ExceptionCode::CBR_NO_CURRENCY);
        }

        if ($dto->currency == env('CBR_BASE_CURRENCY')) {
            if ($dto->baseCurrency == env('CBR_BASE_CURRENCY')) {
                return $this->respondWithDiff(1, 1);
            }

            if ($dto->baseCurrency != env('CBR_BASE_CURRENCY')) {
                $today = 1 / ($rates[$dto->baseCurrency]['Nominal'] / $rates[$dto->baseCurrency]['Value']);
                $prev  = 1 / ($rates[$dto->baseCurrency]['Nominal'] / $rates[$dto->baseCurrency]['Previous']);

                return $this->respondWithDiff($today, $prev);
            }
        }

        if ($dto->baseCurrency == env('CBR_BASE_CURRENCY')) {
            return $this->calculateForRUR($rates[$dto->currency]);
        }

        return $this->calculateForeign($rates[$dto->baseCurrency], $rates[$dto->currency]);
    }

    private function isAvailable(array $rates, string $base, string $currency): bool
    {
        if ($base != env('CBR_BASE_CURRENCY') && empty($rates[$base])) {
            return false;
        }

        if ($currency != env('CBR_BASE_CURRENCY') && empty($rates[$currency])) {
            return false;
        }

        return true;
    }

    private function calculateForRUR(array $rate): array
    {
        $today = $rate['Nominal'] / $rate['Value'];
        $prev  = $rate['Nominal'] / $rate['Previous'];

        return $this->respondWithDiff($today, $prev);

    }

    private function calculateForeign(array $base, array $currency): array
    {
        $today = ($currency['Nominal'] / $currency['Value']) / ($base['Nominal'] / $base['Value']);
        $prev  = ($currency['Nominal'] / $currency['Previous']) / ($base['Nominal'] / $base['Previous']);

        return $this->respondWithDiff($today, $prev);
    }

    private function respondWithDiff(float|int $today, float|int $prev): array
    {
        return [
            'today'    => round($today, 8),
            'previous' => round($prev, 8),
            'diff'     => number_format($today - $prev, 8, '.', '')
        ];
    }

    private function getRates(string $date, string $previousDate): array
    {
        $today = $this->repository->getRates($date);
        $prev  = $this->repository->getRates($previousDate);

        if (is_string($today)) {
            $today = XMLHelper::parse($today);
        }

        if (is_string($prev)) {
            $prev = XMLHelper::parse($prev);
        }

        if (empty($ratesForToday = $prev['Valute'] ?? null) || empty($ratesPrevious = $today['Valute'] ?? null)) {
            throw InternalException::new(ExceptionCode::CBR_ERROR);
        }

        return $this->mapByDays($ratesForToday, $ratesPrevious);
    }

    private function mapByDays(array $ratesForToday, array $ratesPrevious): array
    {
        $ratesForToday = collect($ratesForToday);
        $ratesPrevious = collect($ratesPrevious)->keyBy('CharCode');

        return $ratesForToday->map(function ($item) use ($ratesPrevious) {
            $item['Value']    = str_replace(',', '.', $item['Value']);
            $item['Previous'] = str_replace(',', '.', $ratesPrevious[$item['CharCode']]['Value'] ?? 1);

            return $item;
        })->keyBy('CharCode')->toArray();
    }


}
