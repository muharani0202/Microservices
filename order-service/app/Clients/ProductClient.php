<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

class ProductClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.product.url'),
            '/'
        );
    }

    public function getProduct(int $productId): ?array
    {
        // 1. Periksa konfigurasi URL
        if (empty($this->baseUrl)) {
            throw new RuntimeException(
                'PRODUCT_SERVICE_URL belum dikonfigurasi',
                503
            );
        }

        try {
            // 2. Mengambil data produk dari Product Service
            $response = Http::timeout(5)
                ->acceptJson()
                ->get("{$this->baseUrl}/products/{$productId}");

        } catch (ConnectionException $e) {
            Log::error('Product Service tidak dapat dihubungi', [
                'url' => $this->baseUrl,
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException(
                'Koneksi ke Product Service gagal',
                503,
                $e
            );
        }

        // 3. Jika produk tidak ditemukan
        if ($response->status() === 404) {
            return null;
        }

        // 4. Periksa status HTTP
        if (!$response->successful()) {
            Log::error('Product Service mengembalikan HTTP error', [
                'status' => $response->status(),
                'product_id' => $productId,
                'body' => $response->body(),
            ]);

            throw new RuntimeException(
                "Product Service mengembalikan HTTP {$response->status()}",
                503
            );
        }

        // 5. Periksa format JSON
        $result = $response->json();

        if (
            !is_array($result) ||
            !isset($result['data']) ||
            !is_array($result['data'])
        ) {
            Log::error('Format respons Product Service tidak valid', [
                'product_id' => $productId,
                'response' => $result,
            ]);

            throw new RuntimeException(
                'Format respons Product Service tidak valid',
                503
            );
        }

        // 6. Mengembalikan data produk
        return $result['data'];
    }
}
