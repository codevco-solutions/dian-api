<?php

namespace App\Repositories\Contracts\Customer;

use Illuminate\Support\Collection;

interface PaymentTermRepositoryInterface
{
    /**
     * Obtener términos de pago de un cliente
     */
    public function getCustomerPaymentTerms(int $customerId): Collection;

    /**
     * Obtener un término de pago específico
     */
    public function getPaymentTerm(int $termId);

    /**
     * Crear un nuevo término de pago
     */
    public function createPaymentTerm(int $customerId, array $data);

    /**
     * Actualizar un término de pago
     */
    public function updatePaymentTerm(int $termId, array $data);

    /**
     * Eliminar un término de pago
     */
    public function deletePaymentTerm(int $termId): bool;

    /**
     * Establecer término de pago por defecto
     */
    public function setDefaultPaymentTerm(int $customerId, int $termId): bool;

    /**
     * Obtener el término de pago por defecto de un cliente
     */
    public function getDefaultPaymentTerm(int $customerId);

    /**
     * Validar si un término de pago pertenece a un cliente
     */
    public function validateTermBelongsToCustomer(int $customerId, int $termId): bool;
}
