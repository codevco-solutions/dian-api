<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        $role = $user->role;
        $userToUpdate = $this->route('user');

        if ($role && $role->slug === 'super-admin') {
            return true;
        }

        if ($role && $role->slug === 'company-admin') {
            return $userToUpdate->company_id === $user->company_id;
        }

        return $userToUpdate->id === $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $this->route('user'),
            'password' => 'sometimes|required|string|min:8',
            'company_id' => 'sometimes|required|exists:companies,id',
            'branch_id' => 'sometimes|required|exists:branches,id',
            'role_id' => 'sometimes|required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
            'settings' => 'nullable|array'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'El correo electrónico ya está en uso',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'company_id.required' => 'La empresa es requerida',
            'company_id.exists' => 'La empresa seleccionada no existe',
            'branch_id.required' => 'La sucursal es requerida',
            'branch_id.exists' => 'La sucursal seleccionada no existe',
            'role_id.required' => 'El rol es requerido',
            'role_id.exists' => 'El rol seleccionado no existe',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres',
            'settings.array' => 'La configuración debe ser un arreglo'
        ];
    }
}
