<?php

namespace App\Repositories\Eloquent;

use App\Models\Document\Commercial\PaymentMeans;
use App\Repositories\Contracts\PaymentMeansRepositoryInterface;

class PaymentMeansRepository implements PaymentMeansRepositoryInterface
{
    protected $model;

    public function __construct(PaymentMeans $model)
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

    public function findByInvoice($invoiceId)
    {
        return $this->model->where('invoice_id', $invoiceId)->get();
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
