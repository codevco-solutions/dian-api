<?php

namespace App\Repositories\Eloquent;

use App\Models\Document\Commercial\DocumentReference;
use App\Repositories\Contracts\DocumentReferenceRepositoryInterface;

class DocumentReferenceRepository implements DocumentReferenceRepositoryInterface
{
    protected $model;

    public function __construct(DocumentReference $model)
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
            ->where('referenceable_type', $documentType)
            ->where('referenceable_id', $documentId)
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
