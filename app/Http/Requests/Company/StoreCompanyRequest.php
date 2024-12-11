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
            'business_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'tax_id' => 'required|string|unique:companies,tax_id,' . $companyId,
            'tax_regime' => 'required|string',
            'economic_activity' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email|unique:companies,email,' . $companyId,
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
            'business_name.required' => 'El nombre de la empresa es requerido',
            'tax_id.required' => 'El NIT es requerido',
            'tax_id.unique' => 'El NIT ya está registrado',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'El correo electrónico ya está registrado',
            'subdomain.required' => 'El subdominio es requerido',
            'subdomain.unique' => 'El subdominio ya está registrado',
            'subdomain.regex' => 'El subdominio solo puede contener letras minúsculas, números y guiones',
        ];
    }
}
