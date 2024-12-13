<?php

namespace App\Traits\Document\Commercial;

use App\Models\Document\Commercial\Invoice;
use App\Models\Document\Commercial\CreditNote;
use App\Models\Document\Commercial\DebitNote;
use App\Models\Document\Commercial\Order;
use App\Models\Document\Commercial\Quote;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasDocumentConversion
{
    /**
     * Tipos de documentos permitidos para conversión
     */
    protected static $allowedConversionTypes = [
        'quote' => Quote::class,
        'order' => Order::class,
        'invoice' => Invoice::class,
        'credit_note' => CreditNote::class,
        'debit_note' => DebitNote::class
    ];

    /**
     * Mapa de conversiones permitidas entre tipos de documentos
     */
    protected static $allowedConversions = [
        'quote' => ['order', 'invoice'],
        'order' => ['invoice', 'quote'],
        'invoice' => ['credit_note', 'debit_note'],
        'credit_note' => [],
        'debit_note' => []
    ];

    /**
     * Convertir a otro tipo de documento
     */
    public function convertTo(string $targetType, array $additionalData = []): Model
    {
        // Validar tipo de conversión
        if (!$this->canConvertTo($targetType)) {
            throw new InvalidArgumentException(
                "No se puede convertir de {$this->getDocumentType()} a {$targetType}"
            );
        }

        // Obtener clase del modelo destino
        $targetClass = static::$allowedConversionTypes[$targetType];

        // Crear nuevo documento
        $newDocument = new $targetClass;

        // Copiar datos comunes
        $this->copyCommonData($newDocument);

        // Copiar items si existen
        if (method_exists($this, 'items') && method_exists($newDocument, 'items')) {
            $this->copyItems($newDocument);
        }

        // Aplicar datos adicionales
        foreach ($additionalData as $key => $value) {
            $newDocument->$key = $value;
        }

        // Establecer referencias
        $this->createDocumentReference($newDocument);

        // Guardar nuevo documento
        $newDocument->save();

        return $newDocument;
    }

    /**
     * Verificar si se puede convertir a un tipo específico
     */
    public function canConvertTo(string $targetType): bool
    {
        $sourceType = $this->getDocumentType();
        return isset(static::$allowedConversions[$sourceType]) &&
               in_array($targetType, static::$allowedConversions[$sourceType]);
    }

    /**
     * Copiar datos comunes entre documentos
     */
    protected function copyCommonData(Model $target): void
    {
        $commonFields = [
            'customer_id',
            'supplier_id',
            'currency',
            'exchange_rate',
            'notes',
            'terms',
            'metadata'
        ];

        foreach ($commonFields as $field) {
            if (isset($this->$field)) {
                $target->$field = $this->$field;
            }
        }
    }

    /**
     * Copiar items del documento
     */
    protected function copyItems(Model $target): void
    {
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $target->items()->save($newItem);
        }
    }

    /**
     * Crear referencia entre documentos
     */
    protected function createDocumentReference(Model $target): void
    {
        $target->references()->create([
            'document_type' => $this->getDocumentType(),
            'document_id' => $this->id,
            'reference_type' => 'conversion',
            'metadata' => [
                'conversion_date' => now(),
                'converted_by' => auth()->id()
            ]
        ]);
    }

    /**
     * Obtener documentos relacionados por conversión
     */
    public function getRelatedConversions(): array
    {
        $related = [];

        // Documentos convertidos desde este
        $references = $this->references()
            ->where('reference_type', 'conversion')
            ->get();

        foreach ($references as $ref) {
            $related[] = [
                'type' => 'converted_to',
                'document_type' => $ref->document_type,
                'document_id' => $ref->document_id,
                'date' => $ref->metadata['conversion_date'] ?? null
            ];
        }

        // Documentos desde los que se convirtió este
        $sourceRefs = $this->sourceReferences()
            ->where('reference_type', 'conversion')
            ->get();

        foreach ($sourceRefs as $ref) {
            $related[] = [
                'type' => 'converted_from',
                'document_type' => $ref->source_document_type,
                'document_id' => $ref->source_document_id,
                'date' => $ref->metadata['conversion_date'] ?? null
            ];
        }

        return $related;
    }
}
