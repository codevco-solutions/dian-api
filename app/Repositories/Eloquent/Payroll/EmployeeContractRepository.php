<?php

namespace App\Repositories\Eloquent\Payroll;

use App\Models\Payroll\EmployeeContract;
use App\Repositories\Contracts\Payroll\EmployeeContractRepositoryInterface;

class EmployeeContractRepository implements EmployeeContractRepositoryInterface
{
    protected $model;

    public function __construct(EmployeeContract $model)
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