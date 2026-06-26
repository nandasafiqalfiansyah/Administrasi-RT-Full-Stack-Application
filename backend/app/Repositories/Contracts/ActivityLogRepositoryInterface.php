<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ActivityLogRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function getRecent(int $limit = 10): Collection;

    public function getByUser(int $userId): Collection;

    public function getByModel(string $modelType, int $modelId): Collection;

    public function getByAction(string $action): Collection;

    public function getByDateRange(string $startDate, string $endDate): Collection;

    public function create(array $data);

    public function paginate(int $perPage = 15);
}