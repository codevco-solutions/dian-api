<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        
        if ($role && $role->slug === 'super-admin') {
            $users = $this->userService->getAll();
        } elseif ($role && $role->slug === 'company-admin') {
            $users = $this->userService->getAllByCompany($user->company_id);
        } else {
            $users = $this->userService->getAllByBranch($user->branch_id);
        }

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;

        // Validar que el usuario tenga permisos para crear usuarios
        if (!($role && ($role->slug === 'super-admin' || $role->slug === 'company-admin'))) {
            return response()->json([
                'message' => 'Unauthorized to create users'
            ], Response::HTTP_FORBIDDEN);
        }

        // Si es admin de compañía, solo puede crear usuarios para su compañía
        if ($role->slug === 'company-admin' && $request->company_id != $user->company_id) {
            return response()->json([
                'message' => 'Unauthorized to create users for other companies'
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'settings' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->userService->create($validator->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data' => new UserResource($user)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->userService->find($id);
            
            $authUser = Auth::user();
            $role = $authUser->role;

            // Verificar permisos
            if (!$role) {
                return response()->json([
                    'message' => 'Unauthorized to view user'
                ], Response::HTTP_FORBIDDEN);
            }

            if ($role->slug === 'super-admin') {
                // Super admin puede ver cualquier usuario
            } elseif ($role->slug === 'company-admin') {
                // Admin de compañía solo puede ver usuarios de su compañía
                if ($user->company_id !== $authUser->company_id) {
                    return response()->json([
                        'message' => 'Unauthorized to view this user'
                    ], Response::HTTP_FORBIDDEN);
                }
            } else {
                // Usuario normal solo puede verse a sí mismo
                if ($user->id !== $authUser->id) {
                    return response()->json([
                        'message' => 'Unauthorized to view this user'
                    ], Response::HTTP_FORBIDDEN);
                }
            }

            return response()->json([
                'message' => 'User retrieved successfully',
                'data' => new UserResource($user)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $userToUpdate = $this->userService->find($id);
            
            $authUser = Auth::user();
            $role = $authUser->role;

            // Verificar permisos
            if (!$role) {
                return response()->json([
                    'message' => 'Unauthorized to update user'
                ], Response::HTTP_FORBIDDEN);
            }

            if ($role->slug === 'super-admin') {
                // Super admin puede actualizar cualquier usuario
            } elseif ($role->slug === 'company-admin') {
                // Admin de compañía solo puede actualizar usuarios de su compañía
                if ($userToUpdate->company_id !== $authUser->company_id) {
                    return response()->json([
                        'message' => 'Unauthorized to update this user'
                    ], Response::HTTP_FORBIDDEN);
                }
            } else {
                // Usuario normal solo puede actualizarse a sí mismo
                if ($userToUpdate->id !== $authUser->id) {
                    return response()->json([
                        'message' => 'Unauthorized to update this user'
                    ], Response::HTTP_FORBIDDEN);
                }
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:8',
                'company_id' => 'sometimes|required|exists:companies,id',
                'branch_id' => 'sometimes|required|exists:branches,id',
                'role_id' => 'sometimes|required|exists:roles,id',
                'phone' => 'nullable|string|max:20',
                'is_active' => 'sometimes|boolean',
                'settings' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = $this->userService->update($id, $validator->validated());

            return response()->json([
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userToDelete = $this->userService->find($id);
            
            $authUser = Auth::user();
            $role = $authUser->role;

            // Verificar permisos
            if (!$role) {
                return response()->json([
                    'message' => 'Unauthorized to delete user'
                ], Response::HTTP_FORBIDDEN);
            }

            if ($role->slug === 'super-admin') {
                // Super admin puede eliminar cualquier usuario
            } elseif ($role->slug === 'company-admin') {
                // Admin de compañía solo puede eliminar usuarios de su compañía
                if ($userToDelete->company_id !== $authUser->company_id) {
                    return response()->json([
                        'message' => 'Unauthorized to delete this user'
                    ], Response::HTTP_FORBIDDEN);
                }
            } else {
                return response()->json([
                    'message' => 'Unauthorized to delete users'
                ], Response::HTTP_FORBIDDEN);
            }

            $this->userService->delete($id);

            return response()->json([
                'message' => 'User deleted successfully'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get users by company
     */
    public function getByCompany(string $companyId)
    {
        $user = Auth::user();
        $role = $user->role;
        
        if (!($role && ($role->slug === 'super-admin' || ($role->slug === 'company-admin' && $user->company_id == $companyId)))) {
            return response()->json([
                'message' => 'Unauthorized to view users for this company'
            ], Response::HTTP_FORBIDDEN);
        }

        $users = $this->userService->getAllByCompany($companyId);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Get users by branch
     */
    public function getByBranch(string $branchId)
    {
        $user = Auth::user();
        $role = $user->role;
        
        if (!($role && ($role->slug === 'super-admin' || $role->slug === 'company-admin'))) {
            return response()->json([
                'message' => 'Unauthorized to view users for this branch'
            ], Response::HTTP_FORBIDDEN);
        }

        $users = $this->userService->getAllByBranch($branchId);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users)
        ]);
    }
}
