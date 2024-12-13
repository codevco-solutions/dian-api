<?php

namespace App\Services\Document\Commercial;

use App\Models\Document\Commercial\Invoice;
use App\Models\Document\Commercial\Order;
use App\Models\Document\Commercial\Quote;
use App\Models\Document\Commercial\CreditNote;
use App\Models\Document\Commercial\DebitNote;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class DocumentConversionService
{
    /**
     * Mapa de clases de modelos por tipo
     */
    protected $modelMap = [
        'quote' => Quote::class,
        'order' => Order::class,
        'invoice' => Invoice::class,
        'credit_note' => CreditNote::class,
        'debit_note' => DebitNote::class
    ];

    /**
     * Convertir un documento a otro tipo
     */
    public function convert(string $sourceType, int $documentId, string $targetType, array $additionalData = []): array
    {
        // Validar tipos de documentos
        $this->validateDocumentTypes($sourceType, $targetType);

        // Obtener documento fuente
        $sourceModel = $this->getDocument($sourceType, $documentId);

        // Validar que el documento puede ser convertido
        if (!method_exists($sourceModel, 'convertTo')) {
            throw new InvalidArgumentException(
                "El documento no soporta conversiones. Asegúrese de usar el trait HasDocumentConversion."
            );
        }

        // Realizar la conversión
        $newDocument = $sourceModel->convertTo($targetType, $additionalData);

        return [
            'success' => true,
            'message' => "Documento convertido exitosamente de {$sourceType} a {$targetType}",
            'source_document' => [
                'id' => $sourceModel->id,
                'type' => $sourceType
            ],
            'target_document' => [
                'id' => $newDocument->id,
                'type' => $targetType
            ]
        ];
    }

    /**
     * Obtener tipos de conversión permitidos para un documento
     */
    public function getAllowedConversions(string $sourceType, int $documentId): array
    {
        $document = $this->getDocument($sourceType, $documentId);

        if (!method_exists($document, 'canConvertTo')) {
            return ['allowed_conversions' => []];
        }

        $allowedConversions = [];
        foreach (array_keys($this->modelMap) as $targetType) {
            if ($document->canConvertTo($targetType)) {
                $allowedConversions[] = $targetType;
            }
        }

        return [
            'source_document' => [
                'id' => $document->id,
                'type' => $sourceType
            ],
            'allowed_conversions' => $allowedConversions
        ];
    }

    /**
     * Obtener documentos relacionados por conversión
     */
    public function getRelatedConversions(string $sourceType, int $documentId): array
    {
        $document = $this->getDocument($sourceType, $documentId);

        if (!method_exists($document, 'getRelatedConversions')) {
            return ['related_conversions' => []];
        }

        return [
            'source_document' => [
                'id' => $document->id,
                'type' => $sourceType
            ],
            'related_conversions' => $document->getRelatedConversions()
        ];
    }

    /**
     * Validar tipos de documentos
     */
    protected function validateDocumentTypes(string $sourceType, string $targetType): void
    {
        if (!isset($this->modelMap[$sourceType])) {
            throw new InvalidArgumentException("Tipo de documento fuente inválido: {$sourceType}");
        }

        if (!isset($this->modelMap[$targetType])) {
            throw new InvalidArgumentException("Tipo de documento destino inválido: {$targetType}");
        }
    }

    /**
     * Obtener instancia de documento
     */
    protected function getDocument(string $type, int $id): Model
    {
        $modelClass = $this->modelMap[$type];
        $document = $modelClass::find($id);

        if (!$document) {
            throw new InvalidArgumentException("Documento no encontrado: {$type} #{$id}");
        }

        return $document;
    }
}
