<?php

namespace App\Repositories\Eloquent\Document\Commercial;

use App\Models\Document\Commercial\DocumentState;
use App\Repositories\Contracts\Document\Commercial\DocumentStateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocumentStateRepository implements DocumentStateRepositoryInterface
{
    public function all(): Collection
    {
        return DocumentState::orderBy('order')->get();
    }

    public function find(int $id): ?DocumentState
    {
        return DocumentState::find($id);
    }

    public function create(array $data): DocumentState
    {
        return DocumentState::create($data);
    }

    public function update(DocumentState $state, array $data): bool
    {
        return $state->update($data);
    }

    public function delete(DocumentState $state): bool
    {
        return $state->delete();
    }

    public function getSystemStates(): Collection
    {
        return DocumentState::where('is_system', true)
            ->orderBy('order')
            ->get();
    }

    public function getAvailableTransitions(DocumentState $state): Collection
    {
        $nextStateIds = $state->next_states ?? [];
        return DocumentState::whereIn('id', $nextStateIds)
            ->orderBy('order')
            ->get();
    }

    public function getStatesByDocumentType(string $documentType): Collection
    {
        return DocumentState::where('type', $documentType)
            ->orWhereNull('type')
            ->orderBy('order')
            ->get();
    }

    public function transitionExists(int $fromStateId, int $toStateId): bool
    {
        $fromState = $this->find($fromStateId);
        if (!$fromState) {
            return false;
        }

        return in_array($toStateId, $fromState->next_states ?? []);
    }
}
