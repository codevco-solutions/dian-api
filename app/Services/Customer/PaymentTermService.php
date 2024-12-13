<?php

namespace App\Services\Customer;

use App\Repositories\Contracts\Customer\PaymentTermRepositoryInterface;
use Illuminate\Support\Collection;

class PaymentTermService
{
    protected $paymentTermRepository;

    public function __construct(PaymentTermRepositoryInterface $paymentTermRepository)
    {
        $this->paymentTermRepository = $paymentTermRepository;
    }

    /**
     * Obtener términos de pago de un cliente
     */
    public function getCustomerPaymentTerms(int $customerId): Collection
    {
        return $this->paymentTermRepository->getCustomerPaymentTerms($customerId);
    }

    /**
     * Crear un nuevo término de pago
     */
    public function createPaymentTerm(int $customerId, array $data)
    {
        $this->validatePaymentTermData($data);
        return $this->paymentTermRepository->createPaymentTerm($customerId, $data);
    }

    /**
     * Actualizar un término de pago
     */
    public function updatePaymentTerm(int $customerId, int $termId, array $data)
    {
        $this->validateTermOwnership($customerId, $termId);
        $this->validatePaymentTermData($data);
        return $this->paymentTermRepository->updatePaymentTerm($termId, $data);
    }

    /**
     * Eliminar un término de pago
     */
    public function deletePaymentTerm(int $customerId, int $termId): bool
    {
        $this->validateTermOwnership($customerId, $termId);
        return $this->paymentTermRepository->deletePaymentTerm($termId);
    }

    /**
     * Establecer término de pago por defecto
     */
    public function setDefaultPaymentTerm(int $customerId, int $termId): bool
    {
        $this->validateTermOwnership($customerId, $termId);
        return $this->paymentTermRepository->setDefaultPaymentTerm($customerId, $termId);
    }

    /**
     * Obtener el término de pago por defecto
     */
    public function getDefaultPaymentTerm(int $customerId)
    {
        return $this->paymentTermRepository->getDefaultPaymentTerm($customerId);
    }

    /**
     * Calcular descuento por pronto pago
     */
    public function calculateEarlyPaymentDiscount(int $termId, float $amount, ?string $paymentDate = null): array
    {
        $term = $this->paymentTermRepository->getPaymentTerm($termId);
        $paymentDate = $paymentDate ? new \DateTime($paymentDate) : new \DateTime();
        
        // Si no hay descuento configurado, retornar sin descuento
        if (!$term->discount_percentage || !$term->discount_days) {
            return [
                'original_amount' => $amount,
                'discount_amount' => 0,
                'final_amount' => $amount,
                'discount_applicable' => false,
                'discount_deadline' => null
            ];
        }

        // Calcular fecha límite para descuento
        $discountDeadline = (new \DateTime())->add(new \DateInterval("P{$term->discount_days}D"));
        
        // Verificar si aplica el descuento
        $discountApplicable = $paymentDate <= $discountDeadline;
        
        // Calcular montos
        $discountAmount = $discountApplicable ? ($amount * ($term->discount_percentage / 100)) : 0;
        $finalAmount = $amount - $discountAmount;

        return [
            'original_amount' => $amount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'discount_applicable' => $discountApplicable,
            'discount_deadline' => $discountDeadline->format('Y-m-d')
        ];
    }

    /**
     * Validar datos del término de pago
     */
    protected function validatePaymentTermData(array $data): void
    {
        // Validar días de pago
        if (isset($data['days']) && $data['days'] < 0) {
            throw new \Exception('Los días de pago no pueden ser negativos');
        }

        // Validar porcentaje de descuento
        if (isset($data['discount_percentage'])) {
            if ($data['discount_percentage'] < 0 || $data['discount_percentage'] > 100) {
                throw new \Exception('El porcentaje de descuento debe estar entre 0 y 100');
            }
        }

        // Validar días de descuento
        if (isset($data['discount_days'])) {
            if ($data['discount_days'] < 0) {
                throw new \Exception('Los días de descuento no pueden ser negativos');
            }
            if (isset($data['days']) && $data['discount_days'] >= $data['days']) {
                throw new \Exception('Los días de descuento deben ser menores que los días de pago');
            }
        }
    }

    /**
     * Validar que el término pertenezca al cliente
     */
    protected function validateTermOwnership(int $customerId, int $termId): void
    {
        if (!$this->paymentTermRepository->validateTermBelongsToCustomer($customerId, $termId)) {
            throw new \Exception('El término de pago no pertenece al cliente especificado');
        }
    }
}
