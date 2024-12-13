<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerDocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerDocumentController extends Controller
{
    protected $documentService;

    public function __construct(CustomerDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Obtener documentos de cliente
     */
    public function index($customerId)
    {
        $documents = $this->documentService->getCustomerDocuments($customerId);

        return response()->json([
            'message' => 'Documentos obtenidos exitosamente',
            'data' => $documents
        ]);
    }

    /**
     * Guardar nuevo documento
     */
    public function store(Request $request, $customerId)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // máximo 10MB
            'document_type' => 'required|string',
            'name' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date',
            'is_required' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        try {
            $document = $this->documentService->storeDocument(
                $customerId,
                $request->file('document'),
                $request->except('document')
            );

            return response()->json([
                'message' => 'Documento guardado exitosamente',
                'data' => $document
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Actualizar documento
     */
    public function update(Request $request, $customerId, $documentId)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,inactive,expired',
            'is_required' => 'nullable|boolean',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        try {
            $document = $this->documentService->updateDocument($documentId, $request->all());

            return response()->json([
                'message' => 'Documento actualizado exitosamente',
                'data' => $document
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Actualizar archivo de documento
     */
    public function updateFile(Request $request, $customerId, $documentId)
    {
        $request->validate([
            'document' => 'required|file|max:10240' // máximo 10MB
        ]);

        try {
            $this->documentService->updateDocumentFile($documentId, $request->file('document'));

            return response()->json([
                'message' => 'Archivo actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Eliminar documento
     */
    public function destroy($customerId, $documentId)
    {
        try {
            $this->documentService->deleteDocument($documentId);

            return response()->json([
                'message' => 'Documento eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Obtener documentos por tipo
     */
    public function getByType($customerId, $type)
    {
        try {
            $documents = $this->documentService->getDocumentsByType($customerId, $type);

            return response()->json([
                'message' => 'Documentos obtenidos exitosamente',
                'data' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Obtener documentos vencidos
     */
    public function getExpired($customerId)
    {
        $documents = $this->documentService->getExpiredDocuments($customerId);

        return response()->json([
            'message' => 'Documentos vencidos obtenidos exitosamente',
            'data' => $documents
        ]);
    }

    /**
     * Obtener documentos próximos a vencer
     */
    public function getAboutToExpire(Request $request, $customerId)
    {
        $daysThreshold = $request->get('days', 30);
        
        $documents = $this->documentService->getDocumentsAboutToExpire($customerId, $daysThreshold);

        return response()->json([
            'message' => 'Documentos próximos a vencer obtenidos exitosamente',
            'data' => $documents
        ]);
    }

    /**
     * Obtener documentos requeridos faltantes
     */
    public function getMissingRequired($customerId)
    {
        $documents = $this->documentService->getMissingRequiredDocuments($customerId);

        return response()->json([
            'message' => 'Documentos requeridos faltantes obtenidos exitosamente',
            'data' => $documents
        ]);
    }

    /**
     * Verificar estado de documentación
     */
    public function checkStatus($customerId)
    {
        $status = $this->documentService->checkDocumentationStatus($customerId);

        return response()->json([
            'message' => 'Estado de documentación obtenido exitosamente',
            'data' => $status
        ]);
    }

    /**
     * Generar reporte de documentación
     */
    public function generateReport($customerId)
    {
        $report = $this->documentService->generateDocumentationReport($customerId);

        return response()->json([
            'message' => 'Reporte generado exitosamente',
            'data' => $report
        ]);
    }
}
