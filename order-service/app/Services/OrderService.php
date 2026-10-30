<?php

namespace App\Services;

use App\Clients\CartClient;
use App\Clients\ProductClient;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService
{
    protected CartClient $cartClient;
    protected ProductClient $productClient;

    public function __construct(
        CartClient $cartClient,
        ProductClient $productClient
    ) {
        $this->cartClient = $cartClient;
        $this->productClient = $productClient;
    }

    public function createOrder(int $userId): Order
    {
        // 1. Ambil keranjang dari Cart Service
        $cart = $this->cartClient->getCart($userId);

        // 2. Periksa apakah keranjang kosong
        if (
            !$cart ||
            !isset($cart['items']) ||
            !is_array($cart['items']) ||
            empty($cart['items'])
        ) {
            throw new Exception('Keranjang belanja kosong', 409);
        }

        $itemsData = [];
        $totalAmount = 0;

        // 3. Periksa setiap item dalam keranjang
        foreach ($cart['items'] as $cartItem) {

            if (
                !isset($cartItem['product_id']) ||
                !isset($cartItem['quantity']) ||
                !is_numeric($cartItem['product_id']) ||
                !is_numeric($cartItem['quantity']) ||
                (int) $cartItem['quantity'] <= 0
            ) {
                throw new Exception(
                    'Data item keranjang tidak valid',
                    409
                );
            }

            $productId = (int) $cartItem['product_id'];
            $quantity = (int) $cartItem['quantity'];

            // 4. Ambil produk dari Product Service
            $product = $this->productClient->getProduct($productId);

            // 5. Periksa apakah produk masih tersedia
            if (!$product) {
                throw new Exception(
                    "Produk dengan ID {$productId} tidak ditemukan",
                    409
                );
            }

            // Pastikan data produk lengkap
            if (
                !isset($product['name']) ||
                !isset($product['price']) ||
                !isset($product['stock']) ||
                !is_numeric($product['price']) ||
                !is_numeric($product['stock'])
            ) {
                throw new Exception(
                    'Data dari Product Service tidak valid',
                    503
                );
            }

            $productName = $product['name'];
            $unitPrice = (float) $product['price'];
            $stock = (int) $product['stock'];

            // 6. Periksa stok produk
            if ($stock < $quantity) {
                throw new Exception(
                    "Stok {$productName} tidak mencukupi: diminta {$quantity}, tersedia {$stock}",
                    409
                );
            }

            // 7. Hitung harga item
            $subtotal = round($unitPrice * $quantity, 2);
            $totalAmount += $subtotal;

            $itemsData[] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        // 8. Simpan pesanan dan item dalam satu transaksi
        $order = DB::transaction(function () use (
            $userId,
            $totalAmount,
            $itemsData
        ) {
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'PENDING',
                'total_amount' => $totalAmount,
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create($item);
            }

            return $order->load('items');
        });

        // 9. Kosongkan keranjang setelah pesanan tersimpan
        // Kegagalan mengosongkan keranjang tidak membatalkan order
        try {
            $this->cartClient->clearCart($userId);
        } catch (Exception $e) {
            Log::warning('Pesanan berhasil dibuat tetapi keranjang gagal dikosongkan', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }

        return $order;
    }
}
