<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Support\Facades\Http;

class StocksInfoService
{
    const BASE_URL = "https://finnhub.io/api/v1";

    public function symbolLookup(string $query)
    {
        $result = $this->get('/search?q='.$query);
        if($result['count'] > 0) {
            $data = array_map(function ($stock) {
                $stock['display_symbol'] = $stock['displaySymbol'];
                unset($stock['displaySymbol']);
                return $stock;
            }, $result['result']);
            Stock::upsert($data, uniqueBy: ['symbol'], update: ['description', 'display_symbol', 'type']);
        }
        return $result;
    }

    protected function get(string $query)
    {
        $response = Http::withHeaders([
            'X-Finnhub-Token' => config('services.stocks.finnhub.key'),
        ])->get(self::BASE_URL . $query);

        return $response->json();
    }
}
