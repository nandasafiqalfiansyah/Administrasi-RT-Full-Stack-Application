<?php

namespace App\Repositories;

use App\Models\Resident;
use App\Repositories\Contracts\ResidentRepositoryInterface;

class ResidentRepository extends BaseRepository implements ResidentRepositoryInterface
{
    public function __construct(Resident $model)
    {
        parent::__construct($model);
    }

    public function getTetapCount(): int
    {
        return $this->model->tetap()->active()->count();
    }

    public function getKontrakCount(): int
    {
        return $this->model->kontrak()->active()->count();
    }

    public function getTotalActive(): int
    {
        return $this->model->active()->count();
    }
}