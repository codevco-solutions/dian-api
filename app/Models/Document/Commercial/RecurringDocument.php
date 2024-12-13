<?php

namespace App\Models\Document\Commercial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class RecurringDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_id',
        'frequency_type',
        'frequency_value',
        'start_date',
        'end_date',
        'last_generated_at',
        'next_generation_date',
        'is_active',
        'generation_day',
        'generation_conditions',
        'metadata'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_generated_at' => 'datetime',
        'next_generation_date' => 'date',
        'is_active' => 'boolean',
        'generation_conditions' => 'array',
        'metadata' => 'array'
    ];

    /**
     * El documento base para la recurrencia
     */
    public function document(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Calcular la próxima fecha de generación
     */
    public function calculateNextGenerationDate(): ?Carbon
    {
        if (!$this->is_active || ($this->end_date && $this->end_date->isPast())) {
            return null;
        }

        $baseDate = $this->last_generated_at ? $this->last_generated_at : $this->start_date;
        
        switch ($this->frequency_type) {
            case 'daily':
                $nextDate = $baseDate->addDays($this->frequency_value);
                break;
            
            case 'weekly':
                $nextDate = $baseDate->addWeeks($this->frequency_value);
                break;
            
            case 'monthly':
                $nextDate = $baseDate->addMonths($this->frequency_value);
                if ($this->generation_day) {
                    $nextDate->day($this->generation_day);
                }
                break;
            
            case 'yearly':
                $nextDate = $baseDate->addYears($this->frequency_value);
                break;
            
            default:
                return null;
        }

        // No generar después de la fecha final
        if ($this->end_date && $nextDate->isAfter($this->end_date)) {
            return null;
        }

        return $nextDate;
    }

    /**
     * Generar el siguiente documento recurrente
     */
    public function generateNextDocument(): ?Model
    {
        if (!$this->shouldGenerateNext()) {
            return null;
        }

        $sourceDocument = $this->document;
        $newDocument = $sourceDocument->replicate();
        
        // Actualizar fechas y números
        $newDocument->date = now();
        $newDocument->due_date = now()->addDays($sourceDocument->payment_terms ?? 0);
        $newDocument->number = null; // Se generará automáticamente
        
        // Guardar nuevo documento
        $newDocument->save();

        // Copiar items si existen
        if (method_exists($sourceDocument, 'items')) {
            foreach ($sourceDocument->items as $item) {
                $newItem = $item->replicate();
                $newDocument->items()->save($newItem);
            }
        }

        // Actualizar fechas de recurrencia
        $this->last_generated_at = now();
        $this->next_generation_date = $this->calculateNextGenerationDate();
        $this->save();

        return $newDocument;
    }

    /**
     * Verificar si se debe generar el siguiente documento
     */
    protected function shouldGenerateNext(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        if (!$this->next_generation_date || !$this->next_generation_date->isPast()) {
            return false;
        }

        return $this->evaluateGenerationConditions();
    }

    /**
     * Evaluar condiciones de generación
     */
    protected function evaluateGenerationConditions(): bool
    {
        if (!$this->generation_conditions) {
            return true;
        }

        foreach ($this->generation_conditions as $condition) {
            if (!$this->evaluateCondition($condition)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluar una condición específica
     */
    protected function evaluateCondition(array $condition): bool
    {
        $type = $condition['type'] ?? null;
        $value = $condition['value'] ?? null;

        switch ($type) {
            case 'business_day':
                return !now()->isWeekend();
            
            case 'working_hours':
                $hour = now()->hour;
                return $hour >= ($value['start'] ?? 9) && $hour <= ($value['end'] ?? 17);
            
            case 'custom':
                return $this->evaluateCustomCondition($condition);
            
            default:
                return true;
        }
    }

    /**
     * Evaluar una condición personalizada
     */
    protected function evaluateCustomCondition(array $condition): bool
    {
        // Implementar lógica personalizada según necesidades
        return true;
    }
}
