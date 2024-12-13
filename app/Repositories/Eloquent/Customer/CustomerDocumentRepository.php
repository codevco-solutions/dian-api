<?php

namespace App\Repositories\Eloquent\Customer;

use App\Models\Customer\CustomerDocument;
use App\Repositories\Contracts\Customer\CustomerDocumentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CustomerDocumentRepository implements CustomerDocumentRepositoryInterface
{
    protected $model;

    public function __construct(CustomerDocument $model)
    {
        $this->model = $model;
    }

    public function getCustomerDocuments(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->orderBy('document_type')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getDocument(int $documentId)
    {
        return $this->model->findOrFail($documentId);
    }

    public function storeDocument(int $customerId, UploadedFile $file, array $data)
    {
        // Validar tipo de documento
        if (!in_array($data['document_type'], CustomerDocument::$allowedTypes)) {
            throw new \Exception('Tipo de documento no permitido');
        }

        // Validar tipo MIME
        if (!in_array($file->getMimeType(), CustomerDocument::$allowedMimeTypes)) {
            throw new \Exception('Tipo de archivo no permitido');
        }

        // Generar nombre único para el archivo
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        
        // Definir ruta de almacenamiento
        $filePath = "customers/{$customerId}/documents/{$data['document_type']}/{$fileName}";
        
        // Almacenar archivo
        Storage::put($filePath, file_get_contents($file));

        // Crear registro en base de datos
        return $this->model->create([
            'customer_id' => $customerId,
            'document_type' => $data['document_type'],
            'name' => $data['name'] ?? $file->getClientOriginalName(),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'expiration_date' => $data['expiration_date'] ?? null,
            'status' => $data['status'] ?? 'active',
            'is_required' => $data['is_required'] ?? false,
            'metadata' => $data['metadata'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
    }

    public function updateDocument(int $documentId, array $data)
    {
        $document = $this->getDocument($documentId);
        $document->update($data);
        return $document->fresh();
    }

    public function updateDocumentFile(int $documentId, UploadedFile $file): bool
    {
        $document = $this->getDocument($documentId);

        // Validar tipo MIME
        if (!in_array($file->getMimeType(), CustomerDocument::$allowedMimeTypes)) {
            throw new \Exception('Tipo de archivo no permitido');
        }

        // Eliminar archivo anterior
        if ($document->file_path) {
            Storage::delete($document->file_path);
        }

        // Generar nuevo nombre de archivo
        $fileName = uniqid() . '_' . $file->getClientOriginalName();
        $filePath = "customers/{$document->customer_id}/documents/{$document->document_type}/{$fileName}";

        // Almacenar nuevo archivo
        Storage::put($filePath, file_get_contents($file));

        // Actualizar registro
        return $document->update([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);
    }

    public function deleteDocument(int $documentId): bool
    {
        $document = $this->getDocument($documentId);

        // Eliminar archivo físico
        if ($document->file_path) {
            Storage::delete($document->file_path);
        }

        return $document->delete();
    }

    public function getDocumentsByType(int $customerId, string $type): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->where('document_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function documentExists(int $customerId, string $type): bool
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->where('document_type', $type)
            ->where('status', 'active')
            ->exists();
    }

    public function getExpiredDocuments(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', Carbon::now())
            ->get();
    }

    public function getDocumentsAboutToExpire(int $customerId, int $daysThreshold = 30): Collection
    {
        $thresholdDate = Carbon::now()->addDays($daysThreshold);

        return $this->model
            ->where('customer_id', $customerId)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $thresholdDate)
            ->where('expiration_date', '>', Carbon::now())
            ->get();
    }

    public function getMissingRequiredDocuments(int $customerId): Collection
    {
        $existingDocuments = $this->model
            ->where('customer_id', $customerId)
            ->where('status', 'active')
            ->pluck('document_type')
            ->toArray();

        return collect(CustomerDocument::$allowedTypes)
            ->filter(function ($type) use ($existingDocuments) {
                return !in_array($type, $existingDocuments);
            })
            ->values();
    }

    public function checkDocumentationStatus(int $customerId): array
    {
        $documents = $this->getCustomerDocuments($customerId);
        $expired = $this->getExpiredDocuments($customerId);
        $aboutToExpire = $this->getDocumentsAboutToExpire($customerId);
        $missingRequired = $this->getMissingRequiredDocuments($customerId);

        return [
            'total_documents' => $documents->count(),
            'active_documents' => $documents->where('status', 'active')->count(),
            'expired_documents' => $expired->count(),
            'about_to_expire' => $aboutToExpire->count(),
            'missing_required' => $missingRequired->count(),
            'documentation_complete' => $missingRequired->isEmpty(),
            'expired_list' => $expired,
            'about_to_expire_list' => $aboutToExpire,
            'missing_required_list' => $missingRequired,
            'last_update' => Carbon::now()
        ];
    }
}
