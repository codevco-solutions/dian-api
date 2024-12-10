<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $companies = Company::with(['branches'])->get();
        } else {
            $companies = Company::where('id', $user->company_id)
                ->with(['branches'])
                ->get();
        }

        return response()->json([
            'message' => 'Empresas obtenidas exitosamente',
            'data' => $companies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'tax_id' => 'required|string|unique:companies',
            'tax_regime' => 'required|string',
            'economic_activity' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:companies',
            'website' => 'nullable|url',
            'subdomain' => 'required|string|unique:companies|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
        ]);

        $company = Company::create($request->all());

        // Crear la sucursal principal
        $company->branches()->create([
            'name' => 'Principal',
            'code' => 'MAIN-' . $company->id,
            'address' => $company->address,
            'phone' => $company->phone,
            'email' => $company->email,
            'is_main' => true,
        ]);

        return response()->json($company->load('mainBranch'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $company = Company::findOrFail($id);
            
            $user = auth()->user();
            
            if (!$user->isSuperAdmin() && $user->company_id !== $company->id) {
                return response()->json([
                    'message' => 'No tienes permiso para ver esta empresa'
                ], 403);
            }

            return response()->json($company->load(['branches', 'mainBranch']));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'La empresa no fue encontrada'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);
            
            $user = auth()->user();
            
            if (!$user->isSuperAdmin() && $user->company_id !== $company->id) {
                return response()->json([
                    'message' => 'No tienes permiso para actualizar esta empresa'
                ], 403);
            }

            $request->validate([
                'business_name' => 'required|string|max:255',
                'trade_name' => 'nullable|string|max:255',
                'tax_id' => 'required|string|unique:companies,tax_id,' . $company->id,
                'tax_regime' => 'required|string',
                'economic_activity' => 'required|string',
                'address' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email|unique:companies,email,' . $company->id,
                'website' => 'nullable|url',
                'subdomain' => 'required|string|unique:companies,subdomain,' . $company->id . '|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ]);

            $company->update($request->all());

            return response()->json($company->load('mainBranch'));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'La empresa no fue encontrada'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            
            $user = auth()->user();
            
            if (!$user->isSuperAdmin()) {
                return response()->json([
                    'message' => 'No tienes permiso para eliminar empresas'
                ], 403);
            }

            $company->delete();

            return response()->json([
                'message' => 'Empresa eliminada exitosamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'La empresa no fue encontrada'
            ], 404);
        }
    }
}
