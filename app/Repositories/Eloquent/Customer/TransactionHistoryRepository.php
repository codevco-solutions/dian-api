<?php

namespace App\Repositories\Eloquent\Customer;

use App\Models\Customer\TransactionHistory;
use App\Repositories\Contracts\Customer\TransactionHistoryRepositoryInterface;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionHistoryRepository implements TransactionHistoryRepositoryInterface
{
    protected $model;

    public function __construct(TransactionHistory $model)
    {
        $this->model = $model;
    }

    public function getCustomerTransactions(int $customerId, array $filters = []): Collection
    {
        $query = $this->model->where('customer_id', $customerId);

        // Aplicar filtros
        if (isset($filters['type'])) {
            $query->where('transaction_type', $filters['type']);
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

    public function getTransaction(int $transactionId)
    {
        return $this->model->findOrFail($transactionId);
    }

    public function recordTransaction(int $customerId, array $data)
    {
        // Obtener balance actual
        $currentBalance = $this->getCurrentBalance($customerId);

        // Calcular nuevo balance
        $balanceAfter = $this->calculateNewBalance($currentBalance, $data['amount'], $data['transaction_type']);

        // Preparar datos de la transacción
        $transactionData = array_merge($data, [
            'customer_id' => $customerId,
            'balance_before' => $currentBalance,
            'balance_after' => $balanceAfter
        ]);

        return DB::transaction(function () use ($transactionData) {
            return $this->model->create($transactionData);
        });
    }

    public function updateTransactionStatus(int $transactionId, string $status, array $data = [])
    {
        $transaction = $this->getTransaction($transactionId);
        
        $updateData = array_merge($data, [
            'status' => $status,
            'payment_date' => $status === 'paid' ? Carbon::now() : null
        ]);

        $transaction->update($updateData);
        return $transaction->fresh();
    }

    public function getTransactionsSummary(int $customerId, array $filters = []): array
    {
        $query = $this->model->where('customer_id', $customerId);

        // Aplicar filtros de fecha
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_transactions' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'average_amount' => $query->avg('amount'),
            'total_paid' => $query->where('status', 'paid')->sum('amount'),
            'total_pending' => $query->where('status', 'pending')->sum('amount'),
            'total_overdue' => $query->where('status', 'overdue')->sum('amount')
        ];
    }

    public function getOverdueTransactions(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->where('due_date', '<', Carbon::now())
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();
    }

    public function getTransactionMetrics(int $customerId, array $filters = []): array
    {
        $transactions = $this->getCustomerTransactions($customerId, $filters);
        
        $totalTransactions = $transactions->count();
        $paidOnTime = $transactions->where('status', 'paid')
            ->filter(function ($transaction) {
                return $transaction->payment_date <= $transaction->due_date;
            })->count();

        return [
            'total_transactions' => $totalTransactions,
            'paid_on_time' => $paidOnTime,
            'payment_reliability' => $totalTransactions > 0 ? ($paidOnTime / $totalTransactions) * 100 : 0,
            'average_days_to_pay' => $this->calculateAverageDaysToPay($transactions),
            'most_common_amount' => $this->calculateMostCommonAmount($transactions),
            'transaction_frequency' => $this->calculateTransactionFrequency($transactions)
        ];
    }

    public function getCurrentBalance(int $customerId): float
    {
        $lastTransaction = $this->model
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastTransaction ? $lastTransaction->balance_after : 0;
    }

    public function validateTransactionExists(string $referenceType, int $referenceId): bool
    {
        return $this->model
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->exists();
    }

    protected function calculateNewBalance(float $currentBalance, float $amount, string $type): float
    {
        return $type === 'charge' ? $currentBalance + $amount : $currentBalance - $amount;
    }

    protected function calculateAverageDaysToPay(Collection $transactions): float
    {
        $paidTransactions = $transactions->where('status', 'paid')
            ->filter(function ($transaction) {
                return $transaction->payment_date && $transaction->due_date;
            });

        if ($paidTransactions->isEmpty()) {
            return 0;
        }

        $totalDays = $paidTransactions->sum(function ($transaction) {
            return Carbon::parse($transaction->due_date)
                ->diffInDays(Carbon::parse($transaction->payment_date));
        });

        return $totalDays / $paidTransactions->count();
    }

    protected function calculateMostCommonAmount(Collection $transactions): float
    {
        if ($transactions->isEmpty()) {
            return 0;
        }

        $amounts = $transactions->groupBy('amount')
            ->map(function ($group) {
                return $group->count();
            });

        return (float) $amounts->sortDesc()->keys()->first();
    }

    protected function calculateTransactionFrequency(Collection $transactions): float
    {
        if ($transactions->count() < 2) {
            return 0;
        }

        $firstTransaction = $transactions->sortBy('created_at')->first();
        $lastTransaction = $transactions->sortBy('created_at')->last();
        $daysDifference = Carbon::parse($firstTransaction->created_at)
            ->diffInDays(Carbon::parse($lastTransaction->created_at));

        return $daysDifference > 0 ? $transactions->count() / $daysDifference : 0;
    }
}
