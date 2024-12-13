<?php

namespace App\Traits\Document\Commercial;

use App\Models\Document\Commercial\DocumentChange;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait HasChangeHistory
{
    /**
     * Boot the trait
     */
    public static function bootHasChangeHistory()
    {
        static::created(function ($model) {
            $model->recordChange('created');
        });

        static::updated(function ($model) {
            $model->recordChange('updated');
        });

        static::deleted(function ($model) {
            $model->recordChange('deleted');
        });
    }

    /**
     * Relación con los cambios
     */
    public function changes(): MorphMany
    {
        return $this->morphMany(DocumentChange::class, 'changeable');
    }

    /**
     * Registrar un cambio
     */
    public function recordChange(string $action, array $data = null): void
    {
        $changes = [];

        if ($action === 'updated') {
            $changes = $this->getModelChanges();
        }

        $this->changes()->create([
            'action' => $action,
            'user_id' => Auth::id(),
            'changes' => $changes,
            'data' => $data,
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]
        ]);
    }

    /**
     * Obtener los cambios del modelo
     */
    protected function getModelChanges(): array
    {
        $changes = [];
        $dirty = $this->getDirty();

        foreach ($dirty as $attribute => $newValue) {
            if ($this->shouldTrackAttribute($attribute)) {
                $original = $this->getOriginal($attribute);
                $changes[$attribute] = [
                    'from' => $original,
                    'to' => $newValue
                ];
            }
        }

        return $changes;
    }

    /**
     * Verificar si se debe registrar el cambio de un atributo
     */
    protected function shouldTrackAttribute(string $attribute): bool
    {
        $excludedAttributes = [
            'updated_at',
            'created_at',
            'deleted_at',
            'password',
            'remember_token'
        ];

        return !in_array($attribute, $excludedAttributes);
    }

    /**
     * Obtener historial de cambios formateado
     */
    public function getChangeHistory(): array
    {
        return $this->changes()
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($change) {
                return [
                    'action' => $change->action,
                    'user' => $change->user ? [
                        'name' => $change->user->name,
                        'email' => $change->user->email
                    ] : null,
                    'changes' => $this->formatChanges($change->changes),
                    'data' => $change->data,
                    'date' => $change->created_at->format('Y-m-d H:i:s'),
                    'metadata' => $change->metadata
                ];
            })
            ->toArray();
    }

    /**
     * Formatear cambios para visualización
     */
    protected function formatChanges(?array $changes): array
    {
        if (!$changes) {
            return [];
        }

        $formatted = [];
        foreach ($changes as $attribute => $change) {
            $formatted[] = [
                'field' => $attribute,
                'from' => $this->formatValue($change['from']),
                'to' => $this->formatValue($change['to'])
            ];
        }

        return $formatted;
    }

    /**
     * Formatear valor para visualización
     */
    protected function formatValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }
}
