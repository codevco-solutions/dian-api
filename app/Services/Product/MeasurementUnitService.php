<?php

namespace App\Services\Product;

use App\Repositories\Contracts\Product\MeasurementUnitRepositoryInterface;
use Illuminate\Support\Collection;

class MeasurementUnitService
{
    protected $unitRepository;

    public function __construct(MeasurementUnitRepositoryInterface $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    /**
     * Get all base units
     */
    public function getBaseUnits(): Collection
    {
        return $this->unitRepository->getBaseUnits();
    }

    /**
     * Get all composite units
     */
    public function getCompositeUnits(): Collection
    {
        return $this->unitRepository->getCompositeUnits();
    }

    /**
     * Create a new composite unit
     */
    public function createCompositeUnit(array $data)
    {
        $this->validateCompositeUnit($data);
        return $this->unitRepository->createCompositeUnit($data);
    }

    /**
     * Update a composite unit
     */
    public function updateCompositeUnit(int $unitId, array $data)
    {
        if (isset($data['components'])) {
            $this->validateCompositeUnit($data);
        }
        return $this->unitRepository->updateCompositeUnit($unitId, $data);
    }

    /**
     * Add a conversion between units
     */
    public function addUnitConversion(int $fromUnitId, int $toUnitId, array $data)
    {
        return $this->unitRepository->addUnitConversion($fromUnitId, $toUnitId, $data);
    }

    /**
     * Update a unit conversion
     */
    public function updateUnitConversion(int $conversionId, array $data)
    {
        return $this->unitRepository->updateUnitConversion($conversionId, $data);
    }

    /**
     * Remove a unit conversion
     */
    public function removeUnitConversion(int $conversionId): bool
    {
        return $this->unitRepository->removeUnitConversion($conversionId);
    }

    /**
     * Convert value between units
     */
    public function convertValue(float $value, int $fromUnitId, int $toUnitId): float
    {
        $factor = $this->unitRepository->calculateConversionFactor($fromUnitId, $toUnitId);
        return $value * $factor['multiplier'] + $factor['offset'];
    }

    /**
     * Get all possible conversions for a unit
     */
    public function getUnitConversions(int $unitId): Collection
    {
        return $this->unitRepository->getUnitConversions($unitId);
    }

    /**
     * Get components of a composite unit
     */
    public function getUnitComponents(int $compositeUnitId): Collection
    {
        return $this->unitRepository->getUnitComponents($compositeUnitId);
    }

    /**
     * Set components for a composite unit
     */
    public function setUnitComponents(int $compositeUnitId, array $components)
    {
        return $this->unitRepository->setUnitComponents($compositeUnitId, $components);
    }

    /**
     * Validate composite unit data
     */
    protected function validateCompositeUnit(array $data): void
    {
        if (!isset($data['components']) || empty($data['components'])) {
            throw new \Exception('Composite unit must have at least one component');
        }

        $baseUnits = $this->getBaseUnits()->pluck('id')->toArray();
        foreach ($data['components'] as $component) {
            if (!isset($component['unit_id']) || !in_array($component['unit_id'], $baseUnits)) {
                throw new \Exception('Invalid component unit');
            }

            if (isset($component['operator']) && !in_array($component['operator'], ['*', '/', '+', '-'])) {
                throw new \Exception('Invalid component operator');
            }
        }
    }

    /**
     * Get conversion path between units
     */
    public function getConversionPath(int $fromUnitId, int $toUnitId): array
    {
        return $this->unitRepository->getConversionPath($fromUnitId, $toUnitId);
    }

    /**
     * Validate if conversion is possible between units
     */
    public function validateConversion(int $fromUnitId, int $toUnitId): bool
    {
        return $this->unitRepository->validateConversion($fromUnitId, $toUnitId);
    }

    /**
     * Format composite unit display
     */
    public function formatCompositeUnit(int $compositeUnitId): string
    {
        $components = $this->getUnitComponents($compositeUnitId);
        $parts = [];

        foreach ($components as $component) {
            $unit = $component->unit->symbol ?? $component->unit->name;
            $quantity = $component->quantity;

            if ($quantity == 1) {
                $parts[] = $unit;
            } else {
                $parts[] = "{$unit}^{$quantity}";
            }
        }

        return implode('·', $parts);
    }

    /**
     * Calculate derived value for composite unit
     */
    public function calculateCompositeValue(int $compositeUnitId, array $componentValues): float
    {
        $components = $this->getUnitComponents($compositeUnitId);
        $result = 1;

        foreach ($components as $component) {
            if (!isset($componentValues[$component->unit_id])) {
                throw new \Exception("Missing value for component unit {$component->unit_id}");
            }

            $value = $componentValues[$component->unit_id];

            switch ($component->operator) {
                case '*':
                    $result *= pow($value, $component->quantity);
                    break;
                case '/':
                    $result /= pow($value, $component->quantity);
                    break;
                case '+':
                    $result += $value * $component->quantity;
                    break;
                case '-':
                    $result -= $value * $component->quantity;
                    break;
            }
        }

        return $result;
    }
}
