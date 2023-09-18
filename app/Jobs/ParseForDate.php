<?php

namespace App\Jobs;

use App\DataTransferObjects\CbrRateRequestDTO;
use App\Models\Rate;
use App\Services\CbrService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseForDate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $date,
        private string $base,
        private string $currency
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CbrService $service): void
    {
        try {
            $result = $service->getDifferenceBetweenRates(
                new CbrRateRequestDTO(
                    $this->currency,
                    $this->base,
                    $this->date,
                    Carbon::createFromFormat('d/m/Y', $this->date)->subDay()->format('d/m/Y')
                )
            );

            logger("$this->base - $this->currency for date $this->date is: {$result['today']}");
            Rate::query()->upsert([
                'currency' => $this->currency,
                'base'     => $this->base,
                'date'     => Carbon::createFromFormat('d/m/Y', $this->date)->toDateString(),
                'rate'     => $result['today'],
            ], ['currency', 'base', 'date'], ['rate']);
        } catch (Exception $exception) {
            logger("Error while getting historical data of rates. {$exception->getMessage()}", [
                'message' => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
                'data'    => "$this->currency - $this->base ($this->date)"
            ]);
        }
    }
}
