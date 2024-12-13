<?php

namespace App\Repositories\Contracts\Product;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface MeasurementUnitRepositoryInterface extends BaseRepositoryInterface
{
    public function getBaseUnits(): Collection;
    public function getCompositeUnits(): Collection;
    public function getUnitConversions(int $unitId): Collection;
    public function createCompositeUnit(array $data);
    public function updateCompositeUnit(int $unitId, array $data);
    public function addUnitConversion(int $fromUnitId, int $toUnitId, array $data);
    public function updateUnitConversion(int $conversionId, array $data);
    public function removeUnitConversion(int $conversionId): bool;
    public function getConversionPath(int $fromUnitId, int $toUnitId): array;
    public function validateConversion(int $fromUnitId, int $toUnitId): bool;
    public function getUnitComponents(int $compositeUnitId): Collection;
    public function setUnitComponents(int $compositeUnitId, array $components);
}
