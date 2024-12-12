<?php

namespace App\Repositories\Eloquent\MasterTable;

use App\Models\MasterTable\ReferencePrice;
use App\Repositories\Contracts\MasterTable\ReferencePriceRepositoryInterface;

class ReferencePriceRepository implements ReferencePriceRepositoryInterface
{
    protected $model;

    public function __construct(ReferencePrice $model)
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

    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }
}