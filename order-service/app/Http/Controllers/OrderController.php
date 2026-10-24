<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            // 1. SKENARIO VALIDASI GAGAL (422)
            // Memeriksa apakah user_id ada dan berupa angka
            if (!$request->has('user_id') || empty($request->input('user_id'))) {
                return response()->json([
                    "message" => "Data yang dikirim tidak valid",
                    "errors" => [
                        "user_id" => [
                            "Field user id wajib diisi"
                        ]
                    ]
                ], 422);
            }

            $userId = $request->input('user_id');

            // ==========================================================
            // SIMULASI KONDISI ERROR (Untuk Pengujian di Postman/Insomnia)
            // ==========================================================

            // Simulasi Skenario: 503 Service Lain Bermasalah (Cart/Product Service mati)
            $simulasiServiceBermasalah = true; // Ubah jadi true untuk tes 503
            if ($simulasiServiceBermasalah) {
                Log::error("Order Service gagal menghubungi Product/Cart service untuk user_id: {$userId}");
                return response()->json([
                    "message" => "Product service tidak dapat dihubungi, coba lagi nanti"
                ], 503);
            }

            // Simulasi Skenario: 409 Keranjang Kosong / Produk Tidak Ada / Stok Kurang
            $simulasiStokKurang = true; // Ubah jadi true untuk tes 409
            if ($simulasiStokKurang) {
                return response()->json([
                    "message" => "Stok Monitor 24 Inch tidak mencukupi: diminta 11, tersedia 10"
                ], 409);
            }

            // Simulasi Skenario: 500 Error Tak Terduga (Misal kendala database/sistem)
            $simulasiErrorInternal = true; // Ubah jadi true untuk tes 500
            if ($simulasiErrorInternal) {
                throw new \Exception("Terjadi kesalahan tak terduga pada database internal.");
            }

            // ==========================================================
            // 2. SKENARIO BERHASIL / SUKSES (201)
            // ==========================================================
            // Jika semua pengecekan lolos, buat pesanan baru sesuai format soal
            $orderData = [
                "message" => "Pesanan berhasil dibuat",
                "data" => [
                    "id" => 1,
                    "user_id" => (int) $userId,
                    "status" => "PENDING",
                    "total_amount" => 1050000,
                    "items" => [
                        [
                            "product_id" => 1,
                            "product_name" => "Keyboard Mekanikal",
                            "unit_price" => 450000,
                            "quantity" => 2,
                            "subtotal" => 900000
                        ],
                        [
                            "product_id" => 2,
                            "product_name" => "Mouse Wireless",
                            "unit_price" => 150000,
                            "quantity" => 1,
                            "subtotal" => 150000
                        ]
                    ],
                    "created_at" => "2026-10-20T03:15:00+00:00",
                    "updated_at" => "2026-10-20T03:15:00+00:00"
                ]
            ];

            return response()->json($orderData, 201);

            

        } catch (\Exception $e) {
            // Penanganan Error Tak Terduga (500)
            Log::error("Error pada OrderController: " . $e->getMessage());
            
            return response()->json([
                "message" => "Terjadi kesalahan pada server",
                // Detail teknis hanya dimunculkan jika mode debug aktif (opsional sesuai soal)
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'order-service'
        ], 200);
    }

    public function show($id): JsonResponse
    {
        // Ketentuan soal: Kalau id di path bukan angka, kembalikan 404
        if (!is_numeric($id)) {
            return response()->json([
                "message" => "Pesanan dengan id {$id} tidak ditemukan"
            ], 404);
        }

        // Simulasi pencarian data pesanan berdasarkan ID
        // (Nanti bagian ini bisa diganti dengan Eloquent Model, contoh: Order::with('items')->find($id))
        
        // Contoh data jika ID yang dicari adalah 1 (Sukses 200)
        if ($id == 1) {
            return response()->json([
                "message" => "Berhasil mengambil pesanan",
                "data" => [
                    "id" => (int) $id,
                    "user_id" => 7,
                    "status" => "PAID",
                    "total_amount" => 1050000,
                    "items" => [
                        [
                            "product_id" => 1,
                            "product_name" => "Keyboard Mekanikal",
                            "unit_price" => 450000,
                            "quantity" => 2,
                            "subtotal" => 900000
                        ],
                        [
                            "product_id" => 2,
                            "product_name" => "Mouse Wireless",
                            "unit_price" => 150000,
                            "quantity" => 1,
                            "subtotal" => 150000
                        ]
                    ],
                    "created_at" => "2026-10-20T03:15:00+00:00",
                    "updated_at" => "2026-10-20T04:02:00+00:00"
                ]
            ], 200);
        }

        // Skenario Gagal: Pesanan tidak ditemukan (404) sesuai contoh soal
        return response()->json([
            "message" => "Pesanan dengan id {$id} tidak ditemukan"
        ], 404);
    }

    public function getByUser(Request $request, $userId): JsonResponse
    {
        // Ambil parameter query 'status' jika ada
        $status = $request->query('status');

        // Validasi parameter status jika diisi (hanya boleh PENDING atau PAID)
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

        // Simulasi data daftar pesanan berdasarkan user_id dan filter status
        // (Nanti bagian ini bisa diganti dengan query Eloquent Model, contoh: Order::with('items')->where('user_id', $userId)->when($status, fn($q) => $q->where('status', $status))->latest()->get())

        // Contoh data ilustrasi jika user_id adalah 7
        if ($userId == 7) {
            $orders = [
                [
                    "id" => 2,
                    "user_id" => (int) $userId,
                    "status" => "PENDING",
                    "total_amount" => 150000,
                    "items" => [
                        [
                            "product_id" => 2,
                            "product_name" => "Mouse Wireless",
                            "unit_price" => 150000,
                            "quantity" => 1,
                            "subtotal" => 150000
                        ]
                    ],
                    "created_at" => "2026-10-20T05:10:00+00:00",
                    "updated_at" => "2026-10-20T05:10:00+00:00"
                ]
            ];

            // Filter manual untuk simulasi jika parameter status disertakan
            if ($status) {
                $orders = array_values(array_filter($orders, fn($order) => $order['status'] === $status));
            }

            return response()->json([
                "message" => "Berhasil mengambil daftar pesanan",
                "data" => $orders
            ], 200);
        }

        // Jika user tidak punya pesanan atau tidak ditemukan, kembalikan data kosong
        return response()->json([
            "message" => "Berhasil mengambil daftar pesanan",
            "data" => []
        ], 200);
    }

    public function pay($id): JsonResponse
    {
        // Ketentuan soal: Kalau id di path bukan angka, kembalikan 404
        if (!is_numeric($id)) {
            return response()->json([
                "message" => "Pesanan dengan id {$id} tidak ditemukan"
            ], 404);
        }

        // Simulasi pencarian data pesanan berdasarkan ID
        // (Nanti bagian ini bisa diganti dengan Eloquent Model, contoh: Order::with('items')->find($id))

        // Skenario Gagal: Pesanan tidak ditemukan (404)
        if ($id == 99) {
            return response()->json([
                "message" => "Pesanan dengan id 99 tidak ditemukan"
            ], 404);
        }

        // Simulasi status pesanan saat ini
        // Misalkan id 2 berstatus PENDING, dan id 1 sudah berstatus PAID
        $currentStatus = ($id == 1) ? "PAID" : "PENDING";

        // Skenario Gagal: Status bukan PENDING (409)
        if ($currentStatus !== "PENDING") {
            return response()->json([
                "message" => "Pesanan berstatus PAID, hanya pesanan PENDING yang dapat dibayar"
            ], 409);
        }

        // Skenario Berhasil / Sukses (200): Mengubah status PENDING menjadi PAID
        return response()->json([
            "message" => "Pesanan berhasil dibayar",
            "data" => [
                "id" => (int) $id,
                "user_id" => 7,
                "status" => "PAID",
                "total_amount" => 150000,
                "items" => [
                    [
                        "product_id" => 2,
                        "product_name" => "Mouse Wireless",
                        "unit_price" => 150000,
                        "quantity" => 1,
                        "subtotal" => 150000
                    ]
                ],
                "created_at" => "2026-10-20T05:10:00+00:00",
                "updated_at" => "2026-10-20T05:12:00+00:00"
            ]
        ], 200);
    }
}