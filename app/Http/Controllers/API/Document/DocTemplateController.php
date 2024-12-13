<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\DocTemplateResource;
use App\Models\Document\DocTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = DocTemplate::query();

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

        if ($request->has('is_default')) {
            $query->where('is_default', $request->boolean('is_default'));
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $templates = $query->paginate($perPage);

        return DocTemplateResource::collection($templates);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|string',
            'name' => 'required|string',
            'header' => 'nullable|string',
            'footer' => 'nullable|string',
            'body' => 'required|string',
            'styles' => 'nullable|array',
            'is_default' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Si es default, desactivar otros templates default del mismo tipo
        if ($validated['is_default'] ?? false) {
            DocTemplate::where('company_id', $validated['company_id'])
                ->where('type', $validated['type'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template = DocTemplate::create($validated);

        return new DocTemplateResource($template);
    }

    public function show(DocTemplate $template)
    {
        return new DocTemplateResource($template);
    }

    public function update(Request $request, DocTemplate $template)
    {
        $validated = $request->validate([
            'type' => 'string',
            'name' => 'string',
            'header' => 'nullable|string',
            'footer' => 'nullable|string',
            'body' => 'string',
            'styles' => 'nullable|array',
            'is_default' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Si se está estableciendo como default, desactivar otros templates default
        if (isset($validated['is_default']) && $validated['is_default']) {
            DocTemplate::where('company_id', $template->company_id)
                ->where('type', $template->type)
                ->where('id', '!=', $template->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $template->update($validated);

        return new DocTemplateResource($template);
    }

    public function destroy(DocTemplate $template)
    {
        // No permitir eliminar si es el único template default activo
        if ($template->is_default && $template->is_active) {
            $hasOtherDefault = DocTemplate::where('company_id', $template->company_id)
                ->where('type', $template->type)
                ->where('id', '!=', $template->id)
                ->where('is_default', true)
                ->where('is_active', true)
                ->exists();

            if (!$hasOtherDefault) {
                return response()->json([
                    'message' => 'No se puede eliminar el template porque es el único template default activo'
                ], Response::HTTP_CONFLICT);
            }
        }

        $template->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getDefaultTemplate(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|string'
        ]);

        $template = DocTemplate::where('company_id', $validated['company_id'])
            ->where('type', $validated['type'])
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return response()->json([
                'message' => 'No se encontró un template default activo'
            ], Response::HTTP_NOT_FOUND);
        }

        return new DocTemplateResource($template);
    }
}
