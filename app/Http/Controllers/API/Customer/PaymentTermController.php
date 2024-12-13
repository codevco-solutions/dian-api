<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\PaymentTermService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentTermController extends Controller
{
    protected $paymentTermService;

    public function __construct(PaymentTermService $paymentTermService)
    {
        $this->paymentTermService = $paymentTermService;
    }

    /**
     * Obtener términos de pago de un cliente
     */
    public function index($customerId)
    {
        $terms = $this->paymentTermService->getCustomerPaymentTerms($customerId);

        return response()->json([
            'message' => 'Términos de pago obtenidos exitosamente',
            'data' => $terms
        ]);
    }

    /**
     * Crear nuevo término de pago
     */
    public function store(Request $request, $customerId)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'days' => 'required|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_days' => 'nullable|integer|min:0',
            'is_default' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        try {
            $term = $this->paymentTermService->createPaymentTerm($customerId, $request->all());

            return response()->json([
                'message' => 'Término de pago creado exitosamente',
                'data' => $term
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Actualizar término de pago
     */
    public function update(Request $request, $customerId, $termId)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'days' => 'nullable|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_days' => 'nullable|integer|min:0',
            'is_default' => 'nullable|boolean',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        try {
            $term = $this->paymentTermService->updatePaymentTerm($customerId, $termId, $request->all());

            return response()->json([
                'message' => 'Término de pago actualizado exitosamente',
                'data' => $term
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Eliminar término de pago
     */
    public function destroy($customerId, $termId)
    {
        try {
            $this->paymentTermService->deletePaymentTerm($customerId, $termId);

            return response()->json([
                'message' => 'Término de pago eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Establecer término de pago por defecto
     */
    public function setDefault($customerId, $termId)
    {
        try {
            $this->paymentTermService->setDefaultPaymentTerm($customerId, $termId);

            return response()->json([
                'message' => 'Término de pago establecido como predeterminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Calcular descuento por pronto pago
     */
    public function calculateDiscount(Request $request, $customerId, $termId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'nullable|date'
        ]);

        try {
            $calculation = $this->paymentTermService->calculateEarlyPaymentDiscount(
                $termId,
                $request->amount,
                $request->payment_date
            );

            return response()->json([
                'message' => 'Cálculo de descuento realizado exitosamente',
                'data' => $calculation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
