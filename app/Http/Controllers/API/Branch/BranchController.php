<?php

namespace App\Http\Controllers\API\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Resources\Branch\BranchResource;
use App\Services\Branch\BranchService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role && $role->slug === 'super-admin') {
            $branches = $this->branchService->getAll();
        } else {
            $branches = $this->branchService->getAllByCompany($user->company_id);
        }

        return response()->json([
            'message' => 'Branches retrieved successfully',
            'data' => BranchResource::collection($branches)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request)
    {
        $user = Auth::user();
        $role = $user->role;

        // Si no es super admin, solo puede crear sucursales para su propia empresa
        if (!($role && $role->slug === 'super-admin') && $request->company_id != $user->company_id) {
            return response()->json([
                'message' => 'Unauthorized to create branch for this company'
            ], Response::HTTP_FORBIDDEN);
        }

        $branch = $this->branchService->create($request->validated());

        return response()->json([
            'message' => 'Branch created successfully',
            'data' => new BranchResource($branch)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $branch = $this->branchService->find($id);

            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $branch->company_id != $user->company_id) {
                return response()->json([
                    'message' => 'Unauthorized to view this branch'
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->json([
                'message' => 'Branch retrieved successfully',
                'data' => new BranchResource($branch)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Branch not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $branch = $this->branchService->find($id);

            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $branch->company_id != $user->company_id) {
                return response()->json([
                    'message' => 'Unauthorized to update this branch'
                ], Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|max:20',
                'address' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'email' => 'sometimes|required|email|max:255',
                'is_main' => 'sometimes|boolean',
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $branch = $this->branchService->update($id, $validator->validated());

            return response()->json([
                'message' => 'Branch updated successfully',
                'data' => new BranchResource($branch)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Branch not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $branch = $this->branchService->find($id);

            $user = Auth::user();
            $role = $user->role;

            if (!($role && $role->slug === 'super-admin') && $branch->company_id != $user->company_id) {
                return response()->json([
                    'message' => 'Unauthorized to delete this branch'
                ], Response::HTTP_FORBIDDEN);
            }

            $this->branchService->delete($id);

            return response()->json([
                'message' => 'Branch deleted successfully'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Branch not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get all branches by company
     */
    public function getByCompany(string $companyId)
    {
        $user = Auth::user();
        $role = $user->role;

        if (!($role && $role->slug === 'super-admin') && $companyId != $user->company_id) {
            return response()->json([
                'message' => 'Unauthorized to view branches for this company'
            ], Response::HTTP_FORBIDDEN);
        }

        $branches = $this->branchService->getAllByCompany($companyId);

        return response()->json([
            'message' => 'Branches retrieved successfully',
            'data' => BranchResource::collection($branches)
        ]);
    }
}
