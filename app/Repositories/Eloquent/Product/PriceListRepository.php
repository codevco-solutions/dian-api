<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\PriceList;
use App\Repositories\Contracts\Product\PriceListRepositoryInterface;

class PriceListRepository implements PriceListRepositoryInterface
{
    protected $model;

    public function __construct(PriceList $model)
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

    public function findByProduct(int $productId)
    {
        return $this->model->where('product_id', $productId)->get();
    }
}
