<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalFlow extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'document_type',
        'conditions',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * Los pasos del flujo de aprobación
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ApprovalFlowStep::class)->orderBy('order');
    }

    /**
     * Las instancias de aprobación creadas desde este flujo
     */
    public function approvalInstances(): HasMany
    {
        return $this->hasMany(ApprovalInstance::class);
    }

    /**
     * Evaluar si un documento cumple las condiciones para este flujo
     */
    public function matchesConditions(Model $document): bool
    {
        if (!$this->conditions) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if (!$this->evaluateCondition($document, $condition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluar una condición específica
     */
    protected function evaluateCondition(Model $document, array $condition): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? '=';
        $value = $condition['value'] ?? null;

        if (!$field || !isset($document->$field)) {
            return false;
        }

        $fieldValue = $document->$field;

        switch ($operator) {
            case '=':
                return $fieldValue == $value;
            case '!=':
                return $fieldValue != $value;
            case '>':
                return $fieldValue > $value;
            case '>=':
                return $fieldValue >= $value;
            case '<':
                return $fieldValue < $value;
            case '<=':
                return $fieldValue <= $value;
            case 'in':
                return in_array($fieldValue, (array) $value);
            case 'not_in':
                return !in_array($fieldValue, (array) $value);
            case 'contains':
                return str_contains($fieldValue, $value);
            default:
                return false;
        }
    }
}
