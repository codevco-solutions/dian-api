<?php

namespace App\Repositories\Eloquent\Common;

use App\Models\Common\Address;
use App\Repositories\Contracts\Common\AddressRepositoryInterface;

class AddressRepository implements AddressRepositoryInterface
{
    protected $model;

    public function __construct(Address $model)
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

    public function findByEntity(string $entityType, int $entityId)
    {
        return $this->model
            ->where('addressable_type', $entityType)
            ->where('addressable_id', $entityId)
            ->get();
    }
}
