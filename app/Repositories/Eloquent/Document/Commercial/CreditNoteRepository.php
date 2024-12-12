<?php

namespace App\Repositories\Eloquent\Document\Commercial;

use App\Models\Document\Commercial\CreditNote;
use App\Repositories\Contracts\Document\Commercial\CreditNoteRepositoryInterface;

class CreditNoteRepository implements CreditNoteRepositoryInterface
{
    protected $model;

    public function __construct(CreditNote $model)
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