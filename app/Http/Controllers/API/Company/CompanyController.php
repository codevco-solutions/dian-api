<?php

namespace App\Http\Controllers\API\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Services\Company\CompanyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        
        if ($role && $role->slug === 'super-admin') {
            $companies = $this->companyService->getAll();
        } else {
            // Si no es super admin, solo obtiene su propia compañía
            $company = $this->companyService->find($user->company_id);
            $companies = collect([$company]);
        }

        // Cargar la relación de sucursales
        $companies->load('branches');

        return response()->json([
            'message' => 'Companies retrieved successfully',
            'data' => CompanyResource::collection($companies)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $company = $this->companyService->create($request->validated());

        // Crear la sucursal principal
        $this->companyService->createMainBranch($company);

        // Cargar la relación de sucursales
        $company->load('branches');

        return response()->json([
            'message' => 'Company created successfully',
            'data' => new CompanyResource($company)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $company = $this->companyService->find($id);
            $company->load('branches');

            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $user->company_id !== $company->id) {
                return response()->json([
                    'message' => 'Unauthorized to view this company'
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'message' => 'Company retrieved successfully',
                'data' => new CompanyResource($company)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Primero verificamos si la empresa existe
            $existingCompany = $this->companyService->find($id);

            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $user->company_id !== $existingCompany->id) {
                return response()->json([
                    'message' => 'Unauthorized to update this company'
                ], Response::HTTP_FORBIDDEN);
            }

            // Si llegamos aquí, la empresa existe, ahora validamos los datos
            $validator = validator($request->all(), [
                'business_name' => 'sometimes|required|string|max:255',
                'trade_name' => 'sometimes|required|string|max:255',
                'tax_id' => 'sometimes|required|string|max:20|unique:companies,tax_id,' . $id,
                'tax_regime' => 'sometimes|required|string|max:50',
                'economic_activity' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'email' => 'sometimes|required|email|max:255|unique:companies,email,' . $id,
                'website' => 'sometimes|nullable|url|max:255',
                'subdomain' => 'sometimes|required|string|max:50|unique:companies,subdomain,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $company = $this->companyService->update($id, $validator->validated());
            $company->load('branches');

            return response()->json([
                'message' => 'Company updated successfully',
                'data' => new CompanyResource($company)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $company = $this->companyService->find($id);
            
            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $user->company_id !== $company->id) {
                return response()->json([
                    'message' => 'Unauthorized to delete this company'
                ], Response::HTTP_FORBIDDEN);
            }

            $this->companyService->delete($id);

            return response()->json([
                'message' => 'Company deleted successfully'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Find company by subdomain
     */
    public function findBySubdomain(string $subdomain)
    {
        try {
            $company = $this->companyService->findBySubdomain($subdomain);
            if (!$company) {
                return response()->json([
                    'message' => 'Company not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $user->company_id !== $company->id) {
                return response()->json([
                    'message' => 'Unauthorized to view this company'
                ], Response::HTTP_FORBIDDEN);
            }

            $company->load('branches');

            return response()->json([
                'message' => 'Company retrieved successfully',
                'data' => new CompanyResource($company)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving company'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Find company by tax ID
     */
    public function findByTaxId(string $taxId)
    {
        try {
            $company = $this->companyService->findByTaxId($taxId);
            if (!$company) {
                return response()->json([
                    'message' => 'Company not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $user->company_id !== $company->id) {
                return response()->json([
                    'message' => 'Unauthorized to view this company'
                ], Response::HTTP_FORBIDDEN);
            }

            $company->load('branches');

            return response()->json([
                'message' => 'Company retrieved successfully',
                'data' => new CompanyResource($company)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error retrieving company'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
