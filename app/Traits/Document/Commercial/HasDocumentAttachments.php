<?php

namespace App\Traits\Document\Commercial;

use App\Models\Document\Commercial\DocumentAttachment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasDocumentAttachments
{
    /**
     * Obtener los adjuntos del documento
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class, 'document_id')
            ->where('document_type', $this->getDocumentType());
    }

    /**
     * Obtener el tipo de documento para los adjuntos
     */
    abstract public function getDocumentType(): string;

    /**
     * Agregar un archivo adjunto
     */
    public function addAttachment(UploadedFile $file, array $data = []): DocumentAttachment
    {
        $path = $file->store("documents/{$this->getDocumentType()}/{$this->id}/attachments");

        return $this->attachments()->create([
            'document_type' => $this->getDocumentType(),
            'name' => $data['name'] ?? $file->getClientOriginalName(),
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'metadata' => $data['metadata'] ?? null
        ]);
    }

    /**
     * Eliminar un archivo adjunto
     */
    public function removeAttachment(DocumentAttachment $attachment): bool
    {
        if ($attachment->document_id !== $this->id || 
            $attachment->document_type !== $this->getDocumentType()) {
            return false;
        }

        if (Storage::exists($attachment->file_path)) {
            Storage::delete($attachment->file_path);
        }

        return $attachment->delete();
    }

    /**
     * Obtener adjuntos por tipo de archivo
     */
    public function getAttachmentsByType(array $fileTypes): HasMany
    {
        return $this->attachments()
            ->whereIn('file_type', $fileTypes);
    }

    /**
     * Obtener tamaño total de adjuntos
     */
    public function getTotalAttachmentsSize(): int
    {
        return $this->attachments()->sum('file_size');
    }

    /**
     * Verificar si el documento tiene adjuntos
     */
    public function hasAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    /**
     * Verificar si se puede agregar un nuevo adjunto (límite de tamaño)
     */
    public function canAddAttachment(int $fileSize): bool
    {
        $maxSize = config('documents.attachments.max_total_size', 52428800); // 50MB por defecto
        $currentSize = $this->getTotalAttachmentsSize();

        return ($currentSize + $fileSize) <= $maxSize;
    }

    /**
     * Limpiar todos los adjuntos
     */
    public function clearAttachments(): void
    {
        $this->attachments->each(function ($attachment) {
            $this->removeAttachment($attachment);
        });
    }
}
