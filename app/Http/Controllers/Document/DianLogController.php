<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\DianLogResource;
use App\Models\Document\DianLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DianLogController extends Controller
{
    public function index(Request $request)
    {
        $query = DianLog::query();

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

        return DianLogResource::collection($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'type' => 'required|string',
            'status' => 'required|string',
            'request' => 'required|array',
            'response' => 'required|array',
            'tracking_id' => 'nullable|string'
        ]);

        $log = DianLog::create($validated);

        return new DianLogResource($log);
    }

    public function show(DianLog $log)
    {
        return new DianLogResource($log->load(['documentable']));
    }

    public function destroy(DianLog $log)
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

        $logs = DianLog::where('documentable_type', $validated['documentable_type'])
            ->where('documentable_id', $validated['documentable_id'])
            ->orderBy('created_at', 'desc')
            ->get();

        return DianLogResource::collection($logs);
    }

    public function getLastLog(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer'
        ]);

        $log = DianLog::where('documentable_type', $validated['documentable_type'])
            ->where('documentable_id', $validated['documentable_id'])
            ->latest()
            ->first();

        if (!$log) {
            return response()->json([
                'message' => 'No se encontraron logs'
            ], Response::HTTP_NOT_FOUND);
        }

        return new DianLogResource($log);
    }
}
