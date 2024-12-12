<?php

namespace App\Services\Document;

use App\Repositories\Contracts\PaymentMeansRepositoryInterface;

class PaymentMeansService
{
    protected $repository;

    public function __construct(PaymentMeansRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        // Validar códigos de medio de pago según DIAN
        $this->validatePaymentMeansCode($data['payment_means_code']);
        
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        if (isset($data['payment_means_code'])) {
            $this->validatePaymentMeansCode($data['payment_means_code']);
        }

        return $this->repository->update($id, $data);
    }

    public function getByInvoice($invoiceId)
    {
        return $this->repository->findByInvoice($invoiceId);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    protected function validatePaymentMeansCode($code)
    {
        // Validar que el código exista en la lista de códigos permitidos por la DIAN
        $validCodes = [1, 2, 3, 4, 5]; // Agregar códigos válidos según DIAN
        if (!in_array($code, $validCodes)) {
            throw new \InvalidArgumentException('Invalid payment means code');
        }
    }
}
