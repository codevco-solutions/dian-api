<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentState extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'color',
        'icon',
        'order',
        'is_system',
        'requires_approval',
        'allows_edit',
        'allows_delete',
        'next_states',
        'metadata'
    ];

    protected $casts = [
        'order' => 'integer',
        'is_system' => 'boolean',
        'requires_approval' => 'boolean',
        'allows_edit' => 'boolean',
        'allows_delete' => 'boolean',
        'next_states' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Estados del sistema predefinidos
     */
    public static $systemStates = [
        'draft' => [
            'name' => 'Borrador',
            'color' => '#808080',
            'icon' => 'draft',
            'order' => 1,
            'allows_edit' => true,
            'allows_delete' => true
        ],
        'pending_approval' => [
            'name' => 'Pendiente de Aprobación',
            'color' => '#FFA500',
            'icon' => 'pending',
            'order' => 2,
            'requires_approval' => true
        ],
        'approved' => [
            'name' => 'Aprobado',
            'color' => '#008000',
            'icon' => 'approved',
            'order' => 3
        ],
        'rejected' => [
            'name' => 'Rechazado',
            'color' => '#FF0000',
            'icon' => 'rejected',
            'order' => 4,
            'allows_edit' => true
        ],
        'cancelled' => [
            'name' => 'Anulado',
            'color' => '#000000',
            'icon' => 'cancelled',
            'order' => 5
        ],
        'completed' => [
            'name' => 'Completado',
            'color' => '#0000FF',
            'icon' => 'completed',
            'order' => 6
        ]
    ];

    public function documents()
    {
        return $this->hasMany(Invoice::class, 'state_id')
            ->orHas(CreditNote::class, 'state_id')
            ->orHas(DebitNote::class, 'state_id')
            ->orHas(Order::class, 'state_id')
            ->orHas(Quote::class, 'state_id');
    }

    public function stateTransitions()
    {
        return $this->hasMany(DocumentStateTransition::class, 'from_state_id');
    }

    /**
     * Verificar si el estado permite transición a otro estado
     */
    public function allowsTransitionTo(int $targetStateId): bool
    {
        return in_array($targetStateId, $this->next_states ?? []);
    }

    /**
     * Verificar si el estado requiere aprobación
     */
    public function requiresApproval(): bool
    {
        return $this->requires_approval;
    }

    /**
     * Verificar si el estado permite edición
     */
    public function allowsEdit(): bool
    {
        return $this->allows_edit;
    }

    /**
     * Verificar si el estado permite eliminación
     */
    public function allowsDelete(): bool
    {
        return $this->allows_delete;
    }
}
