<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\TransactionHistoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionHistoryController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionHistoryService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Obtener transacciones de cliente
     */
    public function index(Request $request, $customerId)
    {
        $filters = $request->only([
            'type',
            'status',
            'date_from',
            'date_to',
            'reference_type'
        ]);

        $transactions = $this->transactionService->getCustomerTransactions($customerId, $filters);

        return response()->json([
            'message' => 'Transacciones obtenidas exitosamente',
            'data' => $transactions
        ]);
    }

    /**
     * Registrar nueva transacción
     */
    public function store(Request $request, $customerId)
    {
        $request->validate([
            'transaction_type' => 'required|string|in:charge,payment,adjustment',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'amount' => 'required|numeric|gt:0',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,paid,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            $transaction = $this->transactionService->recordTransaction($customerId, $request->all());

            return response()->json([
                'message' => 'Transacción registrada exitosamente',
                'data' => $transaction
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Actualizar estado de transacción
     */
    public function updateStatus(Request $request, $transactionId)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,cancelled,overdue,disputed',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        try {
            $transaction = $this->transactionService->updateTransactionStatus(
                $transactionId,
                $request->status,
                $request->only(['payment_date', 'notes'])
            );

            return response()->json([
                'message' => 'Estado de transacción actualizado exitosamente',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Obtener resumen de transacciones
     */
    public function getSummary(Request $request, $customerId)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        $summary = $this->transactionService->getTransactionsSummary($customerId, $filters);

        return response()->json([
            'message' => 'Resumen obtenido exitosamente',
            'data' => $summary
        ]);
    }

    /**
     * Obtener transacciones vencidas
     */
    public function getOverdue($customerId)
    {
        $overdue = $this->transactionService->getOverdueTransactions($customerId);

        return response()->json([
            'message' => 'Transacciones vencidas obtenidas exitosamente',
            'data' => $overdue
        ]);
    }

    /**
     * Obtener métricas de transacciones
     */
    public function getMetrics(Request $request, $customerId)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        $metrics = $this->transactionService->getTransactionMetrics($customerId, $filters);

        return response()->json([
            'message' => 'Métricas obtenidas exitosamente',
            'data' => $metrics
        ]);
    }

    /**
     * Obtener balance actual
     */
    public function getCurrentBalance($customerId)
    {
        $balance = $this->transactionService->getCurrentBalance($customerId);

        return response()->json([
            'message' => 'Balance obtenido exitosamente',
            'data' => ['balance' => $balance]
        ]);
    }

    /**
     * Generar reporte de transacciones
     */
    public function generateReport(Request $request, $customerId)
    {
        $filters = $request->only(['date_from', 'date_to', 'type', 'status']);
        
        $report = $this->transactionService->generateTransactionReport($customerId, $filters);

        return response()->json([
            'message' => 'Reporte generado exitosamente',
            'data' => $report
        ]);
    }
}
