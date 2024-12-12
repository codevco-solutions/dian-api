<?php

namespace App\Repositories\Eloquent\Document\Commercial;

use App\Models\Document\Commercial\DebitNote;
use App\Repositories\Contracts\Document\Commercial\DebitNoteRepositoryInterface;

class DebitNoteRepository implements DebitNoteRepositoryInterface
{
    protected $model;

    public function __construct(DebitNote $model)
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