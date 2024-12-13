<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerClassificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerClassificationController extends Controller
{
    protected $classificationService;

    public function __construct(CustomerClassificationService $classificationService)
    {
        $this->classificationService = $classificationService;
    }

    /**
     * Listar todas las clasificaciones
     */
    public function index()
    {
        $classifications = $this->classificationService->getAllClassifications();

        return response()->json([
            'message' => 'Clasificaciones obtenidas exitosamente',
            'data' => $classifications
        ]);
    }

    /**
     * Crear nueva clasificación
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'min_purchase_frequency' => 'nullable|integer|min:0',
            'payment_behavior_score' => 'nullable|numeric|min:0|max:100',
            'credit_score' => 'nullable|numeric|min:0|max:100',
            'criteria' => 'nullable|array',
            'status' => 'nullable|string|in:active,inactive',
            'color' => 'nullable|string',
            'icon' => 'nullable|string'
        ]);

        try {
            $classification = $this->classificationService->createClassification($request->all());

            return response()->json([
                'message' => 'Clasificación creada exitosamente',
                'data' => $classification
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Actualizar clasificación
     */
    public function update(Request $request, $classificationId)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'min_purchase_frequency' => 'nullable|integer|min:0',
            'payment_behavior_score' => 'nullable|numeric|min:0|max:100',
            'credit_score' => 'nullable|numeric|min:0|max:100',
            'criteria' => 'nullable|array',
            'status' => 'nullable|string|in:active,inactive',
            'color' => 'nullable|string',
            'icon' => 'nullable|string'
        ]);

        try {
            $classification = $this->classificationService->updateClassification(
                $classificationId,
                $request->all()
            );

            return response()->json([
                'message' => 'Clasificación actualizada exitosamente',
                'data' => $classification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Eliminar clasificación
     */
    public function destroy($classificationId)
    {
        try {
            $this->classificationService->deleteClassification($classificationId);

            return response()->json([
                'message' => 'Clasificación eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Asignar clasificación a cliente
     */
    public function assignToCustomer(Request $request, $customerId)
    {
        $request->validate([
            'classification_id' => 'required|integer|exists:customer_classifications,id'
        ]);

        try {
            $this->classificationService->assignClassificationToCustomer(
                $customerId,
                $request->classification_id
            );

            return response()->json([
                'message' => 'Clasificación asignada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Evaluar clasificación de cliente
     */
    public function evaluateCustomer($customerId)
    {
        try {
            $evaluation = $this->classificationService->evaluateCustomerClassification($customerId);

            return response()->json([
                'message' => 'Evaluación de clasificación realizada exitosamente',
                'data' => $evaluation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Obtener métricas de clasificación
     */
    public function getMetrics($classificationId)
    {
        try {
            $metrics = $this->classificationService->getClassificationMetrics($classificationId);

            return response()->json([
                'message' => 'Métricas obtenidas exitosamente',
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Evaluar clasificaciones de todos los clientes
     */
    public function evaluateAllCustomers()
    {
        try {
            $results = $this->classificationService->evaluateAllCustomersClassification();

            return response()->json([
                'message' => 'Evaluación masiva realizada exitosamente',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
