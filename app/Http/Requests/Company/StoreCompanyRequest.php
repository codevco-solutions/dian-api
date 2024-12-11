<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'trade_name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:20|unique:companies',
            'tax_regime' => 'required|string|max:50',
            'economic_activity' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:companies',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|string|max:255',
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'unique:companies',
                'regex:/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/',
            ],
            'is_active' => 'boolean',
            'dian_settings' => 'nullable|array'
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' => 'El nombre de la empresa es requerido',
            'tax_id.required' => 'El NIT es requerido',
            'tax_id.unique' => 'El NIT ya está registrado',
            'tax_regime.required' => 'El régimen tributario es requerido',
            'economic_activity.required' => 'La actividad económica es requerida',
            'address.required' => 'La dirección es requerida',
            'phone.required' => 'El teléfono es requerido',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'El correo electrónico ya está registrado',
            'website.url' => 'El sitio web debe ser una URL válida',
            'subdomain.required' => 'El subdominio es requerido.',
            'subdomain.unique' => 'Este subdominio ya está en uso.',
            'subdomain.regex' => 'El subdominio solo puede contener letras minúsculas, números y guiones. Debe comenzar y terminar con letra o número.',
            'subdomain.max' => 'El subdominio no puede tener más de 63 caracteres.'
        ];
    }
}
