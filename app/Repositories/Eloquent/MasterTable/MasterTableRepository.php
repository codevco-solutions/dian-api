<?php

namespace App\Repositories\Eloquent\MasterTable;

use App\Models\MasterTable;
use App\Repositories\Contracts\MasterTable\MasterTableRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MasterTableRepository implements MasterTableRepositoryInterface
{
    protected $model;

    public function __construct(MasterTable $model)
    {
        $this->model = $model;
    }

    public function all(string $table)
    {
        return $this->model->setTableName($table)->all();
    }

    public function findById(string $table, int $id)
    {
        $record = $this->model->setTableName($table)->find($id);
        
        if (!$record) {
            throw new ModelNotFoundException("Record not found in {$table}");
        }

        return $record;
    }

    public function findByCode(string $table, string $code)
    {
        $record = $this->model->setTableName($table)->where('code', $code)->first();
        
        if (!$record) {
            throw new ModelNotFoundException("Record with code {$code} not found in {$table}");
        }

        return $record;
    }

    public function create(string $table, array $data)
    {
        return $this->model->setTableName($table)->create($data);
    }

    public function update(string $table, int $id, array $data)
    {
        $record = $this->findById($table, $id);
        $record->update($data);
        return $record;
    }

    public function delete(string $table, int $id)
    {
        $record = $this->findById($table, $id);
        return $record->delete();
    }

    public function active(string $table)
    {
        return $this->model->setTableName($table)->active()->get();
    }
}
