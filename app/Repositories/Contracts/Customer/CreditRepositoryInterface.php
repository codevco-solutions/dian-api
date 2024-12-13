<?php

namespace App\Repositories\Contracts\Customer;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface CreditRepositoryInterface extends BaseRepositoryInterface
{
    public function getCustomerCredit(int $customerId);
    public function updateCreditLimit(int $customerId, float $amount);
    public function addCreditMovement(int $customerId, array $data);
    public function getCreditMovements(int $customerId, array $filters = []): Collection;
    public function getCreditBalance(int $customerId): float;
    public function validateCreditLimit(int $customerId, float $amount): bool;
    public function getCreditStatus(int $customerId): array;
    public function getOverduePayments(int $customerId): Collection;
    public function updatePaymentStatus(int $movementId, string $status, ?array $data = null);
    public function getCreditHistory(int $customerId): Collection;
    public function getAveragePaymentTime(int $customerId): float;
    public function getCreditMetrics(int $customerId): array;
}
