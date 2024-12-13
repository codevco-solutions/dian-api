<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Services\Document\Commercial\DocumentStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentStateController extends Controller
{
    protected $stateService;

    public function __construct(DocumentStateService $stateService)
    {
        $this->stateService = $stateService;
    }

    /**
     * Listar todos los estados
     */
    public function index(): JsonResponse
    {
        $states = $this->stateService->getAllStates();
        return response()->json($states);
    }

    /**
     * Crear nuevo estado
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $state = $this->stateService->createState($request->all());
            return response()->json($state, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Actualizar estado
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $success = $this->stateService->updateState($id, $request->all());
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Eliminar estado
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->stateService->deleteState($id);
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Obtener estados disponibles para un tipo de documento
     */
    public function getAvailableStates(string $documentType): JsonResponse
    {
        $states = $this->stateService->getAvailableStates($documentType);
        return response()->json($states);
    }

    /**
     * Obtener transiciones disponibles desde un estado
     */
    public function getAvailableTransitions(int $stateId): JsonResponse
    {
        try {
            $transitions = $this->stateService->getAvailableTransitions($stateId);
            return response()->json($transitions);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Realizar transición de estado
     */
    public function transition(Request $request): JsonResponse
    {
        try {
            $this->stateService->transitionState(
                $request->input('document_type'),
                $request->input('document_id'),
                $request->input('from_state_id'),
                $request->input('to_state_id'),
                $request->except(['document_type', 'document_id', 'from_state_id', 'to_state_id'])
            );
            return response()->json(['success' => true]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
