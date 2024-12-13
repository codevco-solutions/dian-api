<?php

namespace App\Repositories\Contracts\Document\Commercial;

use App\Models\Document\Commercial\DocumentStateTransition;
use Illuminate\Database\Eloquent\Collection;

interface DocumentStateTransitionRepositoryInterface
{
    /**
     * Crear nueva transición
     */
    public function create(array $data): DocumentStateTransition;

    /**
     * Obtener transición por ID
     */
    public function find(int $id): ?DocumentStateTransition;

    /**
     * Obtener historial de transiciones de un documento
     */
    public function getDocumentTransitions(string $documentType, int $documentId): Collection;

    /**
     * Obtener transiciones pendientes de aprobación
     */
    public function getPendingApprovals(): Collection;

    /**
     * Aprobar transición
     */
    public function approve(DocumentStateTransition $transition, int $approverId, string $comments = null): bool;

    /**
     * Rechazar transición
     */
    public function reject(DocumentStateTransition $transition, int $approverId, string $comments = null): bool;

    /**
     * Obtener última transición de un documento
     */
    public function getLastTransition(string $documentType, int $documentId): ?DocumentStateTransition;

    /**
     * Obtener transiciones por usuario
     */
    public function getTransitionsByUser(int $userId): Collection;

    /**
     * Obtener transiciones por aprobador
     */
    public function getTransitionsByApprover(int $approverId): Collection;
}
