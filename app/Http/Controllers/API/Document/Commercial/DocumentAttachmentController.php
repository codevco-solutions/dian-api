<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Services\Document\Commercial\DocumentAttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentAttachmentController extends Controller
{
    protected $attachmentService;

    public function __construct(DocumentAttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Subir nuevo adjunto
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['error' => 'No se ha proporcionado ningún archivo'], 422);
            }

            $file = $request->file('file');

            // Verificar tipo de archivo permitido
            if (!$this->attachmentService->isAllowedFileType($file->getMimeType())) {
                return response()->json(['error' => 'Tipo de archivo no permitido'], 422);
            }

            // Verificar límite de tamaño
            if (!$this->attachmentService->checkSizeLimit(
                $request->input('document_type'),
                $request->input('document_id'),
                $file->getSize()
            )) {
                return response()->json(['error' => 'Se ha excedido el límite de tamaño total'], 422);
            }

            $attachment = $this->attachmentService->uploadAttachment(
                $request->input('document_type'),
                $request->input('document_id'),
                $file,
                $request->except(['file', 'document_type', 'document_id'])
            );

            return response()->json($attachment, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Eliminar adjunto
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->attachmentService->deleteAttachment($id);
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Obtener adjuntos de un documento
     */
    public function getDocumentAttachments(string $documentType, int $documentId): JsonResponse
    {
        $attachments = $this->attachmentService->getDocumentAttachments($documentType, $documentId);
        return response()->json($attachments);
    }

    /**
     * Actualizar metadatos del adjunto
     */
    public function updateMetadata(Request $request, int $id): JsonResponse
    {
        try {
            $success = $this->attachmentService->updateMetadata($id, $request->input('metadata', []));
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Obtener adjuntos por tipo de archivo
     */
    public function getByFileType(Request $request, string $documentType, int $documentId): JsonResponse
    {
        $fileTypes = $request->input('file_types', []);
        $attachments = $this->attachmentService->getAttachmentsByType($documentType, $documentId, $fileTypes);
        return response()->json($attachments);
    }

    /**
     * Obtener adjuntos por usuario
     */
    public function getByUser(int $userId): JsonResponse
    {
        $attachments = $this->attachmentService->getAttachmentsByUser($userId);
        return response()->json($attachments);
    }
}
