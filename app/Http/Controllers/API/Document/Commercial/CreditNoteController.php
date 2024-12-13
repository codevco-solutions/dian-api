<?php

namespace App\Http\Controllers\API\Document\Commercial;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\Commercial\CreditNoteResource;
use App\Models\Document\Commercial\CreditNote;
use App\Services\Document\Commercial\CreditNoteService;
use App\Services\DianService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    protected $creditNoteService;
    protected $dianService;

    public function __construct(CreditNoteService $creditNoteService, DianService $dianService)
    {
        $this->creditNoteService = $creditNoteService;
        $this->dianService = $dianService;
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'status', 'date_from', 'date_to', 'customer_id', 'per_page'
            ]);

            $creditNotes = $this->creditNoteService->index(
                $request->user()->company_id,
                $filters
            );

            return response()->json([
                'data' => $creditNotes,
                'message' => 'Notas crédito obtenidas exitosamente'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las notas crédito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $creditNote = $this->creditNoteService->show($id);

            return response()->json([
                'data' => $creditNote,
                'message' => 'Nota crédito obtenida exitosamente'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la nota crédito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['company_id'] = $request->user()->company_id;

            $creditNote = $this->creditNoteService->store($data);

            return response()->json([
                'data' => $creditNote,
                'message' => 'Nota crédito creada exitosamente'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la nota crédito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $creditNote = $this->creditNoteService->update($id, $request->all());

            return response()->json([
                'data' => $creditNote,
                'message' => 'Nota crédito actualizada exitosamente'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la nota crédito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $this->creditNoteService->destroy($id);

            return response()->json([
                'message' => 'Nota crédito eliminada exitosamente'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la nota crédito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function approve($id)
    {
        try {
            $creditNote = $this->creditNoteService->approve($id);

            return response()->json([
                'data' => $creditNote,
                'message' => 'Nota crédito aprobada exitosamente'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al aprobar la nota crédito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $creditNote = $this->creditNoteService->cancel($id, $request->input('reason'));

            return response()->json([
                'data' => $creditNote,
                'message' => 'Nota crédito cancelada exitosamente'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cancelar la nota crédito',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function downloadPdf(CreditNote $creditNote)
    {
        $pdf = $creditNote->generatePdf();

        return response()->download(
            $pdf->getPath(),
            "nota_credito_{$creditNote->number}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadXml(CreditNote $creditNote)
    {
        $xml = $creditNote->generateXml();

        return response()->download(
            $xml->getPath(),
            "nota_credito_{$creditNote->number}.xml",
            ['Content-Type' => 'application/xml']
        );
    }
}
