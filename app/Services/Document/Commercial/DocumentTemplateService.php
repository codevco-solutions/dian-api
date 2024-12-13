<?php

namespace App\Services\Document\Commercial;

use App\Models\Document\Commercial\DocumentTemplate;
use App\Repositories\Contracts\Document\Commercial\DocumentTemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class DocumentTemplateService
{
    protected $templateRepository;

    public function __construct(DocumentTemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * Obtener todas las plantillas
     */
    public function getAllTemplates(): Collection
    {
        return $this->templateRepository->all();
    }

    /**
     * Crear nueva plantilla
     */
    public function createTemplate(array $data): DocumentTemplate
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|string|max:50',
            'content' => 'required|string',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'required|string',
            'default_values' => 'nullable|array',
            'validation_rules' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->templateRepository->create($data);
    }

    /**
     * Actualizar plantilla
     */
    public function updateTemplate(int $id, array $data): bool
    {
        $template = $this->templateRepository->find($id);
        if (!$template) {
            throw new InvalidArgumentException("Plantilla no encontrada");
        }

        $validator = Validator::make($data, [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'content' => 'string',
            'fields' => 'array',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'required|string',
            'default_values' => 'nullable|array',
            'validation_rules' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->templateRepository->update($template, $data);
    }

    /**
     * Eliminar plantilla
     */
    public function deleteTemplate(int $id): bool
    {
        $template = $this->templateRepository->find($id);
        if (!$template) {
            throw new InvalidArgumentException("Plantilla no encontrada");
        }

        if ($template->is_default) {
            throw new InvalidArgumentException("No se puede eliminar la plantilla por defecto");
        }

        return $this->templateRepository->delete($template);
    }

    /**
     * Obtener plantilla por defecto
     */
    public function getDefaultTemplate(string $documentType): ?DocumentTemplate
    {
        return $this->templateRepository->getDefaultTemplate($documentType);
    }

    /**
     * Establecer plantilla por defecto
     */
    public function setDefaultTemplate(int $id): bool
    {
        $template = $this->templateRepository->find($id);
        if (!$template) {
            throw new InvalidArgumentException("Plantilla no encontrada");
        }

        return $this->templateRepository->setDefaultTemplate($template);
    }

    /**
     * Duplicar plantilla
     */
    public function duplicateTemplate(int $id): DocumentTemplate
    {
        $template = $this->templateRepository->find($id);
        if (!$template) {
            throw new InvalidArgumentException("Plantilla no encontrada");
        }

        return $this->templateRepository->duplicate($template);
    }

    /**
     * Validar datos contra plantilla
     */
    public function validateData(int $templateId, array $data): array
    {
        $template = $this->templateRepository->find($templateId);
        if (!$template) {
            throw new InvalidArgumentException("Plantilla no encontrada");
        }

        return $template->validateData($data);
    }

    /**
     * Aplicar valores por defecto
     */
    public function applyDefaultValues(int $templateId, array $data): array
    {
        $template = $this->templateRepository->find($templateId);
        if (!$template) {
            throw new InvalidArgumentException("Plantilla no encontrada");
        }

        return $template->applyDefaultValues($data);
    }

    /**
     * Activar/Desactivar plantilla
     */
    public function toggleActive(int $id, bool $active = true): bool
    {
        $template = $this->templateRepository->find($id);
        if (!$template) {
            throw new InvalidArgumentException("Plantilla no encontrada");
        }

        return $this->templateRepository->toggleActive($template, $active);
    }
}
