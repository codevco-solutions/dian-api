<?php

namespace App\Repositories\Eloquent\Document\Commercial;

use App\Models\Document\Commercial\DocumentStateTransition;
use App\Repositories\Contracts\Document\Commercial\DocumentStateTransitionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class DocumentStateTransitionRepository implements DocumentStateTransitionRepositoryInterface
{
    public function create(array $data): DocumentStateTransition
    {
        $data['transition_date'] = $data['transition_date'] ?? Carbon::now();
        return DocumentStateTransition::create($data);
    }

    public function find(int $id): ?DocumentStateTransition
    {
        return DocumentStateTransition::find($id);
    }

    public function getDocumentTransitions(string $documentType, int $documentId): Collection
    {
        return DocumentStateTransition::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->orderBy('transition_date', 'desc')
            ->get();
    }

    public function getPendingApprovals(): Collection
    {
        return DocumentStateTransition::where('requires_approval', true)
            ->whereNull('approval_date')
            ->orderBy('transition_date')
            ->get();
    }

    public function approve(DocumentStateTransition $transition, int $approverId, string $comments = null): bool
    {
        return $transition->update([
            'approver_id' => $approverId,
            'approval_date' => Carbon::now(),
            'approval_status' => 'approved',
            'comments' => $comments
        ]);
    }

    public function reject(DocumentStateTransition $transition, int $approverId, string $comments = null): bool
    {
        return $transition->update([
            'approver_id' => $approverId,
            'approval_date' => Carbon::now(),
            'approval_status' => 'rejected',
            'comments' => $comments
        ]);
    }

    public function getLastTransition(string $documentType, int $documentId): ?DocumentStateTransition
    {
        return DocumentStateTransition::where('document_type', $documentType)
            ->where('document_id', $documentId)
            ->orderBy('transition_date', 'desc')
            ->first();
    }

    public function getTransitionsByUser(int $userId): Collection
    {
        return DocumentStateTransition::where('user_id', $userId)
            ->orderBy('transition_date', 'desc')
            ->get();
    }

    public function getTransitionsByApprover(int $approverId): Collection
    {
        return DocumentStateTransition::where('approver_id', $approverId)
            ->orderBy('transition_date', 'desc')
            ->get();
    }
}
