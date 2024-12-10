<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Company; // Import the Company model

class CheckCompanySubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $mainDomain = config('app.main_domain', 'localhost');
        
        // Si es el dominio principal, permitir el acceso
        if ($host === $mainDomain) {
            return $next($request);
        }

        // Obtener el subdominio
        $subdomain = explode('.', $host)[0];

        // Buscar la compañía por el subdominio
        $company = Company::where('subdomain', $subdomain)->first();

        if (!$company) {
            return response()->json(['message' => 'Compañía no encontrada'], 404);
        }

        // Verificar si la compañía está activa
        if (!$company->is_active) {
            return response()->json(['message' => 'Compañía inactiva'], 403);
        }

        // Agregar la compañía a la solicitud para uso posterior
        $request->merge(['company' => $company]);

        return $next($request);
    }
}
