<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWhere(array $criteria)
    {
        return $this->model->where($criteria)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id)
    {
        return $this->find($id)->delete();
    }

    public function getAll($perPage = 15, array $filters = [], array $orderBy = ['created_at' => 'desc'])
    {
        $query = $this->model->query();

        // Apply filters
        foreach ($filters as $field => $value) {
            if (is_string($value)) {
                $query->where($field, 'like', '%' . $value . '%');
            } else {
                $query->where($field, $value);
            }
        }

        // Apply ordering
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        // Apply pagination
        return $query->paginate($perPage);
    }
}
