<?php

namespace App\Repositories\Contracts;

interface HouseRepositoryInterface extends BaseRepositoryInterface
{
    public function getDihuniCount(): int;
    public function getTidakDihuniCount(): int;
    public function getTotalRumah(): int;
}