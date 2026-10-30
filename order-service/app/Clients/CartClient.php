<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

class CartClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.cart.url'),
            '/'
        );
    }

    public function getCart(int $userId): ?array
    {
        if (empty($this->baseUrl)) {
            throw new RuntimeException(
                'CART_SERVICE_URL belum dikonfigurasi',
                503
            );
        }

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->get("{$this->baseUrl}/carts/{$userId}");

        } catch (ConnectionException $e) {
            Log::error('Cart Service tidak dapat dihubungi', [
                'url' => $this->baseUrl,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException(
                'Koneksi ke Cart Service gagal',
                503,
                $e
            );
        }

        // Periksa status HTTP dari Cart Service
        if (!$response->successful()) {
            Log::error('Cart Service mengembalikan HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'user_id' => $userId,
            ]);

            throw new RuntimeException(
                "Cart Service mengembalikan HTTP {$response->status()}",
                503
            );
        }

        // Periksa format JSON
        $result = $response->json();

        if (
            !is_array($result) ||
            !isset($result['data']) ||
            !is_array($result['data'])
        ) {
            Log::error('Format respons Cart Service tidak valid', [
                'response' => $result,
                'user_id' => $userId,
            ]);

            throw new RuntimeException(
                'Format respons Cart Service tidak valid',
                503
            );
        }

        return $result['data'];
    }

    public function clearCart(int $userId): void
    {
        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->delete("{$this->baseUrl}/carts/{$userId}");

            if (!$response->successful()) {
                Log::warning('Gagal mengosongkan keranjang', [
                    'user_id' => $userId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

        } catch (ConnectionException $e) {
            Log::warning('Cart Service tidak dapat dihubungi saat mengosongkan keranjang', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
