<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalInstance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'approval_flow_id',
        'document_type',
        'document_id',
        'status',
        'current_step',
        'metadata'
    ];

    protected $casts = [
        'current_step' => 'integer',
        'metadata' => 'array'
    ];

    /**
     * El flujo de aprobación al que pertenece esta instancia
     */
    public function approvalFlow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class);
    }

    /**
     * El documento que está siendo aprobado
     */
    public function document()
    {
        return $this->morphTo();
    }

    /**
     * Los pasos de aprobación de esta instancia
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalInstanceStep::class)->orderBy('order');
    }

    /**
     * Verificar si la aprobación está completa
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si la aprobación está rechazada
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Verificar si la aprobación está en progreso
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Obtener el paso actual
     */
    public function getCurrentStep(): ?ApprovalInstanceStep
    {
        return $this->steps()->where('order', $this->current_step)->first();
    }

    /**
     * Mover al siguiente paso
     */
    public function moveToNextStep(): bool
    {
        $nextStep = $this->steps()
            ->where('order', '>', $this->current_step)
            ->orderBy('order')
            ->first();

        if (!$nextStep) {
            $this->status = 'completed';
            return $this->save();
        }

        $this->current_step = $nextStep->order;
        return $this->save();
    }
}
