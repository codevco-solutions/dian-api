<?php

namespace App\Repositories\Eloquent\Payroll;

use App\Models\Payroll\PayrollDeduction;
use App\Repositories\Contracts\Payroll\PayrollDeductionRepositoryInterface;

class PayrollDeductionRepository implements PayrollDeductionRepositoryInterface
{
    protected $model;

    public function __construct(PayrollDeduction $model)
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