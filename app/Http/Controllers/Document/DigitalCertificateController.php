<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Http\Resources\Document\DigitalCertificateResource;
use App\Models\Document\DigitalCertificate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DigitalCertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = DigitalCertificate::query();

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
        $certificates = $query->paginate($perPage);

        return DigitalCertificateResource::collection($certificates);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string',
            'type' => 'required|string',
            'certificate' => 'required|string',
            'password' => 'required|string',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'pin' => 'nullable|string',
            'software_id' => 'required|string',
            'is_active' => 'boolean'
        ]);

        // Encriptar datos sensibles
        $validated['certificate'] = encrypt($validated['certificate']);
        $validated['password'] = encrypt($validated['password']);
        if (isset($validated['pin'])) {
            $validated['pin'] = encrypt($validated['pin']);
        }

        $certificate = DigitalCertificate::create($validated);

        return new DigitalCertificateResource($certificate);
    }

    public function show(DigitalCertificate $certificate)
    {
        return new DigitalCertificateResource($certificate);
    }

    public function update(Request $request, DigitalCertificate $certificate)
    {
        $validated = $request->validate([
            'name' => 'string',
            'type' => 'string',
            'certificate' => 'string',
            'password' => 'string',
            'valid_from' => 'date',
            'valid_to' => 'date|after:valid_from',
            'pin' => 'nullable|string',
            'software_id' => 'string',
            'is_active' => 'boolean'
        ]);

        // Encriptar datos sensibles si se actualizan
        if (isset($validated['certificate'])) {
            $validated['certificate'] = encrypt($validated['certificate']);
        }
        if (isset($validated['password'])) {
            $validated['password'] = encrypt($validated['password']);
        }
        if (isset($validated['pin'])) {
            $validated['pin'] = encrypt($validated['pin']);
        }

        $certificate->update($validated);

        return new DigitalCertificateResource($certificate);
    }

    public function destroy(DigitalCertificate $certificate)
    {
        $certificate->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getValidCertificate(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type' => 'required|string'
        ]);

        $certificate = DigitalCertificate::where('company_id', $validated['company_id'])
            ->where('type', $validated['type'])
            ->where('is_active', true)
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_to', '>=', now())
            ->first();

        if (!$certificate) {
            return response()->json([
                'message' => 'No se encontró un certificado válido'
            ], Response::HTTP_NOT_FOUND);
        }

        return new DigitalCertificateResource($certificate);
    }
}
