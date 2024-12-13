<?php

namespace App\Repositories\Contracts\Customer;

use Illuminate\Support\Collection;

interface CustomerClassificationRepositoryInterface
{
    /**
     * Obtener todas las clasificaciones
     */
    public function getAllClassifications(): Collection;

    /**
     * Obtener una clasificación específica
     */
    public function getClassification(int $classificationId);

    /**
     * Crear una nueva clasificación
     */
    public function createClassification(array $data);

    /**
     * Actualizar una clasificación
     */
    public function updateClassification(int $classificationId, array $data);

    /**
     * Eliminar una clasificación
     */
    public function deleteClassification(int $classificationId): bool;

    /**
     * Asignar clasificación a cliente
     */
    public function assignClassificationToCustomer(int $customerId, int $classificationId): bool;

    /**
     * Obtener clientes por clasificación
     */
    public function getCustomersByClassification(int $classificationId): Collection;

    /**
     * Evaluar y actualizar clasificación de cliente
     */
    public function evaluateCustomerClassification(int $customerId): array;

    /**
     * Obtener métricas de clasificación
     */
    public function getClassificationMetrics(int $classificationId): array;
}
