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
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Traits\HasRoles;

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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get pagination parameters
        $perPage = $request->input('per_page', 15);
        
        // Get filter parameters
        $filters = $request->only([
            'business_name',
            'commercial_name',
            'nit',
            'address',
            'is_active'
        ]);
        
        // Get sorting parameters
        $orderBy = [];
        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction', 'asc');
            $orderBy[$sortBy] = $sortDirection;
        } else {
            $orderBy['created_at'] = 'desc';
        }

        if ($user->hasRole('super-admin')) {
            $companies = $this->companyService->getAll($perPage, $filters, $orderBy);
        } else {
            // Si no es super admin, verificar si tiene una compañía asignada
            if (!$user->company_id) {
                return response()->json([
                    'message' => 'No company assigned to this user',
                    'data' => []
                ]);
            }
            
            $company = $this->companyService->find($user->company_id);
            if (!$company) {
                return response()->json([
                    'message' => 'Company not found',
                    'data' => []
                ], 404);
            }
            
            // Crear una colección paginada con la única compañía
            $companies = new \Illuminate\Pagination\LengthAwarePaginator(
                [$company],
                1,
                $perPage,
                1
            );
        }

        // Cargar la relación de sucursales
        $companies->load('branches');

        return response()->json([
            'message' => 'Companies retrieved successfully',
            'data' => CompanyResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'from' => $companies->firstItem(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'to' => $companies->lastItem(),
                'total' => $companies->total(),
            ]
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

            if (!$user->hasRole('super-admin') && $user->company_id !== $company->id) {
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

            if (!$user->hasRole('super-admin') && $user->company_id !== $existingCompany->id) {
                return response()->json([
                    'message' => 'Unauthorized to update this company'
                ], Response::HTTP_FORBIDDEN);
            }

            // Validamos los datos
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'commercial_name' => 'sometimes|string|max:255',
                'identification_type_id' => 'sometimes|exists:identification_types,id',
                'identification_number' => 'sometimes|string|max:20|unique:companies,identification_number,' . $id,
                'verification_code' => 'sometimes|string|max:1',
                'organization_type_id' => 'sometimes|exists:organization_types,id',
                'tax_regime_id' => 'sometimes|exists:tax_regimes,id',
                'email' => 'sometimes|email|max:255|unique:companies,email,' . $id,
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:255',
                'website' => 'sometimes|nullable|url|max:255',
                'subdomain' => 'sometimes|string|max:63|unique:companies,subdomain,' . $id,
                'is_active' => 'sometimes|boolean',
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

            if (!$user->hasRole('super-admin') && $user->company_id !== $company->id) {
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

            if (!$user->hasRole('super-admin') && $user->company_id !== $company->id) {
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

            if (!$user->hasRole('super-admin') && $user->company_id !== $company->id) {
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
