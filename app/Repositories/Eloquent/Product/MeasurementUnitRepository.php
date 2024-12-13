<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product\MeasurementUnit;
use App\Models\Product\UnitConversion;
use App\Models\Product\UnitComponent;
use App\Repositories\Contracts\Product\MeasurementUnitRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\Collection;

class MeasurementUnitRepository extends BaseRepository implements MeasurementUnitRepositoryInterface
{
    public function __construct(MeasurementUnit $model)
    {
        parent::__construct($model);
    }

    public function getBaseUnits(): Collection
    {
        return $this->model
            ->where('is_composite', false)
            ->orderBy('name')
            ->get();
    }

    public function getCompositeUnits(): Collection
    {
        return $this->model
            ->where('is_composite', true)
            ->with('components.unit')
            ->orderBy('name')
            ->get();
    }

    public function getUnitConversions(int $unitId): Collection
    {
        return UnitConversion::where('from_unit_id', $unitId)
            ->orWhere('to_unit_id', $unitId)
            ->with(['fromUnit', 'toUnit'])
            ->get();
    }

    public function createCompositeUnit(array $data)
    {
        $data['is_composite'] = true;
        $unit = $this->model->create($data);

        if (isset($data['components'])) {
            $this->setUnitComponents($unit->id, $data['components']);
        }

        return $unit->load('components.unit');
    }

    public function updateCompositeUnit(int $unitId, array $data)
    {
        $unit = $this->model->findOrFail($unitId);

        if (!$unit->is_composite) {
            throw new \Exception('Cannot update a non-composite unit as composite');
        }

        $unit->update($data);

        if (isset($data['components'])) {
            $this->setUnitComponents($unit->id, $data['components']);
        }

        return $unit->load('components.unit');
    }

    public function addUnitConversion(int $fromUnitId, int $toUnitId, array $data)
    {
        // Validate units exist and are not the same
        if ($fromUnitId === $toUnitId) {
            throw new \Exception('Cannot create conversion between the same unit');
        }

        // Check if conversion already exists
        $existingConversion = UnitConversion::where(function ($query) use ($fromUnitId, $toUnitId) {
            $query->where('from_unit_id', $fromUnitId)
                ->where('to_unit_id', $toUnitId);
        })->orWhere(function ($query) use ($fromUnitId, $toUnitId) {
            $query->where('from_unit_id', $toUnitId)
                ->where('to_unit_id', $fromUnitId);
        })->first();

        if ($existingConversion) {
            throw new \Exception('Conversion already exists between these units');
        }

        return UnitConversion::create([
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => $toUnitId,
            'multiplier' => $data['multiplier'],
            'offset' => $data['offset'] ?? 0,
            'is_bidirectional' => $data['is_bidirectional'] ?? true
        ]);
    }

    public function updateUnitConversion(int $conversionId, array $data)
    {
        $conversion = UnitConversion::findOrFail($conversionId);
        $conversion->update($data);
        return $conversion;
    }

    public function removeUnitConversion(int $conversionId): bool
    {
        return UnitConversion::findOrFail($conversionId)->delete();
    }

    public function getConversionPath(int $fromUnitId, int $toUnitId): array
    {
        // Implement Dijkstra's algorithm to find shortest conversion path
        $units = $this->model->pluck('id')->toArray();
        $conversions = UnitConversion::all();
        
        $graph = [];
        foreach ($units as $unit) {
            $graph[$unit] = [];
        }

        foreach ($conversions as $conversion) {
            $graph[$conversion->from_unit_id][$conversion->to_unit_id] = [
                'multiplier' => $conversion->multiplier,
                'offset' => $conversion->offset
            ];

            if ($conversion->is_bidirectional) {
                $graph[$conversion->to_unit_id][$conversion->from_unit_id] = [
                    'multiplier' => 1 / $conversion->multiplier,
                    'offset' => -$conversion->offset / $conversion->multiplier
                ];
            }
        }

        return $this->findShortestPath($graph, $fromUnitId, $toUnitId);
    }

    protected function findShortestPath(array $graph, int $start, int $end): array
    {
        $distances = [];
        $previous = [];
        $path = [];
        $queue = new \SplPriorityQueue();

        foreach (array_keys($graph) as $vertex) {
            $distances[$vertex] = INF;
            $previous[$vertex] = null;
        }

        $distances[$start] = 0;
        $queue->insert($start, 0);

        while (!$queue->isEmpty()) {
            $current = $queue->extract();

            if ($current === $end) {
                break;
            }

            foreach ($graph[$current] as $neighbor => $conversion) {
                $alt = $distances[$current] + 1; // Count steps, not actual distance

                if ($alt < $distances[$neighbor]) {
                    $distances[$neighbor] = $alt;
                    $previous[$neighbor] = $current;
                    $queue->insert($neighbor, -$alt);
                }
            }
        }

        // Build path
        $current = $end;
        while ($current !== null) {
            array_unshift($path, $current);
            $current = $previous[$current];
        }

        return $path;
    }

    public function validateConversion(int $fromUnitId, int $toUnitId): bool
    {
        $path = $this->getConversionPath($fromUnitId, $toUnitId);
        return count($path) > 1; // Path exists if more than just the start unit
    }

    public function getUnitComponents(int $compositeUnitId): Collection
    {
        return UnitComponent::where('composite_unit_id', $compositeUnitId)
            ->with('unit')
            ->orderBy('order')
            ->get();
    }

    public function setUnitComponents(int $compositeUnitId, array $components)
    {
        // Remove existing components
        UnitComponent::where('composite_unit_id', $compositeUnitId)->delete();

        // Add new components
        foreach ($components as $order => $component) {
            UnitComponent::create([
                'composite_unit_id' => $compositeUnitId,
                'unit_id' => $component['unit_id'],
                'quantity' => $component['quantity'] ?? 1,
                'operator' => $component['operator'] ?? '*',
                'order' => $order
            ]);
        }
    }

    /**
     * Calculate conversion factor between units
     */
    public function calculateConversionFactor(int $fromUnitId, int $toUnitId): array
    {
        $path = $this->getConversionPath($fromUnitId, $toUnitId);
        
        if (count($path) < 2) {
            throw new \Exception('No conversion path found between units');
        }

        $multiplier = 1;
        $offset = 0;

        for ($i = 0; $i < count($path) - 1; $i++) {
            $conversion = UnitConversion::where(function ($query) use ($path, $i) {
                $query->where('from_unit_id', $path[$i])
                    ->where('to_unit_id', $path[$i + 1]);
            })->orWhere(function ($query) use ($path, $i) {
                $query->where('from_unit_id', $path[$i + 1])
                    ->where('to_unit_id', $path[$i]);
            })->first();

            if ($conversion->from_unit_id === $path[$i]) {
                $multiplier *= $conversion->multiplier;
                $offset = $offset * $conversion->multiplier + $conversion->offset;
            } else {
                $multiplier /= $conversion->multiplier;
                $offset = ($offset - $conversion->offset) / $conversion->multiplier;
            }
        }

        return [
            'multiplier' => $multiplier,
            'offset' => $offset
        ];
    }
}
