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
            'business_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['required', 'string', 'unique:companies'],
            'tax_regime' => ['required', 'string'],
            'economic_activity' => ['required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:companies'],
            'website' => ['nullable', 'url'],
            'subdomain' => ['required', 'string', 'unique:companies', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
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
            'subdomain.required' => 'El subdominio es requerido',
            'subdomain.unique' => 'El subdominio ya está registrado',
            'subdomain.regex' => 'El subdominio solo puede contener letras minúsculas, números y guiones',
        ];
    }
}
