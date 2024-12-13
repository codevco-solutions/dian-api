<?php

namespace App\Traits\Document\Commercial;

use Carbon\Carbon;
use InvalidArgumentException;

trait HasRecurrence
{
    /**
     * Verificar si el documento es recurrente
     */
    public function isRecurrent(): bool
    {
        return !empty($this->recurrence_config);
    }

    /**
     * Configurar recurrencia
     */
    public function setRecurrence(array $config): void
    {
        $this->validateRecurrenceConfig($config);
        $this->recurrence_config = $config;
        $this->save();
    }

    /**
     * Eliminar recurrencia
     */
    public function removeRecurrence(): void
    {
        $this->recurrence_config = null;
        $this->save();
    }

    /**
     * Obtener próxima fecha de recurrencia
     */
    public function getNextRecurrenceDate(): ?Carbon
    {
        if (!$this->isRecurrent()) {
            return null;
        }

        $config = $this->recurrence_config;
        $lastDate = $this->date;
        $frequency = $config['frequency'] ?? 'monthly';
        $interval = $config['interval'] ?? 1;

        $nextDate = Carbon::parse($lastDate);

        switch ($frequency) {
            case 'daily':
                $nextDate->addDays($interval);
                break;
            case 'weekly':
                $nextDate->addWeeks($interval);
                break;
            case 'monthly':
                $nextDate->addMonths($interval);
                break;
            case 'yearly':
                $nextDate->addYears($interval);
                break;
            default:
                throw new InvalidArgumentException("Frecuencia de recurrencia no válida");
        }

        // Verificar fecha límite si existe
        if (isset($config['end_date'])) {
            $endDate = Carbon::parse($config['end_date']);
            if ($nextDate->greaterThan($endDate)) {
                return null;
            }
        }

        // Verificar número máximo de ocurrencias si existe
        if (isset($config['max_occurrences'])) {
            $currentOccurrences = $this->getRecurrenceOccurrences();
            if ($currentOccurrences >= $config['max_occurrences']) {
                return null;
            }
        }

        return $nextDate;
    }

    /**
     * Obtener número de ocurrencias
     */
    public function getRecurrenceOccurrences(): int
    {
        if (!$this->isRecurrent()) {
            return 0;
        }

        return static::where('parent_id', $this->id)->count() + 1;
    }

    /**
     * Validar configuración de recurrencia
     */
    protected function validateRecurrenceConfig(array $config): void
    {
        $allowedFrequencies = ['daily', 'weekly', 'monthly', 'yearly'];

        if (!isset($config['frequency']) || !in_array($config['frequency'], $allowedFrequencies)) {
            throw new InvalidArgumentException("Frecuencia de recurrencia no válida");
        }

        if (!isset($config['interval']) || $config['interval'] < 1) {
            throw new InvalidArgumentException("Intervalo de recurrencia no válido");
        }

        if (isset($config['end_date'])) {
            try {
                $endDate = Carbon::parse($config['end_date']);
                if ($endDate->isPast()) {
                    throw new InvalidArgumentException("La fecha límite no puede estar en el pasado");
                }
            } catch (\Exception $e) {
                throw new InvalidArgumentException("Fecha límite no válida");
            }
        }

        if (isset($config['max_occurrences'])) {
            if (!is_int($config['max_occurrences']) || $config['max_occurrences'] < 1) {
                throw new InvalidArgumentException("Número máximo de ocurrencias no válido");
            }
        }
    }

    /**
     * Crear siguiente documento recurrente
     */
    public function createNextRecurrence(): ?self
    {
        if (!$this->isRecurrent()) {
            return null;
        }

        $nextDate = $this->getNextRecurrenceDate();
        if (!$nextDate) {
            return null;
        }

        $newDocument = $this->replicate();
        $newDocument->parent_id = $this->id;
        $newDocument->date = $nextDate;
        $newDocument->state_id = config('documents.states.draft'); // Estado inicial
        $newDocument->save();

        return $newDocument;
    }
}
