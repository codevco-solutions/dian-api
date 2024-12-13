<?php

namespace App\Services\Customer;

use App\Repositories\Contracts\Customer\TransactionHistoryRepositoryInterface;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TransactionHistoryService
{
    protected $transactionRepository;

    public function __construct(TransactionHistoryRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Obtener transacciones de cliente
     */
    public function getCustomerTransactions(int $customerId, array $filters = []): Collection
    {
        return $this->transactionRepository->getCustomerTransactions($customerId, $filters);
    }

    /**
     * Registrar nueva transacción
     */
    public function recordTransaction(int $customerId, array $data)
    {
        $this->validateTransactionData($data);

        // Validar que no exista una transacción duplicada
        if (isset($data['reference_type']) && isset($data['reference_id'])) {
            if ($this->transactionRepository->validateTransactionExists($data['reference_type'], $data['reference_id'])) {
                throw new \Exception('Ya existe una transacción registrada para esta referencia');
            }
        }

        return $this->transactionRepository->recordTransaction($customerId, $data);
    }

    /**
     * Actualizar estado de transacción
     */
    public function updateTransactionStatus(int $transactionId, string $status, array $data = [])
    {
        $this->validateTransactionStatus($status);
        return $this->transactionRepository->updateTransactionStatus($transactionId, $status, $data);
    }

    /**
     * Obtener resumen de transacciones
     */
    public function getTransactionsSummary(int $customerId, array $filters = []): array
    {
        return $this->transactionRepository->getTransactionsSummary($customerId, $filters);
    }

    /**
     * Obtener transacciones vencidas
     */
    public function getOverdueTransactions(int $customerId): Collection
    {
        return $this->transactionRepository->getOverdueTransactions($customerId);
    }

    /**
     * Obtener métricas de transacciones
     */
    public function getTransactionMetrics(int $customerId, array $filters = []): array
    {
        return $this->transactionRepository->getTransactionMetrics($customerId, $filters);
    }

    /**
     * Obtener balance actual
     */
    public function getCurrentBalance(int $customerId): float
    {
        return $this->transactionRepository->getCurrentBalance($customerId);
    }

    /**
     * Generar reporte de transacciones
     */
    public function generateTransactionReport(int $customerId, array $filters = []): array
    {
        $transactions = $this->getCustomerTransactions($customerId, $filters);
        $summary = $this->getTransactionsSummary($customerId, $filters);
        $metrics = $this->getTransactionMetrics($customerId, $filters);
        $overdue = $this->getOverdueTransactions($customerId);

        return [
            'report_date' => Carbon::now(),
            'customer_id' => $customerId,
            'current_balance' => $this->getCurrentBalance($customerId),
            'transactions' => $transactions,
            'summary' => $summary,
            'metrics' => $metrics,
            'overdue_transactions' => $overdue,
            'risk_indicators' => $this->calculateRiskIndicators($metrics, $overdue)
        ];
    }

    /**
     * Validar datos de transacción
     */
    protected function validateTransactionData(array $data): void
    {
        if (!isset($data['transaction_type'])) {
            throw new \Exception('El tipo de transacción es requerido');
        }

        if (!in_array($data['transaction_type'], ['charge', 'payment', 'adjustment'])) {
            throw new \Exception('Tipo de transacción inválido');
        }

        if (!isset($data['amount']) || $data['amount'] <= 0) {
            throw new \Exception('El monto debe ser mayor que cero');
        }

        if (isset($data['due_date'])) {
            $dueDate = Carbon::parse($data['due_date']);
            if ($dueDate->isPast()) {
                throw new \Exception('La fecha de vencimiento no puede ser en el pasado');
            }
        }
    }

    /**
     * Validar estado de transacción
     */
    protected function validateTransactionStatus(string $status): void
    {
        if (!in_array($status, ['pending', 'paid', 'cancelled', 'overdue', 'disputed'])) {
            throw new \Exception('Estado de transacción inválido');
        }
    }

    /**
     * Calcular indicadores de riesgo
     */
    protected function calculateRiskIndicators(array $metrics, Collection $overdueTransactions): array
    {
        $riskScore = 100;
        $riskFactors = [];

        // Evaluar pago a tiempo
        if ($metrics['payment_reliability'] < 80) {
            $riskScore -= 20;
            $riskFactors[] = 'Baja confiabilidad en pagos';
        }

        // Evaluar transacciones vencidas
        if ($overdueTransactions->isNotEmpty()) {
            $riskScore -= min(30, $overdueTransactions->count() * 5);
            $riskFactors[] = 'Tiene transacciones vencidas';
        }

        // Evaluar frecuencia de transacciones
        if ($metrics['transaction_frequency'] < 1) { // Menos de una transacción por día
            $riskScore -= 10;
            $riskFactors[] = 'Baja frecuencia de transacciones';
        }

        // Evaluar días promedio de pago
        if ($metrics['average_days_to_pay'] > 30) {
            $riskScore -= 15;
            $riskFactors[] = 'Alto tiempo promedio de pago';
        }

        return [
            'risk_score' => max(0, $riskScore),
            'risk_level' => $this->determineRiskLevel($riskScore),
            'risk_factors' => $riskFactors
        ];
    }

    /**
     * Determinar nivel de riesgo
     */
    protected function determineRiskLevel(int $score): string
    {
        if ($score >= 80) return 'bajo';
        if ($score >= 60) return 'moderado';
        if ($score >= 40) return 'alto';
        return 'crítico';
    }
}
