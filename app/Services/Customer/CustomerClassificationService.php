<?php

namespace App\Services\Customer;

use App\Repositories\Contracts\Customer\CustomerClassificationRepositoryInterface;
use Illuminate\Support\Collection;

class CustomerClassificationService
{
    protected $classificationRepository;

    public function __construct(CustomerClassificationRepositoryInterface $classificationRepository)
    {
        $this->classificationRepository = $classificationRepository;
    }

    /**
     * Obtener todas las clasificaciones
     */
    public function getAllClassifications(): Collection
    {
        return $this->classificationRepository->getAllClassifications();
    }

    /**
     * Crear nueva clasificación
     */
    public function createClassification(array $data)
    {
        $this->validateClassificationData($data);
        return $this->classificationRepository->createClassification($data);
    }

    /**
     * Actualizar clasificación
     */
    public function updateClassification(int $classificationId, array $data)
    {
        $this->validateClassificationData($data);
        return $this->classificationRepository->updateClassification($classificationId, $data);
    }

    /**
     * Eliminar clasificación
     */
    public function deleteClassification(int $classificationId): bool
    {
        // Verificar si hay clientes usando esta clasificación
        $customers = $this->classificationRepository->getCustomersByClassification($classificationId);
        if ($customers->isNotEmpty()) {
            throw new \Exception('No se puede eliminar la clasificación porque hay clientes asignados a ella');
        }

        return $this->classificationRepository->deleteClassification($classificationId);
    }

    /**
     * Asignar clasificación a cliente
     */
    public function assignClassificationToCustomer(int $customerId, int $classificationId): bool
    {
        // Verificar que la clasificación existe
        $this->classificationRepository->getClassification($classificationId);
        
        return $this->classificationRepository->assignClassificationToCustomer($customerId, $classificationId);
    }

    /**
     * Evaluar clasificación de cliente
     */
    public function evaluateCustomerClassification(int $customerId): array
    {
        return $this->classificationRepository->evaluateCustomerClassification($customerId);
    }

    /**
     * Obtener métricas de clasificación
     */
    public function getClassificationMetrics(int $classificationId): array
    {
        return $this->classificationRepository->getClassificationMetrics($classificationId);
    }

    /**
     * Evaluar clasificaciones de todos los clientes
     */
    public function evaluateAllCustomersClassification(): array
    {
        $customers = $this->classificationRepository->getCustomersByClassification(null);
        $results = [];

        foreach ($customers as $customer) {
            try {
                $results[$customer->id] = $this->evaluateCustomerClassification($customer->id);
            } catch (\Exception $e) {
                $results[$customer->id] = [
                    'error' => $e->getMessage(),
                    'customer_id' => $customer->id
                ];
            }
        }

        return $results;
    }

    /**
     * Validar datos de clasificación
     */
    protected function validateClassificationData(array $data): void
    {
        if (isset($data['min_purchase_amount']) && $data['min_purchase_amount'] < 0) {
            throw new \Exception('El monto mínimo de compra no puede ser negativo');
        }

        if (isset($data['min_purchase_frequency']) && $data['min_purchase_frequency'] < 0) {
            throw new \Exception('La frecuencia mínima de compra no puede ser negativa');
        }

        if (isset($data['payment_behavior_score'])) {
            if ($data['payment_behavior_score'] < 0 || $data['payment_behavior_score'] > 100) {
                throw new \Exception('El puntaje de comportamiento de pago debe estar entre 0 y 100');
            }
        }

        if (isset($data['credit_score'])) {
            if ($data['credit_score'] < 0 || $data['credit_score'] > 100) {
                throw new \Exception('El puntaje de crédito debe estar entre 0 y 100');
            }
        }

        if (isset($data['criteria']) && !is_array($data['criteria'])) {
            throw new \Exception('Los criterios deben ser especificados como un array');
        }
    }
}
