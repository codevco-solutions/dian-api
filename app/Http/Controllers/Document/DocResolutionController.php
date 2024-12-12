<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\DocResolutionResource;
use App\Models\Document\DocResolution;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocResolutionController extends Controller
{
    public function index(Request $request)
    {
        $query = DocResolution::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->has('doc_sequence_id')) {
            $query->where('doc_sequence_id', $request->doc_sequence_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $resolutions = $query->paginate($perPage);

        return DocResolutionResource::collection($resolutions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'doc_sequence_id' => 'required|exists:doc_sequences,id',
            'resolution_number' => 'required|string',
            'type' => 'required|string',
            'resolution_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'prefix' => 'nullable|string|max:10',
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|gt:start_number',
            'technical_key' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $resolution = DocResolution::create($validated);

        return new DocResolutionResource($resolution);
    }

    public function show(DocResolution $resolution)
    {
        return new DocResolutionResource($resolution->load(['sequence']));
    }

    public function update(Request $request, DocResolution $resolution)
    {
        $validated = $request->validate([
            'resolution_number' => 'string',
            'type' => 'string',
            'resolution_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'prefix' => 'nullable|string|max:10',
            'start_number' => 'integer|min:1',
            'end_number' => 'integer|gt:start_number',
            'technical_key' => 'string',
            'is_active' => 'boolean'
        ]);

        $resolution->update($validated);

        return new DocResolutionResource($resolution);
    }

    public function destroy(DocResolution $resolution)
    {
        // Verificar si está siendo utilizada
        if ($resolution->isInUse()) {
            return response()->json([
                'message' => 'No se puede eliminar la resolución porque está siendo utilizada'
            ], Response::HTTP_CONFLICT);
        }

        $resolution->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getValidResolution(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|string'
        ]);

        $resolution = DocResolution::where('company_id', $validated['company_id'])
            ->where('type', $validated['type'])
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$resolution) {
            return response()->json([
                'message' => 'No se encontró una resolución válida'
            ], Response::HTTP_NOT_FOUND);
        }

        return new DocResolutionResource($resolution);
    }
}
