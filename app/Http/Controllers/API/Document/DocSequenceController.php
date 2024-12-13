<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\DocSequenceResource;
use App\Models\Document\DocSequence;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocSequenceController extends Controller
{
    public function index(Request $request)
    {
        $query = DocSequence::query();

        // Filtros
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
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
        $sequences = $query->paginate($perPage);

        return DocSequenceResource::collection($sequences);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|string',
            'prefix' => 'nullable|string|max:10',
            'next_number' => 'required|integer|min:1',
            'padding' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $sequence = DocSequence::create($validated);

        return new DocSequenceResource($sequence);
    }

    public function show(DocSequence $sequence)
    {
        return new DocSequenceResource($sequence->load(['resolutions']));
    }

    public function update(Request $request, DocSequence $sequence)
    {
        $validated = $request->validate([
            'type' => 'string',
            'prefix' => 'nullable|string|max:10',
            'next_number' => 'integer|min:1',
            'padding' => 'integer|min:1|max:10',
            'is_active' => 'boolean'
        ]);

        $sequence->update($validated);

        return new DocSequenceResource($sequence);
    }

    public function destroy(DocSequence $sequence)
    {
        // Verificar si tiene resoluciones asociadas
        if ($sequence->resolutions()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar la secuencia porque tiene resoluciones asociadas'
            ], Response::HTTP_CONFLICT);
        }

        $sequence->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getNextNumber(DocSequence $sequence)
    {
        $number = $sequence->getNextNumber();
        $formattedNumber = $sequence->formatNumber($number);

        return response()->json([
            'next_number' => $number,
            'formatted_number' => $formattedNumber
        ]);
    }
}
