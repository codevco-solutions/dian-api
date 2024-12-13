<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Services\Document\Commercial\DocumentConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentConversionController extends Controller
{
    protected $conversionService;

    public function __construct(DocumentConversionService $conversionService)
    {
        $this->conversionService = $conversionService;
    }

    /**
     * Convertir un documento a otro tipo
     */
    public function convert(Request $request, string $sourceType, int $documentId): JsonResponse
    {
        $this->validate($request, [
            'target_type' => 'required|string',
            'additional_data' => 'array'
        ]);

        $result = $this->conversionService->convert(
            $sourceType,
            $documentId,
            $request->target_type,
            $request->additional_data ?? []
        );

        return response()->json($result);
    }

    /**
     * Obtener tipos de conversión permitidos para un documento
     */
    public function getAllowedConversions(string $sourceType, int $documentId): JsonResponse
    {
        $conversions = $this->conversionService->getAllowedConversions($sourceType, $documentId);
        return response()->json($conversions);
    }

    /**
     * Obtener documentos relacionados por conversión
     */
    public function getRelatedConversions(string $sourceType, int $documentId): JsonResponse
    {
        $related = $this->conversionService->getRelatedConversions($sourceType, $documentId);
        return response()->json($related);
    }
}
