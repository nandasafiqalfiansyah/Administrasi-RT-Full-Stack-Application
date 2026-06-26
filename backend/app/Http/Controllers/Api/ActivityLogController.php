<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['user_id', 'action', 'model_type', 'start_date', 'end_date']);
        $perPage = $request->get('per_page', 15);

        $data = $this->activityLogService->getPaginatedActivityLogs($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas berhasil dimuat',
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    public function recent(): JsonResponse
    {
        $limit = request()->get('limit', 10);
        $data = $this->activityLogService->getRecentActivity($limit);

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas terbaru berhasil dimuat',
            'data' => $data,
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = $this->activityLogService->getActivityStats();

        return response()->json([
            'success' => true,
            'message' => 'Statistik log aktivitas berhasil dimuat',
            'data' => $stats,
        ]);
    }

    public function byUser(int $userId): JsonResponse
    {
        $data = $this->activityLogService->getUserActivity($userId);

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas pengguna berhasil dimuat',
            'data' => $data,
        ]);
    }

    public function byModel(Request $request): JsonResponse
    {
        $modelType = $request->get('model_type');
        $modelId = $request->get('model_id');

        if (!$modelType || !$modelId) {
            return response()->json([
                'success' => false,
                'message' => 'Model type dan model ID harus diisi',
            ], 422);
        }

        $data = $this->activityLogService->getModelActivity($modelType, $modelId);

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas model berhasil dimuat',
            'data' => $data,
        ]);
    }

    public function byAction(string $action): JsonResponse
    {
        $data = $this->activityLogService->getActivityByAction($action);

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas berhasil dimuat',
            'data' => $data,
        ]);
    }

    public function byDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $data = $this->activityLogService->getActivityByDateRange($startDate, $endDate);

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas berhasil dimuat',
            'data' => $data,
        ]);
    }
}