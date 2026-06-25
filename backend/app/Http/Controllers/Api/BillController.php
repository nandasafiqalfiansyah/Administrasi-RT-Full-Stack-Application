<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(
        private BillService $billService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $bills = $this->billService->getBills($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Daftar tagihan berhasil dimuat',
            'data' => $bills->items(),
            'meta' => [
                'current_page' => $bills->currentPage(),
                'last_page' => $bills->lastPage(),
                'per_page' => $bills->perPage(),
                'total' => $bills->total(),
            ],
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $count = $this->billService->generateMonthlyBills($bulan, $tahun);

        return response()->json([
            'success' => true,
            'message' => "Berhasil generate {$count} tagihan untuk bulan {$bulan}/{$tahun}",
            'data' => [
                'count' => $count,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $summary = $this->billService->getBillsSummary($bulan, $tahun);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
