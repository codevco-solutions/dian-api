<?php

namespace App\Services\Document\Commercial;

use App\Models\Document\Commercial\DocumentAttachment;
use App\Repositories\Contracts\Document\Commercial\DocumentAttachmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class DocumentAttachmentService
{
    protected $attachmentRepository;

    public function __construct(DocumentAttachmentRepositoryInterface $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Subir nuevo adjunto
     */
    public function uploadAttachment(string $documentType, int $documentId, UploadedFile $file, array $data = []): DocumentAttachment
    {
        $validator = Validator::make([
            'file' => $file,
            'data' => $data
        ], [
            'file' => 'required|file|max:10240', // 10MB máximo
            'data.name' => 'nullable|string|max:255',
            'data.description' => 'nullable|string',
            'data.metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->attachmentRepository->create($documentType, $documentId, $file, $data);
    }

    /**
     * Eliminar adjunto
     */
    public function deleteAttachment(int $id): bool
    {
        $attachment = $this->attachmentRepository->find($id);
        if (!$attachment) {
            throw new InvalidArgumentException("Adjunto no encontrado");
        }

        return $this->attachmentRepository->delete($attachment);
    }

    /**
     * Obtener adjuntos de un documento
     */
    public function getDocumentAttachments(string $documentType, int $documentId): Collection
    {
        return $this->attachmentRepository->getDocumentAttachments($documentType, $documentId);
    }

    /**
     * Actualizar metadatos del adjunto
     */
    public function updateMetadata(int $id, array $metadata): bool
    {
        $attachment = $this->attachmentRepository->find($id);
        if (!$attachment) {
            throw new InvalidArgumentException("Adjunto no encontrado");
        }

        $validator = Validator::make(['metadata' => $metadata], [
            'metadata' => 'required|array'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->attachmentRepository->updateMetadata($attachment, $metadata);
    }

    /**
     * Obtener adjuntos por tipo de archivo
     */
    public function getAttachmentsByType(string $documentType, int $documentId, array $fileTypes): Collection
    {
        return $this->attachmentRepository->getAttachmentsByType($documentType, $documentId, $fileTypes);
    }

    /**
     * Obtener adjuntos por usuario
     */
    public function getAttachmentsByUser(int $userId): Collection
    {
        return $this->attachmentRepository->getAttachmentsByUser($userId);
    }

    /**
     * Verificar límite de tamaño total de adjuntos
     */
    public function checkSizeLimit(string $documentType, int $documentId, int $newFileSize): bool
    {
        $currentSize = $this->attachmentRepository->getTotalSize($documentType, $documentId);
        $maxSize = config('documents.attachments.max_total_size', 52428800); // 50MB por defecto

        return ($currentSize + $newFileSize) <= $maxSize;
    }

    /**
     * Verificar tipo de archivo permitido
     */
    public function isAllowedFileType(string $mimeType): bool
    {
        $allowedTypes = config('documents.attachments.allowed_types', [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);

        return in_array($mimeType, $allowedTypes);
    }
}
