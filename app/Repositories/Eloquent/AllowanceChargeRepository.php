<?php

namespace App\Repositories\Eloquent;

use App\Models\Document\Commercial\AllowanceCharge;
use App\Repositories\Contracts\AllowanceChargeRepositoryInterface;

class AllowanceChargeRepository implements AllowanceChargeRepositoryInterface
{
    protected $model;

    public function __construct(AllowanceCharge $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return null;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByDocument($documentType, $documentId)
    {
        return $this->model
            ->where('chargeable_type', $documentType)
            ->where('chargeable_id', $documentId)
            ->get();
    }

    public function delete($id)
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}
