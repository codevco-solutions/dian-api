<?php

namespace App\Repositories\Eloquent\Customer;

use App\Models\Customer\CustomerCredit;
use App\Models\Customer\CreditMovement;
use App\Repositories\Contracts\Customer\CreditRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CreditRepository extends BaseRepository implements CreditRepositoryInterface
{
    public function __construct(CustomerCredit $model)
    {
        parent::__construct($model);
    }

    public function getCustomerCredit(int $customerId)
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->with(['movements' => function ($query) {
                $query->orderBy('created_at', 'desc')
                    ->limit(5);
            }])
            ->firstOrFail();
    }

    public function updateCreditLimit(int $customerId, float $amount)
    {
        return $this->model->updateOrCreate(
            ['customer_id' => $customerId],
            [
                'credit_limit' => $amount,
                'last_review_date' => Carbon::now()
            ]
        );
    }

    public function addCreditMovement(int $customerId, array $data)
    {
        return DB::transaction(function () use ($customerId, $data) {
            $credit = $this->getCustomerCredit($customerId);
            
            // Create movement
            $movement = CreditMovement::create([
                'customer_credit_id' => $credit->id,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'pending'
            ]);

            // Update credit balance
            $multiplier = $data['type'] === 'charge' ? 1 : -1;
            $credit->current_balance += ($data['amount'] * $multiplier);
            $credit->save();

            return $movement;
        });
    }

    public function getCreditMovements(int $customerId, array $filters = []): Collection
    {
        $query = CreditMovement::whereHas('customerCredit', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        });

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['reference_type'])) {
            $query->where('reference_type', $filters['reference_type']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getCreditBalance(int $customerId): float
    {
        return $this->getCustomerCredit($customerId)->current_balance;
    }

    public function validateCreditLimit(int $customerId, float $amount): bool
    {
        $credit = $this->getCustomerCredit($customerId);
        return ($credit->current_balance + $amount) <= $credit->credit_limit;
    }

    public function getCreditStatus(int $customerId): array
    {
        $credit = $this->getCustomerCredit($customerId);
        $overdueAmount = $this->getOverduePayments($customerId)->sum('amount');

        return [
            'credit_limit' => $credit->credit_limit,
            'current_balance' => $credit->current_balance,
            'available_credit' => $credit->credit_limit - $credit->current_balance,
            'overdue_amount' => $overdueAmount,
            'last_review_date' => $credit->last_review_date,
            'status' => $this->calculateCreditStatus($credit, $overdueAmount)
        ];
    }

    protected function calculateCreditStatus($credit, float $overdueAmount): string
    {
        if ($overdueAmount > 0) {
            return 'overdue';
        }

        $utilizationRate = $credit->current_balance / $credit->credit_limit;

        if ($utilizationRate >= 0.9) {
            return 'critical';
        } elseif ($utilizationRate >= 0.75) {
            return 'warning';
        } else {
            return 'good';
        }
    }

    public function getOverduePayments(int $customerId): Collection
    {
        return CreditMovement::whereHas('customerCredit', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
        ->where('type', 'charge')
        ->where('status', 'pending')
        ->where('due_date', '<', Carbon::now())
        ->get();
    }

    public function updatePaymentStatus(int $movementId, string $status, ?array $data = null)
    {
        return DB::transaction(function () use ($movementId, $status, $data) {
            $movement = CreditMovement::findOrFail($movementId);
            
            if ($status === 'paid' && $movement->status === 'pending') {
                // Create payment movement
                $this->addCreditMovement(
                    $movement->customerCredit->customer_id,
                    [
                        'type' => 'payment',
                        'amount' => $movement->amount,
                        'reference_type' => 'payment',
                        'reference_id' => $movement->id,
                        'status' => 'completed',
                        'notes' => $data['notes'] ?? 'Payment for movement #' . $movement->id
                    ]
                );
            }

            $movement->status = $status;
            $movement->payment_date = $status === 'paid' ? Carbon::now() : null;
            $movement->payment_reference = $data['payment_reference'] ?? null;
            $movement->save();

            return $movement;
        });
    }

    public function getCreditHistory(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->withTrashed()
            ->with(['movements' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();
    }

    public function getAveragePaymentTime(int $customerId): float
    {
        return CreditMovement::whereHas('customerCredit', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })
        ->where('type', 'charge')
        ->where('status', 'paid')
        ->whereNotNull('payment_date')
        ->whereNotNull('due_date')
        ->get()
        ->avg(function ($movement) {
            return Carbon::parse($movement->due_date)
                ->diffInDays($movement->payment_date, false);
        }) ?? 0;
    }

    public function getCreditMetrics(int $customerId): array
    {
        $credit = $this->getCustomerCredit($customerId);
        $movements = $this->getCreditMovements($customerId);
        $overduePayments = $this->getOverduePayments($customerId);

        $totalCharges = $movements->where('type', 'charge')->sum('amount');
        $totalPayments = $movements->where('type', 'payment')->sum('amount');
        $averageTransactionAmount = $movements->average('amount');
        $maxTransactionAmount = $movements->max('amount');

        return [
            'credit_limit' => $credit->credit_limit,
            'current_balance' => $credit->current_balance,
            'available_credit' => $credit->credit_limit - $credit->current_balance,
            'utilization_rate' => ($credit->current_balance / $credit->credit_limit) * 100,
            'total_charges' => $totalCharges,
            'total_payments' => $totalPayments,
            'average_transaction' => $averageTransactionAmount,
            'max_transaction' => $maxTransactionAmount,
            'overdue_count' => $overduePayments->count(),
            'overdue_amount' => $overduePayments->sum('amount'),
            'average_payment_time' => $this->getAveragePaymentTime($customerId),
            'last_review_date' => $credit->last_review_date
        ];
    }
}
