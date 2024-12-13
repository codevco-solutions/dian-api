<?php

namespace App\Services\Customer;

use App\Repositories\Contracts\Customer\CreditRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CreditService
{
    protected $creditRepository;

    public function __construct(CreditRepositoryInterface $creditRepository)
    {
        $this->creditRepository = $creditRepository;
    }

    /**
     * Get customer credit information
     */
    public function getCustomerCredit(int $customerId)
    {
        return $this->creditRepository->getCustomerCredit($customerId);
    }

    /**
     * Update credit limit
     */
    public function updateCreditLimit(int $customerId, float $amount, array $data = [])
    {
        // Validate minimum credit limit
        if ($amount < 0) {
            throw new \Exception('Credit limit cannot be negative');
        }

        // Get current credit status
        $currentCredit = $this->getCustomerCredit($customerId);
        
        // Validate if new limit is less than current balance
        if ($amount < $currentCredit->current_balance) {
            throw new \Exception('New credit limit cannot be less than current balance');
        }

        return $this->creditRepository->updateCreditLimit($customerId, $amount);
    }

    /**
     * Add credit movement
     */
    public function addCreditMovement(int $customerId, array $data)
    {
        // Validate movement type
        if (!in_array($data['type'], ['charge', 'payment'])) {
            throw new \Exception('Invalid movement type');
        }

        // Validate amount
        if ($data['amount'] <= 0) {
            throw new \Exception('Amount must be greater than zero');
        }

        // If it's a charge, validate credit limit
        if ($data['type'] === 'charge') {
            if (!$this->creditRepository->validateCreditLimit($customerId, $data['amount'])) {
                throw new \Exception('Credit limit would be exceeded');
            }
        }

        return $this->creditRepository->addCreditMovement($customerId, $data);
    }

    /**
     * Get credit movements
     */
    public function getCreditMovements(int $customerId, array $filters = []): Collection
    {
        return $this->creditRepository->getCreditMovements($customerId, $filters);
    }

    /**
     * Get current credit balance
     */
    public function getCreditBalance(int $customerId): float
    {
        return $this->creditRepository->getCreditBalance($customerId);
    }

    /**
     * Get credit status
     */
    public function getCreditStatus(int $customerId): array
    {
        return $this->creditRepository->getCreditStatus($customerId);
    }

    /**
     * Get overdue payments
     */
    public function getOverduePayments(int $customerId): Collection
    {
        return $this->creditRepository->getOverduePayments($customerId);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $movementId, string $status, ?array $data = null)
    {
        // Validate status
        if (!in_array($status, ['pending', 'paid', 'cancelled', 'disputed'])) {
            throw new \Exception('Invalid payment status');
        }

        return $this->creditRepository->updatePaymentStatus($movementId, $status, $data);
    }

    /**
     * Get credit history
     */
    public function getCreditHistory(int $customerId): Collection
    {
        return $this->creditRepository->getCreditHistory($customerId);
    }

    /**
     * Get credit metrics
     */
    public function getCreditMetrics(int $customerId): array
    {
        return $this->creditRepository->getCreditMetrics($customerId);
    }

    /**
     * Evaluate credit risk
     */
    public function evaluateCreditRisk(int $customerId): array
    {
        $metrics = $this->getCreditMetrics($customerId);
        $status = $this->getCreditStatus($customerId);
        $history = $this->getCreditHistory($customerId);

        $riskFactors = [];
        $riskScore = 100;

        // Check utilization rate
        if ($metrics['utilization_rate'] > 90) {
            $riskFactors[] = 'High credit utilization';
            $riskScore -= 20;
        } elseif ($metrics['utilization_rate'] > 75) {
            $riskFactors[] = 'Elevated credit utilization';
            $riskScore -= 10;
        }

        // Check overdue payments
        if ($metrics['overdue_count'] > 0) {
            $riskFactors[] = 'Has overdue payments';
            $riskScore -= ($metrics['overdue_count'] * 5);
        }

        // Check average payment time
        if ($metrics['average_payment_time'] > 0) {
            $riskFactors[] = 'Delayed payments trend';
            $riskScore -= min(20, $metrics['average_payment_time']);
        }

        // Check payment history consistency
        $totalTransactions = $metrics['total_charges'] + $metrics['total_payments'];
        if ($totalTransactions < 5) {
            $riskFactors[] = 'Limited credit history';
            $riskScore -= 10;
        }

        return [
            'risk_score' => max(0, $riskScore),
            'risk_level' => $this->calculateRiskLevel($riskScore),
            'risk_factors' => $riskFactors,
            'metrics' => $metrics,
            'status' => $status,
            'recommendation' => $this->generateCreditRecommendation($riskScore, $metrics)
        ];
    }

    /**
     * Calculate risk level
     */
    protected function calculateRiskLevel(int $score): string
    {
        if ($score >= 80) {
            return 'low';
        } elseif ($score >= 60) {
            return 'moderate';
        } elseif ($score >= 40) {
            return 'high';
        } else {
            return 'critical';
        }
    }

    /**
     * Generate credit recommendation
     */
    protected function generateCreditRecommendation(int $riskScore, array $metrics): array
    {
        $recommendations = [];

        if ($riskScore < 40) {
            $recommendations[] = 'Consider reducing credit limit';
            $recommendations[] = 'Require payment of overdue balances';
        } elseif ($riskScore < 60) {
            $recommendations[] = 'Monitor payment behavior closely';
            $recommendations[] = 'Review credit limit in 3 months';
        } elseif ($riskScore < 80) {
            $recommendations[] = 'Review credit limit in 6 months';
        } else {
            if ($metrics['utilization_rate'] > 75) {
                $recommendations[] = 'Consider increasing credit limit';
            }
        }

        return $recommendations;
    }

    /**
     * Generate credit report
     */
    public function generateCreditReport(int $customerId): array
    {
        $risk = $this->evaluateCreditRisk($customerId);
        $movements = $this->getCreditMovements($customerId, [
            'date_from' => Carbon::now()->subMonths(6)
        ]);

        return [
            'customer_id' => $customerId,
            'report_date' => Carbon::now(),
            'risk_assessment' => $risk,
            'credit_status' => $this->getCreditStatus($customerId),
            'metrics' => $this->getCreditMetrics($customerId),
            'recent_movements' => $movements,
            'recommendations' => $risk['recommendation']
        ];
    }
}
