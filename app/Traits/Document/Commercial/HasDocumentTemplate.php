<?php

namespace App\Traits\Document\Commercial;

use App\Models\Document\Commercial\DocumentTemplate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasDocumentTemplate
{
    /**
     * Obtener la plantilla del documento
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Obtener el tipo de documento para las plantillas
     */
    abstract public function getDocumentType(): string;

    /**
     * Validar los datos del documento contra su plantilla
     */
    public function validateAgainstTemplate(array $data): array
    {
        if (!$this->template) {
            return [];
        }

        return $this->template->validateData($data);
    }

    /**
     * Aplicar valores por defecto de la plantilla
     */
    public function applyTemplateDefaults(array $data): array
    {
        if (!$this->template) {
            return $data;
        }

        return $this->template->applyDefaultValues($data);
    }

    /**
     * Obtener los campos requeridos de la plantilla
     */
    public function getRequiredFields(): array
    {
        if (!$this->template) {
            return [];
        }

        return $this->template->getRequiredFields();
    }

    /**
     * Verificar si todos los campos requeridos están presentes
     */
    public function hasRequiredFields(): bool
    {
        if (!$this->template) {
            return true;
        }

        $requiredFields = $this->getRequiredFields();
        $documentData = $this->toArray();

        foreach ($requiredFields as $field) {
            if (!isset($documentData[$field]) || empty($documentData[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generar contenido basado en la plantilla
     */
    public function generateContent(): string
    {
        if (!$this->template) {
            return '';
        }

        $content = $this->template->content;
        $documentData = $this->toArray();

        // Reemplazar variables en el contenido
        foreach ($documentData as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }

        return $content;
    }
}
