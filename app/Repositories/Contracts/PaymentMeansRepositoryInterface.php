<?php

namespace App\Repositories\Contracts;

interface PaymentMeansRepositoryInterface
{
    public function create(array $data);
    public function update($id, array $data);
    public function find($id);
    public function findByInvoice($invoiceId);
    public function delete($id);
}
