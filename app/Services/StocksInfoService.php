<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class StocksInfoService
{
    const BASE_URL = "https://finnhub.io/api/v1";

    public function symbolLookup(string $query)
    {
        $response = $this->get('/search?q='.$query);
        if($response['count'] > 0) {
            $data = array_map(function ($stock) {
                $stock['display_symbol'] = $stock['displaySymbol'];
                unset($stock['displaySymbol']);
                return $stock;
            }, $response['result']);
            Stock::upsert($data, uniqueBy: ['symbol'], update: ['description', 'display_symbol', 'type']);
        }
        return $response;
    }

    public function quote(Stock $stock): ?Quote
    {
        try {
            $response = $this->get("/quote?symbol={$stock->symbol}");

            if (! is_array($response) || ! empty($response['error'])) {
                return null;
            }

            // Finnhub returns all-zero values for unknown symbols.
            if (($response['c'] ?? 0) == 0 && ($response['pc'] ?? 0) == 0) {
                return null;
            }

            return Quote::create([
                'stock_id' => $stock->id,
                'price' => $response['c'] ?? null,
                'change' => $response['d'] ?? null,
                'percent_change' => $response['dp'] ?? null,
                'daily_high' => $response['h'] ?? null,
                'daily_low' => $response['l'] ?? null,
                'open' => $response['o'] ?? null,
                'previous_close' => $response['pc'] ?? null,
                'timestamp' => empty($response['t']) ? now() : Carbon::createFromTimestamp($response['t']),
            ]);
        } catch (Throwable $e) {
            Log::warning("Failed to fetch quote for {$stock->symbol}: {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Fetch and store a fresh quote for every given stock.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Stock>  $stocks
     * @return int Number of quotes successfully stored.
     */
    public function quoteMany(Collection $stocks): int
    {
        return $stocks->reduce(
            fn (int $count, Stock $stock) => $this->quote($stock) ? $count + 1 : $count,
            0
        );
    }

    public function exchangeLookup(string $exchange)
    {
        $response = $this->get('/stock/symbol?exchange='.$exchange);
        return $response;
    }

    protected function get(string $query)
    {
        $response = Http::withHeaders([
            'X-Finnhub-Token' => config('services.stocks.finnhub.key'),
        ])->get(self::BASE_URL . $query);

        return $response->json();
    }
}
