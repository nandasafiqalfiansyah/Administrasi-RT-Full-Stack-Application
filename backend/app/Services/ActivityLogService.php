<?php

namespace App\Services;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ActivityLogService
{
    public function __construct(
        private ActivityLogRepositoryInterface $activityLogRepository
    ) {}

    public function getAllActivityLogs(array $filters = []): Collection
    {
        return $this->activityLogRepository->getAll($filters);
    }

    public function getRecentActivity(int $limit = 10): Collection
    {
        return $this->activityLogRepository->getRecent($limit);
    }

    public function getUserActivity(int $userId): Collection
    {
        return $this->activityLogRepository->getByUser($userId);
    }

    public function getModelActivity(string $modelType, int $modelId): Collection
    {
        return $this->activityLogRepository->getByModel($modelType, $modelId);
    }

    public function getActivityByAction(string $action): Collection
    {
        return $this->activityLogRepository->getByAction($action);
    }

    public function getActivityByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->activityLogRepository->getByDateRange($startDate, $endDate);
    }

    public function logActivity(array $data)
    {
        return $this->activityLogRepository->create($data);
    }

    public function getPaginatedActivityLogs(int $perPage = 15)
    {
        return $this->activityLogRepository->paginate($perPage);
    }

    public function getActivityStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->subWeek();
        $thisMonth = now()->subMonth();

        return [
            'total' => $this->activityLogRepository->getAll()->count(),
            'today' => $this->activityLogRepository->getByDateRange($today->toDateTimeString(), now()->toDateTimeString())->count(),
            'this_week' => $this->activityLogRepository->getByDateRange($thisWeek->toDateTimeString(), now()->toDateTimeString())->count(),
            'this_month' => $this->activityLogRepository->getByDateRange($thisMonth->toDateTimeString(), now()->toDateTimeString())->count(),
            'by_action' => [
                'create' => $this->activityLogRepository->getByAction('create')->count(),
                'update' => $this->activityLogRepository->getByAction('update')->count(),
                'delete' => $this->activityLogRepository->getByAction('delete')->count(),
            ],
        ];
    }
}