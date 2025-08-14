<?php

use App\Mail\ContractNotification;
use App\Models\Contrat;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('universal', []);
    })->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->call(function () {
            $contrats = Contrat::where('status', 'pending_organizer')
                ->where('created_at', '>=', now()->subHours(24))
                ->get();

            foreach ($contrats as $contrat) {
                $signedUrl = URL::temporarySignedRoute(
                    'contract.review',
                    now()->addHours(48),
                    ['contrat' => $contrat->id]
                );

                Mail::to($contrat->prestation->contact_organisateur)
                    ->queue(new ContractNotification($contrat, $signedUrl));
            }
        })->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
       /* $exceptions->report(function (TenantCouldNotBeIdentifiedOnDomainException $e) {
            abort(403 , 'Client introuvable');
        });*/
    })->create();
