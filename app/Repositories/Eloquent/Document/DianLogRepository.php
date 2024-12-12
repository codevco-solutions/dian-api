<?php

namespace App\Repositories\Eloquent\Document;

use App\Models\Document\DianLog;
use App\Repositories\Contracts\Document\DianLogRepositoryInterface;

class DianLogRepository implements DianLogRepositoryInterface
{
    protected $model;

    public function __construct(DianLog $model)
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
}