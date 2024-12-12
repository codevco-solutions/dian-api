<?php

namespace App\Repositories\Eloquent\MasterTable;

use App\Models\MasterTable\TaxRegime;
use App\Repositories\Contracts\MasterTable\TaxRegimeRepositoryInterface;

class TaxRegimeRepository implements TaxRegimeRepositoryInterface
{
    protected $model;

    public function __construct(TaxRegime $model)
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