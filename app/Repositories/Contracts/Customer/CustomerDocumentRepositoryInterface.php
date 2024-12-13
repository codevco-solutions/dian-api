<?php

namespace App\Repositories\Contracts\Customer;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface CustomerDocumentRepositoryInterface
{
    /**
     * Obtener documentos de un cliente
     */
    public function getCustomerDocuments(int $customerId): Collection;

    /**
     * Obtener un documento específico
     */
    public function getDocument(int $documentId);

    /**
     * Guardar nuevo documento
     */
    public function storeDocument(int $customerId, UploadedFile $file, array $data);

    /**
     * Actualizar documento
     */
    public function updateDocument(int $documentId, array $data);

    /**
     * Actualizar archivo de documento
     */
    public function updateDocumentFile(int $documentId, UploadedFile $file): bool;

    /**
     * Eliminar documento
     */
    public function deleteDocument(int $documentId): bool;

    /**
     * Obtener documentos por tipo
     */
    public function getDocumentsByType(int $customerId, string $type): Collection;

    /**
     * Verificar si existe documento
     */
    public function documentExists(int $customerId, string $type): bool;

    /**
     * Obtener documentos vencidos
     */
    public function getExpiredDocuments(int $customerId): Collection;

    /**
     * Obtener documentos próximos a vencer
     */
    public function getDocumentsAboutToExpire(int $customerId, int $daysThreshold = 30): Collection;

    /**
     * Obtener documentos requeridos faltantes
     */
    public function getMissingRequiredDocuments(int $customerId): Collection;

    /**
     * Verificar estado de documentación
     */
    public function checkDocumentationStatus(int $customerId): array;
}
