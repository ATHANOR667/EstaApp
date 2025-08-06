<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    /**
     * Gère une requête entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenancy()->tenant;

        if (!$tenant) {
            abort(403, "Organisation introuvable (aucune donnée de tenant).");
        }

        if ($tenant->validation_status === 'pending') {
            abort(403, "Votre organisation est actuellement en cours de validation. Veuillez patienter.");
        }

        if ($tenant->validation_status == 'rejected') {
            abort(403, "Votre organisation a été rejetée. Veuillez contacter le support.");
        }

        if ($tenant->activation_status !== 'enabled') {
            abort(403, "Votre organisation est actuellement désactivée. Veuillez contacter le support si ce n'est pas de votre fait.");
        }

        return $next($request);
    }
}
