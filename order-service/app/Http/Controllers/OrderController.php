<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'order-service'
        ], 200);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $userId = $request->input('user_id');
            $order = $this->orderService->createOrder($userId);

            return response()->json([
                "message" => "Pesanan berhasil dibuat",
                "data" => $order
            ], 201);

        } catch (Exception $e) {
            $statusCode = in_array($e->getCode(), [409, 422, 503]) ? $e->getCode() : 500;
            
            if ($statusCode === 500) {
                Log::error("Error pada OrderController (store): " . $e->getMessage());
            }

            return response()->json([
                "message" => $e->getMessage() ?: "Terjadi kesalahan pada server",
                "error" => config('app.debug') && $statusCode === 500 ? $e->getMessage() : null
            ], $statusCode);
        }
    }

    public function show($id): JsonResponse
    {
        if (!is_numeric($id)) {
            return response()->json([
                "message" => "Pesanan dengan id {$id} tidak ditemukan"
            ], 404);
        }

        $order = $this->orderService->getOrderById((int) $id);

        if (!$order) {
            return response()->json([
                "message" => "Pesanan dengan id {$id} tidak ditemukan"
            ], 404);
        }

        return response()->json([
            "message" => "Berhasil mengambil pesanan",
            "data" => $order
        ], 200);
    }

    public function getByUser(Request $request, $userId): JsonResponse
    {
        if (!is_numeric($userId)) {
            return response()->json([
                "message" => "Data yang dikirim tidak valid",
                "errors" => ["user_id" => ["User ID harus berupa angka"]]
            ], 422);
        }

        $status = $request->query('status');

        if ($status !== null && !in_array($status, ['PENDING', 'PAID'])) {
            return response()->json([
                "message" => "Data yang dikirim tidak valid",
                "errors" => [
                    "status" => [
                        "Status harus PENDING atau PAID"
                    ]
                ]
            ], 422);
        }

        $orders = $this->orderService->getOrdersByUser((int) $userId, $status);

        return response()->json([
            "message" => "Berhasil mengambil daftar pesanan",
            "data" => $orders
        ], 200);
    }

    public function pay($id): JsonResponse
    {
        if (!is_numeric($id)) {
            return response()->json([
                "message" => "Pesanan dengan id {$id} tidak ditemukan"
            ], 404);
        }

        try {
            $order = $this->orderService->payOrder((int) $id);

            return response()->json([
                "message" => "Pesanan berhasil dibayar",
                "data" => $order
            ], 200);

        } catch (Exception $e) {
            $statusCode = in_array($e->getCode(), [404, 409]) ? $e->getCode() : 500;

            return response()->json([
                "message" => $e->getMessage(),
            ], $statusCode);
        }
    }
}