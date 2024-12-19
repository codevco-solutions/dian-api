<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

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
        $role = $user->roles->first();  // Cambio aquí para obtener el primer rol
        
        if (!$role) {
            return response()->json([
                'message' => 'User has no role assigned'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($role->name === 'super-admin') {
            $users = $this->userService->getAll();
        } elseif ($role->name === 'company-admin') {
            if (!$user->company_id) {
                return response()->json([
                    'message' => 'Company admin has no company assigned'
                ], Response::HTTP_FORBIDDEN);
            }
            $users = $this->userService->getAllByCompany($user->company_id);
        } else {
            if (!$user->branch_id) {
                return response()->json([
                    'message' => 'User has no branch assigned'
                ], Response::HTTP_FORBIDDEN);
            }
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
        $role = $user->roles->first();

        // Validar que el usuario tenga permisos para crear usuarios
        if (!$role || !in_array($role->name, ['super-admin', 'company-admin'])) {
            return response()->json([
                'message' => 'Unauthorized to create users'
            ], Response::HTTP_FORBIDDEN);
        }

        // Si es admin de compañía, solo puede crear usuarios para su compañía
        if ($role->name === 'company-admin' && $request->company_id != $user->company_id) {
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

        // Establecer valores por defecto
        $data = $validator->validated();
        $data['is_active'] = $data['is_active'] ?? true;
        $data['settings'] = $data['settings'] ?? [];

        $user = $this->userService->create($data);

        // Asignar el rol al usuario
        $role = Role::findById($data['role_id']);
        $user->assignRole($role);

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
            $role = $authUser->roles->first();

            // Verificar permisos
            if (!$role) {
                return response()->json([
                    'message' => 'Unauthorized to view user'
                ], Response::HTTP_FORBIDDEN);
            }

            if ($role->name === 'super-admin') {
                // Super admin puede ver cualquier usuario
            } elseif ($role->name === 'company-admin') {
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
            $role = $authUser->roles->first();

            // Verificar permisos
            if (!$role) {
                return response()->json([
                    'message' => 'Unauthorized to update user'
                ], Response::HTTP_FORBIDDEN);
            }

            if ($role->name === 'super-admin') {
                // Super admin puede actualizar cualquier usuario
            } elseif ($role->name === 'company-admin') {
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
            $role = $authUser->roles->first();

            // Verificar permisos
            if (!$role) {
                return response()->json([
                    'message' => 'Unauthorized to delete users'
                ], Response::HTTP_FORBIDDEN);
            }

            if ($role->name === 'company-admin' && $userToDelete->company_id !== $authUser->company_id) {
                return response()->json([
                    'message' => 'Unauthorized to delete this user'
                ], Response::HTTP_FORBIDDEN);
            }

            $this->userService->delete($id);

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
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
        $users = $this->userService->getAllByBranch($branchId);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => UserResource::collection($users)
        ]);
    }
}
