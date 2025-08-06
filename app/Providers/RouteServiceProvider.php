<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureLoginLimiter();
        $this->configureAdminLimiter();
    }

    protected function configureLoginLimiter(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip())->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return back()->with('error',
                        "Trop de tentatives depuis votre adresse. Veuillez réessayer dans $seconds secondes.");
                }),

                Limit::perMinute(3)->by($request->input('email'))->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return back()->with('error',
                        "Trop de tentatives pour cet email. Veuillez patienter $seconds secondes.");
                })
            ];
        });
    }

    protected function configureAdminLimiter(): void
    {
        RateLimiter::for('super-admin-login', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip())->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 60;
                    return back()->with('error',
                        "[Sécurité immédiate] Trop de requêtes. Attendez $seconds secondes.");
                }),

                Limit::perMinutes(5, 5)->by($request->ip())->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 300;
                    return back()->with('error',
                        "[Sécurité moyenne] Limite atteinte. Réessayez dans $seconds secondes.");
                }),

                Limit::perHour(20)->by($request->ip())->response(function (Request $request, array $headers) {
                    $seconds = $headers['Retry-After'] ?? 3600;
                    return back()->with('error',
                        "[Sécurité stricte] Compte bloqué temporairement. Temps restant: $seconds secondes.");
                })
            ];
        });
    }
}
