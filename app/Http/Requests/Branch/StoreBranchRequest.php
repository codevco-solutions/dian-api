<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $branchId = $this->route('branch');
        
        return [
            'company_id' => [
                'required',
                Rule::exists('companies', 'id')->whereNull('deleted_at'),
            ],
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                Rule::unique('branches')->ignore($branchId)
            ],
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'is_main' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'La empresa es requerida',
            'company_id.exists' => 'La empresa seleccionada no existe',
            'name.required' => 'El nombre es requerido',
            'code.required' => 'El código es requerido',
            'code.unique' => 'El código ya está registrado',
            'address.required' => 'La dirección es requerida',
            'phone.required' => 'El teléfono es requerido',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido'
        ];
    }
}
