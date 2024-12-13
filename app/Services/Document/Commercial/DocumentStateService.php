<?php

namespace App\Services\Document\Commercial;

use App\Models\Document\Commercial\DocumentState;
use App\Repositories\Contracts\Document\Commercial\DocumentStateRepositoryInterface;
use App\Repositories\Contracts\Document\Commercial\DocumentStateTransitionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class DocumentStateService
{
    protected $stateRepository;
    protected $transitionRepository;

    public function __construct(
        DocumentStateRepositoryInterface $stateRepository,
        DocumentStateTransitionRepositoryInterface $transitionRepository
    ) {
        $this->stateRepository = $stateRepository;
        $this->transitionRepository = $transitionRepository;
    }

    /**
     * Obtener todos los estados
     */
    public function getAllStates(): Collection
    {
        return $this->stateRepository->all();
    }

    /**
     * Crear nuevo estado
     */
    public function createState(array $data): DocumentState
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:50',
            'order' => 'required|integer|min:0',
            'requires_approval' => 'boolean',
            'allows_edit' => 'boolean',
            'allows_delete' => 'boolean',
            'next_states' => 'nullable|array',
            'next_states.*' => 'integer|exists:document_states,id'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->stateRepository->create($data);
    }

    /**
     * Actualizar estado
     */
    public function updateState(int $id, array $data): bool
    {
        $state = $this->stateRepository->find($id);
        if (!$state) {
            throw new InvalidArgumentException("Estado no encontrado");
        }

        $validator = Validator::make($data, [
            'name' => 'string|max:255',
            'type' => 'nullable|string|max:50',
            'color' => 'string|max:7',
            'icon' => 'string|max:50',
            'order' => 'integer|min:0',
            'requires_approval' => 'boolean',
            'allows_edit' => 'boolean',
            'allows_delete' => 'boolean',
            'next_states' => 'nullable|array',
            'next_states.*' => 'integer|exists:document_states,id'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->stateRepository->update($state, $data);
    }

    /**
     * Eliminar estado
     */
    public function deleteState(int $id): bool
    {
        $state = $this->stateRepository->find($id);
        if (!$state) {
            throw new InvalidArgumentException("Estado no encontrado");
        }

        if ($state->is_system) {
            throw new InvalidArgumentException("No se puede eliminar un estado del sistema");
        }

        return $this->stateRepository->delete($state);
    }

    /**
     * Validar transición de estado
     */
    public function validateStateTransition(string $documentType, int $documentId, int $fromStateId, int $toStateId): bool
    {
        // Verificar que los estados existen
        $fromState = $this->stateRepository->find($fromStateId);
        $toState = $this->stateRepository->find($toStateId);

        if (!$fromState || !$toState) {
            throw new InvalidArgumentException("Estado no encontrado");
        }

        // Verificar que la transición está permitida
        if (!$this->stateRepository->transitionExists($fromStateId, $toStateId)) {
            throw new InvalidArgumentException("Transición no permitida");
        }

        // Verificar que no hay una transición pendiente
        $lastTransition = $this->transitionRepository->getLastTransition($documentType, $documentId);
        if ($lastTransition && $lastTransition->isPendingApproval()) {
            throw new InvalidArgumentException("Ya existe una transición pendiente de aprobación");
        }

        return true;
    }

    /**
     * Realizar transición de estado
     */
    public function transitionState(string $documentType, int $documentId, int $fromStateId, int $toStateId, array $data = []): void
    {
        $this->validateStateTransition($documentType, $documentId, $fromStateId, $toStateId);

        $toState = $this->stateRepository->find($toStateId);
        
        $transitionData = [
            'document_type' => $documentType,
            'document_id' => $documentId,
            'from_state_id' => $fromStateId,
            'to_state_id' => $toStateId,
            'user_id' => auth()->id(),
            'requires_approval' => $toState->requires_approval,
            'comments' => $data['comments'] ?? null,
            'metadata' => $data['metadata'] ?? null
        ];

        $this->transitionRepository->create($transitionData);
    }

    /**
     * Obtener estados disponibles para un tipo de documento
     */
    public function getAvailableStates(string $documentType): Collection
    {
        return $this->stateRepository->getStatesByDocumentType($documentType);
    }

    /**
     * Obtener transiciones disponibles desde un estado
     */
    public function getAvailableTransitions(int $stateId): Collection
    {
        $state = $this->stateRepository->find($stateId);
        if (!$state) {
            throw new InvalidArgumentException("Estado no encontrado");
        }

        return $this->stateRepository->getAvailableTransitions($state);
    }
}
