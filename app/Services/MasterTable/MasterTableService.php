<?php

namespace App\Services\MasterTable;

use App\Models\MasterTable\Currency;
use App\Models\MasterTable\IdentificationType;
use App\Models\MasterTable\OperationType;
use App\Models\MasterTable\OrganizationType;
use App\Models\MasterTable\TaxRegime;
use App\Models\MasterTable\TaxResponsibility;
use Illuminate\Support\Facades\DB;

class MasterTableService
{
    protected $models = [
        'currencies' => Currency::class,
        'identification_types' => IdentificationType::class,
        'organization_types' => OrganizationType::class,
        'tax_regimes' => TaxRegime::class,
        'tax_responsibilities' => TaxResponsibility::class,
        'operation_types' => OperationType::class
    ];

    public function getAll(string $table)
    {
        $modelClass = $this->getModelClass($table);
        return $modelClass::all();
    }

    public function getById(string $table, int $id)
    {
        $modelClass = $this->getModelClass($table);
        $record = $modelClass::find($id);
        
        if (!$record) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Record not found in {$table}");
        }

        return $record;
    }

    public function getByCode(string $table, string $code)
    {
        $modelClass = $this->getModelClass($table);
        $record = $modelClass::where('code', $code)->first();
        
        if (!$record) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Record with code {$code} not found in {$table}");
        }

        return $record;
    }

    public function create(string $table, array $data)
    {
        $modelClass = $this->getModelClass($table);
        return DB::transaction(function () use ($modelClass, $data) {
            return $modelClass::create($data);
        });
    }

    public function update(string $table, int $id, array $data)
    {
        $modelClass = $this->getModelClass($table);
        return DB::transaction(function () use ($modelClass, $id, $data) {
            $record = $modelClass::find($id);
            if (!$record) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Record not found in {$table}");
            }
            $record->update($data);
            return $record;
        });
    }

    public function delete(string $table, int $id)
    {
        $modelClass = $this->getModelClass($table);
        return DB::transaction(function () use ($modelClass, $id) {
            $record = $modelClass::find($id);
            if (!$record) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Record not found in {$table}");
            }
            return $record->delete();
        });
    }

    public function getActive(string $table)
    {
        $modelClass = $this->getModelClass($table);
        return $modelClass::active()->get();
    }

    protected function getModelClass(string $table)
    {
        if (!isset($this->models[$table])) {
            throw new \InvalidArgumentException("Invalid table name: {$table}");
        }

        return $this->models[$table];
    }
}
