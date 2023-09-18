<?php

namespace App\Console\Commands;

use App\Jobs\ParseForDate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ParseRatesForLastNDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:parse {currency} {base=RUR} {days=180}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date     = now();
        $base     = $this->argument('base');
        $currency = $this->argument('currency');
        $days     = $this->argument('days');

        $jobs = [];
        for ($i = 0; $i < $days; $i++) {
            $jobs[] = new ParseForDate(
                $date->subDay()->format('d/m/Y'),
                $base,
                $currency
            );
        }

        Bus::chain($jobs)->dispatch();
    }
}
