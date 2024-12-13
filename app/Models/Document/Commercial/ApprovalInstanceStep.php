<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalInstanceStep extends Model
{
    protected $fillable = [
        'approval_instance_id',
        'approval_flow_step_id',
        'order',
        'status',
        'approver_id',
        'approved_at',
        'comments',
        'timeout_at',
        'metadata'
    ];

    protected $casts = [
        'order' => 'integer',
        'approved_at' => 'datetime',
        'timeout_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * La instancia de aprobación a la que pertenece este paso
     */
    public function approvalInstance(): BelongsTo
    {
        return $this->belongsTo(ApprovalInstance::class);
    }

    /**
     * El paso del flujo de aprobación original
     */
    public function flowStep(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlowStep::class, 'approval_flow_step_id');
    }

    /**
     * El usuario que aprobó este paso
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }

    /**
     * Aprobar este paso
     */
    public function approve(\App\Models\User $user, string $comments = null): bool
    {
        if (!$this->flowStep->canBeApprovedBy($user)) {
            return false;
        }

        $this->status = 'approved';
        $this->approver_id = $user->id;
        $this->approved_at = now();
        $this->comments = $comments;

        if ($this->save()) {
            return $this->approvalInstance->moveToNextStep();
        }

        return false;
    }

    /**
     * Rechazar este paso
     */
    public function reject(\App\Models\User $user, string $comments = null): bool
    {
        if (!$this->flowStep->canBeApprovedBy($user)) {
            return false;
        }

        $this->status = 'rejected';
        $this->approver_id = $user->id;
        $this->approved_at = now();
        $this->comments = $comments;

        if ($this->save()) {
            $this->approvalInstance->status = 'rejected';
            return $this->approvalInstance->save();
        }

        return false;
    }

    /**
     * Verificar si el paso está expirado
     */
    public function isExpired(): bool
    {
        return $this->timeout_at && now()->isAfter($this->timeout_at);
    }

    /**
     * Manejar la expiración del paso
     */
    public function handleTimeout(): void
    {
        if (!$this->isExpired()) {
            return;
        }

        $action = $this->flowStep->on_timeout_action ?? 'reject';

        switch ($action) {
            case 'approve':
                $this->approve(null, 'Aprobado automáticamente por timeout');
                break;
            
            case 'reject':
                $this->reject(null, 'Rechazado automáticamente por timeout');
                break;
            
            case 'skip':
                $this->status = 'skipped';
                $this->save();
                $this->approvalInstance->moveToNextStep();
                break;
        }
    }
}
