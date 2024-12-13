<?php

namespace App\Repositories\Eloquent\Customer;

use App\Models\Customer\CustomerClassification;
use App\Models\Customer\Customer;
use App\Repositories\Contracts\Customer\CustomerClassificationRepositoryInterface;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CustomerClassificationRepository implements CustomerClassificationRepositoryInterface
{
    protected $model;
    protected $customerModel;

    public function __construct(CustomerClassification $model, Customer $customerModel)
    {
        $this->model = $model;
        $this->customerModel = $customerModel;
    }

    public function getAllClassifications(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function getClassification(int $classificationId)
    {
        return $this->model->findOrFail($classificationId);
    }

    public function createClassification(array $data)
    {
        return $this->model->create($data);
    }

    public function updateClassification(int $classificationId, array $data)
    {
        $classification = $this->getClassification($classificationId);
        $classification->update($data);
        return $classification->fresh();
    }

    public function deleteClassification(int $classificationId): bool
    {
        return $this->getClassification($classificationId)->delete();
    }

    public function assignClassificationToCustomer(int $customerId, int $classificationId): bool
    {
        return $this->customerModel->findOrFail($customerId)
            ->update(['classification_id' => $classificationId]);
    }

    public function getCustomersByClassification(int $classificationId): Collection
    {
        return $this->customerModel
            ->where('classification_id', $classificationId)
            ->get();
    }

    public function evaluateCustomerClassification(int $customerId): array
    {
        $customer = $this->customerModel->findOrFail($customerId);
        $classifications = $this->getAllClassifications();
        $metrics = $this->calculateCustomerMetrics($customerId);
        
        $matchingClassifications = $classifications->filter(function ($classification) use ($metrics) {
            return $this->matchesClassificationCriteria($classification, $metrics);
        });

        // Obtener la mejor clasificación basada en los criterios
        $bestClassification = $matchingClassifications->sortByDesc(function ($classification) {
            return $classification->min_purchase_amount;
        })->first();

        // Si se encontró una clasificación diferente a la actual, actualizarla
        if ($bestClassification && $customer->classification_id !== $bestClassification->id) {
            $this->assignClassificationToCustomer($customerId, $bestClassification->id);
        }

        return [
            'previous_classification' => $customer->classification_id,
            'new_classification' => $bestClassification ? $bestClassification->id : null,
            'metrics' => $metrics,
            'evaluation_date' => Carbon::now()
        ];
    }

    public function getClassificationMetrics(int $classificationId): array
    {
        $classification = $this->getClassification($classificationId);
        $customers = $this->getCustomersByClassification($classificationId);

        return [
            'total_customers' => $customers->count(),
            'average_purchase_amount' => $customers->avg('total_purchases'),
            'average_payment_score' => $customers->avg('payment_behavior_score'),
            'total_active_customers' => $customers->where('status', 'active')->count(),
            'criteria' => $classification->criteria,
            'updated_at' => Carbon::now()
        ];
    }

    protected function calculateCustomerMetrics(int $customerId): array
    {
        $customer = $this->customerModel->findOrFail($customerId);
        
        // Aquí deberías obtener y calcular todas las métricas relevantes
        // Este es un ejemplo básico
        return [
            'total_purchases' => $customer->total_purchases ?? 0,
            'purchase_frequency' => $customer->purchase_frequency ?? 0,
            'payment_behavior_score' => $customer->payment_behavior_score ?? 0,
            'credit_score' => $customer->credit_score ?? 0,
            'days_as_customer' => Carbon::parse($customer->created_at)->diffInDays(Carbon::now()),
            'average_transaction_amount' => $customer->average_transaction_amount ?? 0
        ];
    }

    protected function matchesClassificationCriteria($classification, array $metrics): bool
    {
        // Verificar criterios básicos
        if ($metrics['total_purchases'] < $classification->min_purchase_amount) {
            return false;
        }

        if ($metrics['purchase_frequency'] < $classification->min_purchase_frequency) {
            return false;
        }

        if ($metrics['payment_behavior_score'] < $classification->payment_behavior_score) {
            return false;
        }

        if ($metrics['credit_score'] < $classification->credit_score) {
            return false;
        }

        // Verificar criterios adicionales si existen
        if ($classification->criteria) {
            foreach ($classification->criteria as $criterion => $value) {
                if (!isset($metrics[$criterion]) || $metrics[$criterion] < $value) {
                    return false;
                }
            }
        }

        return true;
    }
}
