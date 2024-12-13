<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFlowStep extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'approval_flow_id',
        'name',
        'description',
        'type',
        'approver_type',
        'approver_id',
        'order',
        'is_required',
        'timeout_hours',
        'on_timeout_action',
        'conditions',
        'metadata'
    ];

    protected $casts = [
        'order' => 'integer',
        'is_required' => 'boolean',
        'timeout_hours' => 'integer',
        'conditions' => 'array',
        'metadata' => 'array'
    ];

    /**
     * El flujo de aprobación al que pertenece este paso
     */
    public function approvalFlow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class);
    }

    /**
     * El aprobador (si es un usuario específico)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }

    /**
     * Verificar si un usuario puede aprobar este paso
     */
    public function canBeApprovedBy(\App\Models\User $user): bool
    {
        switch ($this->approver_type) {
            case 'user':
                return $this->approver_id === $user->id;
            
            case 'role':
                return $user->hasRole($this->approver_id);
            
            case 'department':
                return $user->department_id === $this->approver_id;
            
            case 'any':
                return true;
            
            default:
                return false;
        }
    }

    /**
     * Obtener el tiempo límite para este paso
     */
    public function getTimeoutDate(): ?\Carbon\Carbon
    {
        if (!$this->timeout_hours) {
            return null;
        }

        return now()->addHours($this->timeout_hours);
    }
}
