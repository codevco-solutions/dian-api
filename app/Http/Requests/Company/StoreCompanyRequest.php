<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company');
        
        return [
            'name' => 'required|string|max:255',
            'commercial_name' => 'nullable|string|max:255',
            'identification_type_id' => 'required|exists:identification_types,id',
            'identification_number' => 'required|string|unique:companies,identification_number,' . $companyId,
            'verification_code' => 'nullable|string',
            'organization_type_id' => 'required|exists:organization_types,id',
            'tax_regime_id' => 'required|exists:tax_regimes,id',
            'email' => 'required|email|unique:companies,email,' . $companyId,
            'phone' => 'nullable|string',
            'address' => 'required|string',
            'website' => 'nullable|url',
            'subdomain' => [
                'required',
                'string',
                'unique:companies,subdomain,' . $companyId,
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la empresa es requerido',
            'identification_number.required' => 'El número de identificación es requerido',
            'identification_number.unique' => 'El número de identificación ya está registrado',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'El correo electrónico ya está registrado',
            'subdomain.required' => 'El subdominio es requerido',
            'subdomain.unique' => 'El subdominio ya está registrado',
            'subdomain.regex' => 'El subdominio solo puede contener letras minúsculas, números y guiones',
        ];
    }
}
