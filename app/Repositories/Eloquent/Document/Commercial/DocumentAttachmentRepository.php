<?php

namespace App\Repositories\Eloquent\Document\Commercial;

use App\Models\Document\Commercial\DocumentAttachment;
use App\Repositories\Contracts\Document\Commercial\DocumentAttachmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentAttachmentRepository implements DocumentAttachmentRepositoryInterface
{
    public function create(string $documentType, int $documentId, UploadedFile $file, array $data = []): DocumentAttachment
    {
        $path = $file->store("documents/{$documentType}/{$documentId}/attachments");

        return DocumentAttachment::create([
            'document_type' => $documentType,
            'document_id' => $documentId,
            'name' => $data['name'] ?? $file->getClientOriginalName(),
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'metadata' => $data['metadata'] ?? null
        ]);
    }

    public function find(int $id): ?DocumentAttachment
    {
        return DocumentAttachment::find($id);
    }

    public function delete(DocumentAttachment $attachment): bool
    {
        if (Storage::exists($attachment->file_path)) {
            Storage::delete($attachment->file_path);
        }

        return $attachment->delete();
    }

    public function getDocumentAttachments(string $documentType, int $documentId): Collection
    {
        return DocumentAttachment::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateMetadata(DocumentAttachment $attachment, array $metadata): bool
    {
        return $attachment->update(['metadata' => $metadata]);
    }

    public function getAttachmentsByType(string $documentType, int $documentId, array $fileTypes): Collection
    {
        return DocumentAttachment::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->whereIn('file_type', $fileTypes)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAttachmentsByUser(int $userId): Collection
    {
        return DocumentAttachment::where('uploaded_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTotalSize(string $documentType, int $documentId): int
    {
        return DocumentAttachment::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->sum('file_size');
    }

    public function hasAttachments(string $documentType, int $documentId): bool
    {
        return DocumentAttachment::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->exists();
    }
}
