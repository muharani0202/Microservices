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

    public function __construct(CartClient $cartClient, ProductClient $productClient)
    {
        $this->cartClient = $cartClient;
        $this->productClient = $productClient;
    }

    public function createOrder(int $userId): Order
    {
        $cart = $this->cartClient->getCart($userId);

        if (!$cart || empty($cart['items'])) {
            throw new Exception("Keranjang belanja kosong", 409);
        }

        $itemsData = [];
        $totalAmount = 0;

        foreach ($cart['items'] as $cartItem) {
            $productId = $cartItem['product_id'];
            $quantity = $cartItem['quantity'];

            $product = $this->productClient->getProduct($productId);

            if (!$product) {
                throw new Exception("Produk dengan ID {$productId} tidak ditemukan", 409);
            }

            $productName = $product['name'];
            $unitPrice = (float) $product['price'];
            $stock = (int) $product['stock'];

            if ($stock < $quantity) {
                throw new Exception("Stok {$productName} tidak mencukupi: diminta {$quantity}, tersedia {$stock}", 409);
            }

            $subtotal = $unitPrice * $quantity;
            $totalAmount += $subtotal;

            $itemsData[] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        $order = DB::transaction(function () use ($userId, $totalAmount, $itemsData) {
            $order = Order::create([
                'user_id' => $userId,
                'status' => 'PENDING',
                'total_amount' => $totalAmount,
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        try {
            $this->cartClient->clearCart($userId);
        } catch (Exception $e) {
            Log::warning("Pesanan berhasil dibuat (ID: {$order->id}), tetapi gagal mengosongkan keranjang untuk user: {$userId}");
        }

        return $order->load('items');
    }

    public function getOrderById(int $id): ?Order
    {
        return Order::with('items')->find($id);
    }

    public function getOrdersByUser(int $userId, ?string $status = null)
    {
        return Order::with('items')
            ->where('user_id', $userId)
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->get();
    }

    public function payOrder(int $id): Order
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            throw new Exception("Pesanan dengan id {$id} tidak ditemukan", 404);
        }

        if ($order->status !== 'PENDING') {
            throw new Exception("Pesanan berstatus {$order->status}, hanya pesanan PENDING yang dapat dibayar", 409);
        }

        $order->update(['status' => 'PAID']);

        return $order->fresh('items');
    }
}