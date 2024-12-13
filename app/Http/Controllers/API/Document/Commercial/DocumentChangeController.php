<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Services\Document\Commercial\DocumentChangeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentChangeController extends Controller
{
    protected $changeService;

    public function __construct(DocumentChangeService $changeService)
    {
        $this->changeService = $changeService;
    }

    /**
     * Obtener historial de cambios de un documento
     */
    public function getHistory(string $documentType, int $documentId): JsonResponse
    {
        $history = $this->changeService->getHistory($documentType, $documentId);
        return response()->json($history);
    }

    /**
     * Obtener detalles de un cambio específico
     */
    public function getChangeDetails(int $changeId): JsonResponse
    {
        $details = $this->changeService->getChangeDetails($changeId);
        return response()->json($details);
    }

    /**
     * Obtener resumen de cambios por período
     */
    public function getChangeSummary(Request $request, string $documentType, int $documentId): JsonResponse
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $summary = $this->changeService->getChangeSummary(
            $documentType,
            $documentId,
            $request->start_date,
            $request->end_date
        );

        return response()->json($summary);
    }
}
