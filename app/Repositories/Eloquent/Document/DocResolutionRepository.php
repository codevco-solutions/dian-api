<?php

namespace App\Repositories\Eloquent\Document;

use App\Models\Document\DocResolution;
use App\Repositories\Contracts\Document\DocResolutionRepositoryInterface;

class DocResolutionRepository implements DocResolutionRepositoryInterface
{
    protected $model;

    public function __construct(DocResolution $model)
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