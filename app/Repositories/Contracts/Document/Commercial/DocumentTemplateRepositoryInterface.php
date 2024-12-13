<?php

namespace App\Repositories\Contracts\Document\Commercial;

use App\Models\Document\Commercial\DocumentTemplate;
use Illuminate\Database\Eloquent\Collection;

interface DocumentTemplateRepositoryInterface
{
    /**
     * Obtener todas las plantillas
     */
    public function all(): Collection;

    /**
     * Obtener plantilla por ID
     */
    public function find(int $id): ?DocumentTemplate;

    /**
     * Crear nueva plantilla
     */
    public function create(array $data): DocumentTemplate;

    /**
     * Actualizar plantilla
     */
    public function update(DocumentTemplate $template, array $data): bool;

    /**
     * Eliminar plantilla
     */
    public function delete(DocumentTemplate $template): bool;

    /**
     * Obtener plantillas por tipo de documento
     */
    public function getTemplatesByType(string $documentType): Collection;

    /**
     * Obtener plantilla por defecto para un tipo de documento
     */
    public function getDefaultTemplate(string $documentType): ?DocumentTemplate;

    /**
     * Establecer plantilla por defecto
     */
    public function setDefaultTemplate(DocumentTemplate $template): bool;

    /**
     * Duplicar plantilla
     */
    public function duplicate(DocumentTemplate $template): DocumentTemplate;

    /**
     * Obtener plantillas activas
     */
    public function getActiveTemplates(): Collection;

    /**
     * Activar/Desactivar plantilla
     */
    public function toggleActive(DocumentTemplate $template, bool $active = true): bool;
}
