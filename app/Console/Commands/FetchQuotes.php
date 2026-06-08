<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Services\StocksInfoService;
use Illuminate\Console\Command;

class FetchQuotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-quotes
                            {--all : Fetch quotes for every stock, not only owned ones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the latest Finnhub quotes for stocks held in portfolios';

    /**
     * Execute the console command.
     */
    public function handle(StocksInfoService $service): int
    {
        $query = Stock::query();

        if (! $this->option('all')) {
            $query->whereHas('transactions');
        }

        $stocks = $query->get();

        if ($stocks->isEmpty()) {
            $this->info('No stocks to update.');

            return self::SUCCESS;
        }

        $this->info("Fetching quotes for {$stocks->count()} stock(s)...");

        $updated = $service->quoteMany($stocks);

        $this->info("Stored {$updated} new quote(s).");

        return self::SUCCESS;
    }
}
