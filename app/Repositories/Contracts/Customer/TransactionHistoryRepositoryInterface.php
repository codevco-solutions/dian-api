<?php

namespace App\Repositories\Contracts\Customer;

use Illuminate\Support\Collection;

interface TransactionHistoryRepositoryInterface
{
    /**
     * Obtener historial de transacciones de un cliente
     */
    public function getCustomerTransactions(int $customerId, array $filters = []): Collection;

    /**
     * Obtener una transacción específica
     */
    public function getTransaction(int $transactionId);

    /**
     * Registrar una nueva transacción
     */
    public function recordTransaction(int $customerId, array $data);

    /**
     * Actualizar estado de transacción
     */
    public function updateTransactionStatus(int $transactionId, string $status, array $data = []);

    /**
     * Obtener resumen de transacciones
     */
    public function getTransactionsSummary(int $customerId, array $filters = []): array;

    /**
     * Obtener transacciones vencidas
     */
    public function getOverdueTransactions(int $customerId): Collection;

    /**
     * Obtener métricas de transacciones
     */
    public function getTransactionMetrics(int $customerId, array $filters = []): array;

    /**
     * Obtener balance actual
     */
    public function getCurrentBalance(int $customerId): float;

    /**
     * Validar si existe transacción
     */
    public function validateTransactionExists(string $referenceType, int $referenceId): bool;
}
