<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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

    public function quote(Stock $stock)
    {
        $response = $this->get("/quote?symbol={$stock->symbol}");
        $quote = Quote::create([
            'stock_id' => $stock->id,
            'price' => $response['c'],
            'change' => $response['d'],
            'percent_change' => $response['dp'],
            'daily_high' => $response['h'],
            'daily_low' => $response['l'],
            'open' => $response['pc'],
            'timestamp' => Carbon::createFromTimeStamp($response['t'])
        ]);
        dd($quote);
        return $response;
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
