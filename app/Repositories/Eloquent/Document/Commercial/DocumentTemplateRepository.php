<?php

namespace App\Repositories\Eloquent\Document\Commercial;

use App\Models\Document\Commercial\DocumentTemplate;
use App\Repositories\Contracts\Document\Commercial\DocumentTemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DocumentTemplateRepository implements DocumentTemplateRepositoryInterface
{
    public function all(): Collection
    {
        return DocumentTemplate::orderBy('name')->get();
    }

    public function find(int $id): ?DocumentTemplate
    {
        return DocumentTemplate::find($id);
    }

    public function create(array $data): DocumentTemplate
    {
        return DocumentTemplate::create($data);
    }

    public function update(DocumentTemplate $template, array $data): bool
    {
        return $template->update($data);
    }

    public function delete(DocumentTemplate $template): bool
    {
        return $template->delete();
    }

    public function getTemplatesByType(string $documentType): Collection
    {
        return DocumentTemplate::where('document_type', $documentType)
            ->orderBy('name')
            ->get();
    }

    public function getDefaultTemplate(string $documentType): ?DocumentTemplate
    {
        return DocumentTemplate::where('document_type', $documentType)
            ->where('is_default', true)
            ->first();
    }

    public function setDefaultTemplate(DocumentTemplate $template): bool
    {
        return DB::transaction(function () use ($template) {
            // Remover el estado por defecto de otras plantillas del mismo tipo
            DocumentTemplate::where('document_type', $template->document_type)
                ->where('id', '!=', $template->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);

            // Establecer esta plantilla como predeterminada
            return $template->update(['is_default' => true]);
        });
    }

    public function duplicate(DocumentTemplate $template): DocumentTemplate
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = "{$template->name} (copia)";
        $newTemplate->is_default = false;
        $newTemplate->save();

        return $newTemplate;
    }

    public function getActiveTemplates(): Collection
    {
        return DocumentTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function toggleActive(DocumentTemplate $template, bool $active = true): bool
    {
        return $template->update(['is_active' => $active]);
    }
}
