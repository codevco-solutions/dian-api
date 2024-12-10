<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $request->query('company_id');
        $includeDeleted = $request->query('include_deleted', false);

        Log::info('User info:', [
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'is_super_admin' => $user->isSuperAdmin(),
            'requested_company_id' => $companyId
        ]);

        $query = Branch::with('company');

        // Incluir eliminados si se solicita
        if ($includeDeleted) {
            $query->withTrashed();
        }

        // Si es super admin
        if ($user->isSuperAdmin()) {
            // Si se proporciona company_id, filtrar por esa compañía
            if ($companyId) {
                $company = Company::find($companyId);
                if (!$company) {
                    return response()->json([
                        'message' => 'La empresa no fue encontrada'
                    ], 404);
                }
                $query->where('company_id', $companyId);
            }
            // Si no se proporciona company_id, mostrar todas las sucursales
        } else {
            // Para usuarios normales, mostrar solo las sucursales de su compañía
            $query->where('company_id', $user->company_id);
        }

        $branches = $query->get();

        Log::info('Branches query result:', [
            'count' => $branches->count(),
            'user_type' => $user->isSuperAdmin() ? 'super_admin' : 'normal',
            'filtered_by_company' => !empty($companyId),
            'include_deleted' => $includeDeleted
        ]);

        if ($branches->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron sucursales',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Sucursales obtenidas exitosamente',
            'data' => $branches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Verificar si el usuario es super admin o admin de la compañía
        if (!$user->isSuperAdmin() && !$user->isCompanyAdmin()) {
            return response()->json([
                'message' => 'No tienes permiso para crear sucursales'
            ], 403);
        }

        try {
            $request->validate([
                'company_id' => [
                    'required',
                    Rule::exists('companies', 'id')->whereNull('deleted_at'),
                ],
                'name' => 'required|string|max:255',
                'code' => 'required|string|unique:branches',
                'address' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }

        // Si no es super admin, verificar que la compañía sea la suya
        if (!$user->isSuperAdmin() && $user->company_id !== $request->company_id) {
            return response()->json([
                'message' => 'No tienes permiso para crear sucursales en esta empresa'
            ], 403);
        }

        // Verificar si la compañía existe y no está eliminada
        $company = Company::find($request->company_id);
        if (!$company) {
            return response()->json([
                'message' => 'La empresa no fue encontrada'
            ], 404);
        }

        // Todas las sucursales son principales por defecto
        $data = $request->all();
        $data['is_main'] = true;

        $branch = Branch::create($data);

        Log::info('Created new branch', [
            'company_id' => $request->company_id,
            'branch_id' => $branch->id,
            'is_main' => true
        ]);

        return response()->json([
            'message' => 'Sucursal creada exitosamente',
            'data' => $branch
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $branch = Branch::find($id);
        
        if (!$branch) {
            return response()->json([
                'message' => 'La sucursal no fue encontrada'
            ], 404);
        }

        if (!$user->isSuperAdmin() && $user->company_id !== $branch->company_id) {
            return response()->json([
                'message' => 'No tienes permiso para ver esta sucursal'
            ], 403);
        }

        return response()->json([
            'message' => 'Sucursal obtenida exitosamente',
            'data' => $branch
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        $branch = Branch::find($id);
        
        if (!$branch) {
            return response()->json([
                'message' => 'La sucursal no fue encontrada'
            ], 404);
        }

        if (!$user->isSuperAdmin() && $user->company_id !== $branch->company_id) {
            return response()->json([
                'message' => 'No tienes permiso para actualizar esta sucursal'
            ], 403);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => [
                    'required',
                    'string',
                    Rule::unique('branches')->ignore($branch->id)
                ],
                'address' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email',
                'is_main' => 'boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }

        $data = $request->all();

        // Si se está cambiando is_main
        if ($request->has('is_main')) {
            $newIsMain = $request->boolean('is_main');
            
            // Si se está estableciendo como principal
            if ($newIsMain && !$branch->is_main) {
                Branch::where('company_id', $branch->company_id)
                    ->where('is_main', true)
                    ->update(['is_main' => false]);
            }
            // Si se está quitando como principal, verificar que haya otra sucursal principal
            elseif (!$newIsMain && $branch->is_main) {
                $hasOtherMainBranch = Branch::where('company_id', $branch->company_id)
                    ->where('id', '!=', $branch->id)
                    ->where('is_main', true)
                    ->exists();

                if (!$hasOtherMainBranch) {
                    return response()->json([
                        'message' => 'No se puede quitar como principal porque no hay otra sucursal principal'
                    ], 422);
                }
            }
        }

        $branch->update($data);

        return response()->json([
            'message' => 'Sucursal actualizada exitosamente',
            'data' => $branch
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        $branch = Branch::find($id);
        
        if (!$branch) {
            return response()->json([
                'message' => 'La sucursal no fue encontrada'
            ], 404);
        }

        if (!$user->isSuperAdmin() && $user->company_id !== $branch->company_id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta sucursal'
            ], 403);
        }

        // Verificar si es la primera sucursal de la compañía
        $firstBranch = Branch::where('company_id', $branch->company_id)
            ->orderBy('created_at')
            ->first();

        if ($branch->id === $firstBranch->id) {
            return response()->json([
                'message' => 'No se puede eliminar la sucursal principal original'
            ], 400);
        }

        $branch->delete();

        return response()->json([
            'message' => 'Sucursal eliminada exitosamente'
        ], 200);
    }
}
