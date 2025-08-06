<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OrganizerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('organizer')->check()) {
            return redirect()
                ->route('organization.organizer.auth.disconnected.login')
                ->with('message','Veuillez vous connecter');
        }
        return $next($request);
    }
}
