<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $companyId = $this->input('company_id');

        return $user->isSuperAdmin() || 
               ($user->isCompanyAdmin() && $user->company_id === $companyId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'unique:branches'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email'],
            'is_main' => ['boolean'],
            'settings' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'La compañía es requerida',
            'company_id.exists' => 'La compañía no existe',
            'name.required' => 'El nombre es requerido',
            'code.required' => 'El código es requerido',
            'code.unique' => 'El código ya está registrado',
            'address.required' => 'La dirección es requerida',
            'phone.required' => 'El teléfono es requerido',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'settings.array' => 'La configuración debe ser un arreglo',
        ];
    }
}
