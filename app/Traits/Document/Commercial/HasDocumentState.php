<?php

namespace App\Traits\Document\Commercial;

use App\Models\Document\Commercial\DocumentState;
use App\Models\Document\Commercial\DocumentStateTransition;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasDocumentState
{
    /**
     * Obtener el estado actual del documento
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(DocumentState::class, 'state_id');
    }

    /**
     * Obtener el historial de transiciones del documento
     */
    public function stateTransitions(): HasMany
    {
        return $this->hasMany(DocumentStateTransition::class, 'document_id')
            ->where('document_type', $this->getDocumentType());
    }

    /**
     * Obtener el tipo de documento para las transiciones
     */
    abstract public function getDocumentType(): string;

    /**
     * Verificar si el documento puede cambiar al estado especificado
     */
    public function canTransitionTo(int $targetStateId): bool
    {
        return $this->state->allowsTransitionTo($targetStateId);
    }

    /**
     * Verificar si el documento puede ser editado
     */
    public function canBeEdited(): bool
    {
        return $this->state->allowsEdit();
    }

    /**
     * Verificar si el documento puede ser eliminado
     */
    public function canBeDeleted(): bool
    {
        return $this->state->allowsDelete();
    }

    /**
     * Verificar si el documento requiere aprobación
     */
    public function requiresApproval(): bool
    {
        return $this->state->requiresApproval();
    }

    /**
     * Obtener la última transición del documento
     */
    public function getLastTransition(): ?DocumentStateTransition
    {
        return $this->stateTransitions()
            ->orderBy('transition_date', 'desc')
            ->first();
    }

    /**
     * Verificar si el documento tiene una transición pendiente
     */
    public function hasPendingTransition(): bool
    {
        $lastTransition = $this->getLastTransition();
        return $lastTransition && $lastTransition->isPendingApproval();
    }

    /**
     * Obtener el estado anterior del documento
     */
    public function getPreviousState(): ?DocumentState
    {
        $previousTransition = $this->stateTransitions()
            ->orderBy('transition_date', 'desc')
            ->skip(1)
            ->first();

        return $previousTransition ? $previousTransition->fromState : null;
    }
}
