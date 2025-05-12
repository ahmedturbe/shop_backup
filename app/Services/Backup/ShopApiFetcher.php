<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\Http;

class ShopApiFetcher
{
    public function fetch(): array
    {
        $response = Http::get(config('services.shop_api.url') . '/api/products');

        if ($response->failed()) {
            throw new \Exception('Failed to fetch products from API');
        }

        return $response->json();
    }
}
