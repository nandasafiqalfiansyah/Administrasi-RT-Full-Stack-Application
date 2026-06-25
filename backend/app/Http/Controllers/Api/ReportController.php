<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function summary(Request $request): JsonResponse
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $summary = $this->reportService->getMonthlySummary((int) $bulan, (int) $tahun);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function chart(Request $request): JsonResponse
    {
        $tahun = $request->input('tahun', now()->year);

        $chart = $this->reportService->getYearlyChart((int) $tahun);

        return response()->json([
            'success' => true,
            'data' => $chart,
        ]);
    }

    public function detail(Request $request): JsonResponse
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $report = $this->reportService->getDetailReport((int) $bulan, (int) $tahun);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }
}