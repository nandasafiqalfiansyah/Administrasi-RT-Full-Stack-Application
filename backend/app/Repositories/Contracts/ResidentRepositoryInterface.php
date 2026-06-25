<?php

namespace App\Repositories\Contracts;

interface ResidentRepositoryInterface extends BaseRepositoryInterface
{
    public function getTetapCount(): int;
    public function getKontrakCount(): int;
    public function getTotalActive(): int;
}