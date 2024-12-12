<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\DocLogResource;
use App\Models\Document\DocLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DocLog::query();

        // Filtros
        if ($request->has('documentable_type')) {
            $query->where('documentable_type', $request->documentable_type);
        }

        if ($request->has('documentable_id')) {
            $query->where('documentable_id', $request->documentable_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $logs = $query->paginate($perPage);

        return DocLogResource::collection($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'type' => 'required|string',
            'status' => 'required|string',
            'message' => 'required|string',
            'metadata' => 'nullable|array'
        ]);

        $log = DocLog::create($validated);

        return new DocLogResource($log);
    }

    public function show(DocLog $log)
    {
        return new DocLogResource($log->load(['documentable']));
    }

    public function destroy(DocLog $log)
    {
        $log->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getDocumentLogs(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer'
        ]);

        $logs = DocLog::where('documentable_type', $validated['documentable_type'])
            ->where('documentable_id', $validated['documentable_id'])
            ->orderBy('created_at', 'desc')
            ->get();

        return DocLogResource::collection($logs);
    }
}
