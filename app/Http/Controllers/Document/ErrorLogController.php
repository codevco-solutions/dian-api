<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\ErrorLogResource;
use App\Models\Document\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ErrorLog::query();

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

        if ($request->has('code')) {
            $query->where('code', $request->code);
        }

        // Ordenamiento
        $query->orderBy($request->get('sort_by', 'id'), $request->get('sort_order', 'desc'));

        // Paginación
        $perPage = $request->get('per_page', 10);
        $logs = $query->paginate($perPage);

        return ErrorLogResource::collection($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'type' => 'required|string',
            'code' => 'required|string',
            'message' => 'required|string',
            'trace' => 'required|string',
            'context' => 'nullable|array'
        ]);

        $log = ErrorLog::create($validated);

        return new ErrorLogResource($log);
    }

    public function show(ErrorLog $log)
    {
        return new ErrorLogResource($log->load(['documentable']));
    }

    public function destroy(ErrorLog $log)
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

        $logs = ErrorLog::where('documentable_type', $validated['documentable_type'])
            ->where('documentable_id', $validated['documentable_id'])
            ->orderBy('created_at', 'desc')
            ->get();

        return ErrorLogResource::collection($logs);
    }

    public function getLastError(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer'
        ]);

        $log = ErrorLog::where('documentable_type', $validated['documentable_type'])
            ->where('documentable_id', $validated['documentable_id'])
            ->latest()
            ->first();

        if (!$log) {
            return response()->json([
                'message' => 'No se encontraron errores'
            ], Response::HTTP_NOT_FOUND);
        }

        return new ErrorLogResource($log);
    }
}
