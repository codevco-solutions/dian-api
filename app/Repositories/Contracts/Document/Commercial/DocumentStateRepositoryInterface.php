<?php

namespace App\Repositories\Contracts\Document\Commercial;

use App\Models\Document\Commercial\DocumentState;
use Illuminate\Database\Eloquent\Collection;

interface DocumentStateRepositoryInterface
{
    /**
     * Obtener todos los estados
     */
    public function all(): Collection;

    /**
     * Obtener estado por ID
     */
    public function find(int $id): ?DocumentState;

    /**
     * Crear nuevo estado
     */
    public function create(array $data): DocumentState;

    /**
     * Actualizar estado
     */
    public function update(DocumentState $state, array $data): bool;

    /**
     * Eliminar estado
     */
    public function delete(DocumentState $state): bool;

    /**
     * Obtener estados predefinidos del sistema
     */
    public function getSystemStates(): Collection;

    /**
     * Obtener estados disponibles para transición desde un estado específico
     */
    public function getAvailableTransitions(DocumentState $state): Collection;

    /**
     * Obtener estados por tipo de documento
     */
    public function getStatesByDocumentType(string $documentType): Collection;

    /**
     * Verificar si existe transición entre estados
     */
    public function transitionExists(int $fromStateId, int $toStateId): bool;
}
