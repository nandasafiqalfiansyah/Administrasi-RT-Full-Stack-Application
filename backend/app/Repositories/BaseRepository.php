<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (request()->has('search') && request()->search) {
            $query->search(request()->search);
        }

        if (request()->has('sort_by') && request()->sort_by) {
            $direction = request()->sort_direction ?? 'asc';
            $query->orderBy(request()->sort_by, $direction);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage, $columns);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    public function restore(Model $model): bool
    {
        return $model->restore();
    }

    public function search(string $term, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->search($term)->paginate($perPage);
    }

    public function findByField(string $field, mixed $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }

    public function findWhere(array $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }
}