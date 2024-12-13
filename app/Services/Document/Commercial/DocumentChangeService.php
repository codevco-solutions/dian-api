<?php

namespace App\Services\Document\Commercial;

use App\Models\Document\Commercial\DocumentChange;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class DocumentChangeService
{
    /**
     * Mapa de clases de modelos por tipo
     */
    protected $modelMap = [
        'quote' => \App\Models\Document\Commercial\Quote::class,
        'order' => \App\Models\Document\Commercial\Order::class,
        'invoice' => \App\Models\Document\Commercial\Invoice::class,
        'credit_note' => \App\Models\Document\Commercial\CreditNote::class,
        'debit_note' => \App\Models\Document\Commercial\DebitNote::class
    ];

    /**
     * Obtener historial de cambios de un documento
     */
    public function getHistory(string $documentType, int $documentId): array
    {
        $document = $this->getDocument($documentType, $documentId);

        if (!method_exists($document, 'getChangeHistory')) {
            throw new InvalidArgumentException(
                "El documento no soporta historial de cambios. Asegúrese de usar el trait HasChangeHistory."
            );
        }

        return [
            'document' => [
                'id' => $document->id,
                'type' => $documentType
            ],
            'history' => $document->getChangeHistory()
        ];
    }

    /**
     * Obtener detalles de un cambio específico
     */
    public function getChangeDetails(int $changeId): array
    {
        $change = DocumentChange::with('user:id,name,email')->findOrFail($changeId);

        return [
            'id' => $change->id,
            'document' => [
                'type' => $change->changeable_type,
                'id' => $change->changeable_id
            ],
            'action' => $change->action,
            'user' => $change->user ? [
                'name' => $change->user->name,
                'email' => $change->user->email
            ] : null,
            'changes' => $this->formatChanges($change->changes),
            'data' => $change->data,
            'metadata' => $change->metadata,
            'timestamp' => $change->created_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Obtener resumen de cambios por período
     */
    public function getChangeSummary(
        string $documentType,
        int $documentId,
        string $startDate,
        string $endDate
    ): array {
        $document = $this->getDocument($documentType, $documentId);
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $changes = $document->changes()
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy('action')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'users' => $group->pluck('user_id')->unique()->count(),
                    'last_change' => $group->max('created_at')->format('Y-m-d H:i:s')
                ];
            });

        return [
            'document' => [
                'id' => $document->id,
                'type' => $documentType
            ],
            'period' => [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d')
            ],
            'summary' => $changes
        ];
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
                'from' => $change['from'] ?? null,
                'to' => $change['to'] ?? null
            ];
        }

        return $formatted;
    }

    /**
     * Obtener instancia de documento
     */
    protected function getDocument(string $type, int $id): Model
    {
        if (!isset($this->modelMap[$type])) {
            throw new InvalidArgumentException("Tipo de documento inválido: {$type}");
        }

        $modelClass = $this->modelMap[$type];
        $document = $modelClass::find($id);

        if (!$document) {
            throw new InvalidArgumentException("Documento no encontrado: {$type} #{$id}");
        }

        return $document;
    }
}
