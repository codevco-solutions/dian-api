<?php

namespace App\Services\Customer;

use App\Repositories\Contracts\Customer\CustomerDocumentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CustomerDocumentService
{
    protected $documentRepository;

    public function __construct(CustomerDocumentRepositoryInterface $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Obtener documentos de cliente
     */
    public function getCustomerDocuments(int $customerId): Collection
    {
        return $this->documentRepository->getCustomerDocuments($customerId);
    }

    /**
     * Guardar nuevo documento
     */
    public function storeDocument(int $customerId, UploadedFile $file, array $data)
    {
        $this->validateDocumentData($data);
        
        // Verificar si ya existe un documento activo del mismo tipo
        if ($this->documentRepository->documentExists($customerId, $data['document_type'])) {
            throw new \Exception('Ya existe un documento activo de este tipo');
        }

        return $this->documentRepository->storeDocument($customerId, $file, $data);
    }

    /**
     * Actualizar documento
     */
    public function updateDocument(int $documentId, array $data)
    {
        $this->validateDocumentData($data, true);
        return $this->documentRepository->updateDocument($documentId, $data);
    }

    /**
     * Actualizar archivo de documento
     */
    public function updateDocumentFile(int $documentId, UploadedFile $file): bool
    {
        return $this->documentRepository->updateDocumentFile($documentId, $file);
    }

    /**
     * Eliminar documento
     */
    public function deleteDocument(int $documentId): bool
    {
        $document = $this->documentRepository->getDocument($documentId);
        
        // Verificar si es un documento requerido
        if ($document->is_required) {
            throw new \Exception('No se puede eliminar un documento requerido');
        }

        return $this->documentRepository->deleteDocument($documentId);
    }

    /**
     * Obtener documentos por tipo
     */
    public function getDocumentsByType(int $customerId, string $type): Collection
    {
        if (!in_array($type, \App\Models\Customer\CustomerDocument::$allowedTypes)) {
            throw new \Exception('Tipo de documento no válido');
        }

        return $this->documentRepository->getDocumentsByType($customerId, $type);
    }

    /**
     * Obtener documentos vencidos
     */
    public function getExpiredDocuments(int $customerId): Collection
    {
        return $this->documentRepository->getExpiredDocuments($customerId);
    }

    /**
     * Obtener documentos próximos a vencer
     */
    public function getDocumentsAboutToExpire(int $customerId, int $daysThreshold = 30): Collection
    {
        return $this->documentRepository->getDocumentsAboutToExpire($customerId, $daysThreshold);
    }

    /**
     * Obtener documentos requeridos faltantes
     */
    public function getMissingRequiredDocuments(int $customerId): Collection
    {
        return $this->documentRepository->getMissingRequiredDocuments($customerId);
    }

    /**
     * Verificar estado de documentación
     */
    public function checkDocumentationStatus(int $customerId): array
    {
        return $this->documentRepository->checkDocumentationStatus($customerId);
    }

    /**
     * Generar reporte de documentación
     */
    public function generateDocumentationReport(int $customerId): array
    {
        $status = $this->checkDocumentationStatus($customerId);
        $documents = $this->getCustomerDocuments($customerId);

        $documentsByType = $documents->groupBy('document_type')->map(function ($group) {
            return [
                'total' => $group->count(),
                'active' => $group->where('status', 'active')->count(),
                'expired' => $group->filter(function ($doc) {
                    return $doc->isExpired();
                })->count(),
                'about_to_expire' => $group->filter(function ($doc) {
                    return $doc->isAboutToExpire();
                })->count(),
                'last_updated' => $group->max('updated_at')
            ];
        });

        return [
            'report_date' => Carbon::now(),
            'status' => $status,
            'documents_by_type' => $documentsByType,
            'compliance_score' => $this->calculateComplianceScore($status),
            'recommendations' => $this->generateRecommendations($status)
        ];
    }

    /**
     * Validar datos de documento
     */
    protected function validateDocumentData(array $data, bool $isUpdate = false): void
    {
        if (!$isUpdate) {
            if (!isset($data['document_type'])) {
                throw new \Exception('El tipo de documento es requerido');
            }

            if (!in_array($data['document_type'], \App\Models\Customer\CustomerDocument::$allowedTypes)) {
                throw new \Exception('Tipo de documento no válido');
            }
        }

        if (isset($data['expiration_date'])) {
            $expirationDate = Carbon::parse($data['expiration_date']);
            if ($expirationDate->isPast()) {
                throw new \Exception('La fecha de vencimiento no puede ser en el pasado');
            }
        }

        if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive', 'expired'])) {
            throw new \Exception('Estado no válido');
        }
    }

    /**
     * Calcular puntuación de cumplimiento
     */
    protected function calculateComplianceScore(array $status): float
    {
        $score = 100;

        // Penalización por documentos faltantes
        if ($status['missing_required'] > 0) {
            $score -= ($status['missing_required'] * 20);
        }

        // Penalización por documentos vencidos
        if ($status['expired_documents'] > 0) {
            $score -= ($status['expired_documents'] * 15);
        }

        // Penalización por documentos próximos a vencer
        if ($status['about_to_expire'] > 0) {
            $score -= ($status['about_to_expire'] * 5);
        }

        return max(0, $score);
    }

    /**
     * Generar recomendaciones
     */
    protected function generateRecommendations(array $status): array
    {
        $recommendations = [];

        if ($status['missing_required'] > 0) {
            $recommendations[] = 'Cargar los documentos requeridos faltantes';
        }

        if ($status['expired_documents'] > 0) {
            $recommendations[] = 'Actualizar los documentos vencidos';
        }

        if ($status['about_to_expire'] > 0) {
            $recommendations[] = 'Preparar la renovación de documentos próximos a vencer';
        }

        return $recommendations;
    }
}
