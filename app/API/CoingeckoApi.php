<?php

namespace App\API;

use Illuminate\Support\Facades\Http;

class CoingeckoApi
{
    protected $baseURL;

    public function __construct()
    {
        $this->baseURL = env('SERVICES_COINGECKO_API_URL');
    }

    public function getCoins(string $id)
    {
        return Http::get("{$this->baseURL}/coins/{$id}")->json();
    }

    public function getTokenPrice(string $id, string $currency)
    {
        return Http::get("{$this->baseURL}/simple/price?ids={$id}&vs_currencies={$currency}")->json();
    }
}
