<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StocksInfoService
{
    const BASE_URL = "https://finnhub.io/api/v1";

    public function symbolLookup(string $query)
    {
        return $this->get('/search?q='.$query);
    }

    protected function get(string $query)
    {
        $response = Http::withHeaders([
            'X-Finnhub-Token' => config('services.stocks.finnhub.key'),
        ])->get(self::BASE_URL . $query);

        return $response->json();
    }
}
