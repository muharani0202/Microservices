<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.product.url');
    }

    public function getProduct($productId)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/products/{$productId}");

            if ($response->successful()) {
                return $response->json()['data'] ?? null;
            }

            if ($response->status() === 404) {
                return null;
            }

            throw new Exception("Product service error status: " . $response->status());
        } catch (Exception $e) {
            Log::error("ProductClient Error (getProduct): " . $e->getMessage());
            throw new Exception("Product service tidak dapat dihubungi, coba lagi nanti", 503);
        }
    }
}