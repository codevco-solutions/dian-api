<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\MeasurementUnitService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeasurementUnitController extends Controller
{
    protected $unitService;

    public function __construct(MeasurementUnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * Get base units
     */
    public function getBaseUnits()
    {
        $units = $this->unitService->getBaseUnits();

        return response()->json([
            'message' => 'Base units retrieved successfully',
            'data' => $units
        ]);
    }

    /**
     * Get composite units
     */
    public function getCompositeUnits()
    {
        $units = $this->unitService->getCompositeUnits();

        return response()->json([
            'message' => 'Composite units retrieved successfully',
            'data' => $units
        ]);
    }

    /**
     * Create composite unit
     */
    public function createCompositeUnit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:50|unique:measurement_units,symbol',
            'description' => 'nullable|string',
            'components' => 'required|array|min:1',
            'components.*.unit_id' => 'required|exists:measurement_units,id',
            'components.*.quantity' => 'nullable|numeric',
            'components.*.operator' => 'nullable|in:*,/,+,-'
        ]);

        try {
            $unit = $this->unitService->createCompositeUnit($request->all());

            return response()->json([
                'message' => 'Composite unit created successfully',
                'data' => $unit
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update composite unit
     */
    public function updateCompositeUnit(Request $request, $unitId)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'symbol' => 'nullable|string|max:50|unique:measurement_units,symbol,' . $unitId,
            'description' => 'nullable|string',
            'components' => 'nullable|array|min:1',
            'components.*.unit_id' => 'required_with:components|exists:measurement_units,id',
            'components.*.quantity' => 'nullable|numeric',
            'components.*.operator' => 'nullable|in:*,/,+,-'
        ]);

        try {
            $unit = $this->unitService->updateCompositeUnit($unitId, $request->all());

            return response()->json([
                'message' => 'Composite unit updated successfully',
                'data' => $unit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Add unit conversion
     */
    public function addUnitConversion(Request $request)
    {
        $request->validate([
            'from_unit_id' => 'required|exists:measurement_units,id',
            'to_unit_id' => 'required|exists:measurement_units,id|different:from_unit_id',
            'multiplier' => 'required|numeric|gt:0',
            'offset' => 'nullable|numeric',
            'is_bidirectional' => 'nullable|boolean'
        ]);

        try {
            $conversion = $this->unitService->addUnitConversion(
                $request->from_unit_id,
                $request->to_unit_id,
                $request->all()
            );

            return response()->json([
                'message' => 'Unit conversion added successfully',
                'data' => $conversion
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update unit conversion
     */
    public function updateUnitConversion(Request $request, $conversionId)
    {
        $request->validate([
            'multiplier' => 'nullable|numeric|gt:0',
            'offset' => 'nullable|numeric',
            'is_bidirectional' => 'nullable|boolean'
        ]);

        try {
            $conversion = $this->unitService->updateUnitConversion($conversionId, $request->all());

            return response()->json([
                'message' => 'Unit conversion updated successfully',
                'data' => $conversion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove unit conversion
     */
    public function removeUnitConversion($conversionId)
    {
        try {
            $this->unitService->removeUnitConversion($conversionId);

            return response()->json([
                'message' => 'Unit conversion removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Convert value between units
     */
    public function convertValue(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric',
            'from_unit_id' => 'required|exists:measurement_units,id',
            'to_unit_id' => 'required|exists:measurement_units,id|different:from_unit_id'
        ]);

        try {
            $result = $this->unitService->convertValue(
                $request->value,
                $request->from_unit_id,
                $request->to_unit_id
            );

            return response()->json([
                'message' => 'Value converted successfully',
                'data' => [
                    'original_value' => $request->value,
                    'converted_value' => $result,
                    'from_unit_id' => $request->from_unit_id,
                    'to_unit_id' => $request->to_unit_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get unit conversions
     */
    public function getUnitConversions($unitId)
    {
        $conversions = $this->unitService->getUnitConversions($unitId);

        return response()->json([
            'message' => 'Unit conversions retrieved successfully',
            'data' => $conversions
        ]);
    }

    /**
     * Get unit components
     */
    public function getUnitComponents($unitId)
    {
        try {
            $components = $this->unitService->getUnitComponents($unitId);

            return response()->json([
                'message' => 'Unit components retrieved successfully',
                'data' => [
                    'components' => $components,
                    'formatted' => $this->unitService->formatCompositeUnit($unitId)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Calculate composite value
     */
    public function calculateCompositeValue(Request $request, $unitId)
    {
        $request->validate([
            'component_values' => 'required|array',
            'component_values.*' => 'required|numeric'
        ]);

        try {
            $result = $this->unitService->calculateCompositeValue($unitId, $request->component_values);

            return response()->json([
                'message' => 'Composite value calculated successfully',
                'data' => [
                    'component_values' => $request->component_values,
                    'result' => $result,
                    'formatted' => $this->unitService->formatCompositeUnit($unitId)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate conversion possibility
     */
    public function validateConversion(Request $request)
    {
        $request->validate([
            'from_unit_id' => 'required|exists:measurement_units,id',
            'to_unit_id' => 'required|exists:measurement_units,id|different:from_unit_id'
        ]);

        $isValid = $this->unitService->validateConversion(
            $request->from_unit_id,
            $request->to_unit_id
        );

        if ($isValid) {
            $path = $this->unitService->getConversionPath(
                $request->from_unit_id,
                $request->to_unit_id
            );
        }

        return response()->json([
            'message' => $isValid ? 'Conversion is possible' : 'Conversion is not possible',
            'data' => [
                'is_valid' => $isValid,
                'conversion_path' => $isValid ? $path : null
            ]
        ]);
    }
}
