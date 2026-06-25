<?php

namespace App\Repositories;

use App\Models\House;
use App\Repositories\Contracts\HouseRepositoryInterface;

class HouseRepository extends BaseRepository implements HouseRepositoryInterface
{
    public function __construct(House $model)
    {
        parent::__construct($model);
    }

    public function getDihuniCount(): int
    {
        return $this->model->dihuni()->count();
    }

    public function getTidakDihuniCount(): int
    {
        return $this->model->tidakDihuni()->count();
    }

    public function getTotalRumah(): int
    {
        return $this->model->count();
    }
}