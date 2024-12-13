<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentStateTransition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_id',
        'from_state_id',
        'to_state_id',
        'user_id',
        'approver_id',
        'transition_date',
        'requires_approval',
        'approval_date',
        'approval_status',
        'comments',
        'metadata'
    ];

    protected $casts = [
        'transition_date' => 'datetime',
        'approval_date' => 'datetime',
        'requires_approval' => 'boolean',
        'metadata' => 'array'
    ];

    public function fromState()
    {
        return $this->belongsTo(DocumentState::class, 'from_state_id');
    }

    public function toState()
    {
        return $this->belongsTo(DocumentState::class, 'to_state_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }

    /**
     * Obtener el documento relacionado basado en el tipo
     */
    public function document()
    {
        switch ($this->document_type) {
            case 'invoice':
                return $this->belongsTo(Invoice::class, 'document_id');
            case 'credit_note':
                return $this->belongsTo(CreditNote::class, 'document_id');
            case 'debit_note':
                return $this->belongsTo(DebitNote::class, 'document_id');
            case 'order':
                return $this->belongsTo(Order::class, 'document_id');
            case 'quote':
                return $this->belongsTo(Quote::class, 'document_id');
            default:
                return null;
        }
    }

    /**
     * Verificar si la transición está aprobada
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Verificar si la transición está pendiente de aprobación
     */
    public function isPendingApproval(): bool
    {
        return $this->requires_approval && !$this->approval_date;
    }

    /**
     * Verificar si la transición está rechazada
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }
}
