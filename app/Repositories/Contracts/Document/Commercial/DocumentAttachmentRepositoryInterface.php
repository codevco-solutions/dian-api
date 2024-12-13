<?php

namespace App\Repositories\Contracts\Document\Commercial;

use App\Models\Document\Commercial\DocumentAttachment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface DocumentAttachmentRepositoryInterface
{
    /**
     * Crear nuevo adjunto
     */
    public function create(string $documentType, int $documentId, UploadedFile $file, array $data = []): DocumentAttachment;

    /**
     * Obtener adjunto por ID
     */
    public function find(int $id): ?DocumentAttachment;

    /**
     * Eliminar adjunto
     */
    public function delete(DocumentAttachment $attachment): bool;

    /**
     * Obtener adjuntos de un documento
     */
    public function getDocumentAttachments(string $documentType, int $documentId): Collection;

    /**
     * Actualizar metadatos del adjunto
     */
    public function updateMetadata(DocumentAttachment $attachment, array $metadata): bool;

    /**
     * Obtener adjuntos por tipo de archivo
     */
    public function getAttachmentsByType(string $documentType, int $documentId, array $fileTypes): Collection;

    /**
     * Obtener adjuntos por usuario
     */
    public function getAttachmentsByUser(int $userId): Collection;

    /**
     * Obtener tamaño total de adjuntos de un documento
     */
    public function getTotalSize(string $documentType, int $documentId): int;

    /**
     * Verificar si un documento tiene adjuntos
     */
    public function hasAttachments(string $documentType, int $documentId): bool;
}
