<?php

namespace App\Repositories\Eloquent\Customer;

use App\Models\Customer\PaymentTerm;
use App\Repositories\Contracts\Customer\PaymentTermRepositoryInterface;
use Illuminate\Support\Collection;

class PaymentTermRepository implements PaymentTermRepositoryInterface
{
    protected $model;

    public function __construct(PaymentTerm $model)
    {
        $this->model = $model;
    }

    public function getCustomerPaymentTerms(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();
    }

    public function getPaymentTerm(int $termId)
    {
        return $this->model->findOrFail($termId);
    }

    public function createPaymentTerm(int $customerId, array $data)
    {
        $data['customer_id'] = $customerId;
        
        // Si es el primer término de pago, establecerlo como predeterminado
        if ($this->getCustomerPaymentTerms($customerId)->isEmpty()) {
            $data['is_default'] = true;
        }

        // Si se marca como predeterminado, actualizar los otros términos
        if (isset($data['is_default']) && $data['is_default']) {
            $this->clearDefaultTerms($customerId);
        }

        return $this->model->create($data);
    }

    public function updatePaymentTerm(int $termId, array $data)
    {
        $term = $this->getPaymentTerm($termId);

        // Si se marca como predeterminado, actualizar los otros términos
        if (isset($data['is_default']) && $data['is_default'] && !$term->is_default) {
            $this->clearDefaultTerms($term->customer_id);
        }

        $term->update($data);
        return $term->fresh();
    }

    public function deletePaymentTerm(int $termId): bool
    {
        $term = $this->getPaymentTerm($termId);

        // Si es el término predeterminado, establecer otro como predeterminado
        if ($term->is_default) {
            $newDefault = $this->model
                ->where('customer_id', $term->customer_id)
                ->where('id', '!=', $termId)
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return $term->delete();
    }

    public function setDefaultPaymentTerm(int $customerId, int $termId): bool
    {
        // Limpiar términos predeterminados actuales
        $this->clearDefaultTerms($customerId);

        // Establecer el nuevo término predeterminado
        return $this->model
            ->where('customer_id', $customerId)
            ->where('id', $termId)
            ->update(['is_default' => true]);
    }

    public function getDefaultPaymentTerm(int $customerId)
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->where('is_default', true)
            ->first();
    }

    public function validateTermBelongsToCustomer(int $customerId, int $termId): bool
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->where('id', $termId)
            ->exists();
    }

    protected function clearDefaultTerms(int $customerId): void
    {
        $this->model
            ->where('customer_id', $customerId)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
