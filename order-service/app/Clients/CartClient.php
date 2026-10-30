<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CartClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.cart.url');
    }

    public function getCart($userId)
    {
        try {
            // Tambahkan ->throw() atau cetak URL untuk debugging jika perlu
            $response = Http::timeout(5)->get("{$this->baseUrl}/carts/{$userId}");

            if ($response->successful()) {
                return $response->json()['data'] ?? null;
            }

            if ($response->status() === 404) {
                return null;
            }

            throw new Exception("Cart service error status: " . $response->status());
        } catch (Exception $e) {
            Log::error("CartClient Error (getCart): " . $e->getMessage());
            throw new Exception("Cart service tidak dapat dihubungi, coba lagi nanti", 503);
        }
    }

    public function clearCart($userId)
    {
        try {
            Http::timeout(5)->delete("{$this->baseUrl}/carts/{$userId}");
        } catch (Exception $e) {
            Log::warning("Gagal mengosongkan keranjang: " . $e->getMessage());
        }
    }
}