<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User;
use App\Http\Requests\Auth\RegisterRequest;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Verificar si el usuario está activo
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Usuario inactivo'
            ], 401);
        }

        // Cargar relaciones necesarias
        $user->load(['roles', 'company', 'branch']);

        // Crear token con el nombre del dispositivo
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Usuario loggeado exitosamente',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['roles', 'company', 'branch']);

        return response()->json([
            'message' => 'User information retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'settings' => $user->settings,
                'roles' => $user->roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name
                    ];
                }),
                'company' => $user->company ? [
                    'id' => $user->company->id,
                    'name' => $user->company->name
                ] : null,
                'branch' => $user->branch ? [
                    'id' => $user->branch->id,
                    'name' => $user->branch->name
                ] : null
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $data['is_active'] = true;

        $user = User::create($data);
        
        // Asignar el rol al usuario
        $role = Role::findById($data['role_id']);
        $user->assignRole($role);

        $token = $user->createToken('auth-token')->plainTextToken;

        // Cargar las relaciones necesarias
        $user->load(['roles', 'company', 'branch']);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
        ], 201);
    }
}
