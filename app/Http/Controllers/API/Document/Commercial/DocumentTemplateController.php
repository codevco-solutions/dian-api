<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Services\Document\Commercial\DocumentTemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    protected $templateService;

    public function __construct(DocumentTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Listar todas las plantillas
     */
    public function index(): JsonResponse
    {
        $templates = $this->templateService->getAllTemplates();
        return response()->json($templates);
    }

    /**
     * Crear nueva plantilla
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $template = $this->templateService->createTemplate($request->all());
            return response()->json($template, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Actualizar plantilla
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $success = $this->templateService->updateTemplate($id, $request->all());
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Eliminar plantilla
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->templateService->deleteTemplate($id);
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Obtener plantilla por defecto
     */
    public function getDefaultTemplate(string $documentType): JsonResponse
    {
        $template = $this->templateService->getDefaultTemplate($documentType);
        return response()->json($template);
    }

    /**
     * Establecer plantilla por defecto
     */
    public function setDefaultTemplate(int $id): JsonResponse
    {
        try {
            $success = $this->templateService->setDefaultTemplate($id);
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Duplicar plantilla
     */
    public function duplicate(int $id): JsonResponse
    {
        try {
            $template = $this->templateService->duplicateTemplate($id);
            return response()->json($template);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Validar datos contra plantilla
     */
    public function validateData(Request $request, int $id): JsonResponse
    {
        try {
            $errors = $this->templateService->validateData($id, $request->all());
            return response()->json(['errors' => $errors]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Activar/Desactivar plantilla
     */
    public function toggleActive(int $id): JsonResponse
    {
        try {
            $success = $this->templateService->toggleActive($id, request('active', true));
            return response()->json(['success' => $success]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
