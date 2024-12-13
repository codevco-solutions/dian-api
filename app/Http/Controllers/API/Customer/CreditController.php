<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CreditService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreditController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * Get customer credit information
     */
    public function getCustomerCredit($customerId)
    {
        try {
            $credit = $this->creditService->getCustomerCredit($customerId);

            return response()->json([
                'message' => 'Customer credit retrieved successfully',
                'data' => $credit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update credit limit
     */
    public function updateCreditLimit(Request $request, $customerId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $credit = $this->creditService->updateCreditLimit(
                $customerId,
                $request->amount,
                $request->only(['reason', 'notes'])
            );

            return response()->json([
                'message' => 'Credit limit updated successfully',
                'data' => $credit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Add credit movement
     */
    public function addCreditMovement(Request $request, $customerId)
    {
        $request->validate([
            'type' => 'required|in:charge,payment',
            'amount' => 'required|numeric|gt:0',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        try {
            $movement = $this->creditService->addCreditMovement($customerId, $request->all());

            return response()->json([
                'message' => 'Credit movement added successfully',
                'data' => $movement
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get credit movements
     */
    public function getCreditMovements(Request $request, $customerId)
    {
        $filters = $request->only([
            'type',
            'status',
            'date_from',
            'date_to',
            'reference_type'
        ]);

        $movements = $this->creditService->getCreditMovements($customerId, $filters);

        return response()->json([
            'message' => 'Credit movements retrieved successfully',
            'data' => $movements
        ]);
    }

    /**
     * Get credit status
     */
    public function getCreditStatus($customerId)
    {
        $status = $this->creditService->getCreditStatus($customerId);

        return response()->json([
            'message' => 'Credit status retrieved successfully',
            'data' => $status
        ]);
    }

    /**
     * Get overdue payments
     */
    public function getOverduePayments($customerId)
    {
        $payments = $this->creditService->getOverduePayments($customerId);

        return response()->json([
            'message' => 'Overdue payments retrieved successfully',
            'data' => $payments
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $movementId)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled,disputed',
            'payment_reference' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $movement = $this->creditService->updatePaymentStatus(
                $movementId,
                $request->status,
                $request->only(['payment_reference', 'notes'])
            );

            return response()->json([
                'message' => 'Payment status updated successfully',
                'data' => $movement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get credit metrics
     */
    public function getCreditMetrics($customerId)
    {
        $metrics = $this->creditService->getCreditMetrics($customerId);

        return response()->json([
            'message' => 'Credit metrics retrieved successfully',
            'data' => $metrics
        ]);
    }

    /**
     * Evaluate credit risk
     */
    public function evaluateCreditRisk($customerId)
    {
        $risk = $this->creditService->evaluateCreditRisk($customerId);

        return response()->json([
            'message' => 'Credit risk evaluated successfully',
            'data' => $risk
        ]);
    }

    /**
     * Generate credit report
     */
    public function generateCreditReport($customerId)
    {
        $report = $this->creditService->generateCreditReport($customerId);

        return response()->json([
            'message' => 'Credit report generated successfully',
            'data' => $report
        ]);
    }
}
