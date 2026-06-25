<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index(): JsonResponse
    {
        $data = $this->dashboardService->getOverview();

        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil dimuat',
            'data' => $data,
        ]);
    }
}