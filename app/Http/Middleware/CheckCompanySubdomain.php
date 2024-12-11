<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $mainDomain = env('APP_DOMAIN', 'dian-api.test');
        
        // Si es el dominio principal, permitir el acceso
        if ($host === $mainDomain) {
            return $next($request);
        }

        // Obtener el subdominio
        $parts = explode('.', $host);
        if (count($parts) < 2) {
            return response()->json(['message' => 'Subdominio inválido'], 400);
        }

        $subdomain = $parts[0];

        // Buscar la compañía por el subdominio
        $company = Company::where('subdomain', $subdomain)
                        ->where('is_active', true)
                        ->first();

        if (!$company) {
            return response()->json([
                'message' => 'Compañía no encontrada o inactiva',
                'subdomain' => $subdomain
            ], 404);
        }

        // Agregar la compañía a la solicitud para uso posterior
        $request->merge(['company' => $company]);

        return $next($request);
    }
}
