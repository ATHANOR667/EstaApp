<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    'universal',
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\Tenant\CheckTenantStatus::class,
    Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,

])->group(function () {


    /** ROUTES DU PATRON */
    Route::name('patron.')
        ->prefix('patron')
        ->group(function () {

        /** CONNECTED */
        Route::controller(App\Http\Controllers\Tenant\Patron\AuthController::class)
            ->group(function (){
                Route::get('/login','loginView')->name('loginView');
                Route::post('/login','login')->name('login');

                Route::delete('/logout','logout')->name('logout');

            });
        /** DISCONNECTED */
        Route::controller(App\Http\Controllers\Tenant\Patron\BaseController::class)
            ->middleware([ App\Http\Middleware\Tenant\PatronMiddleware::class ])
            ->group(function (){
                Route::get('/panel','patronPanel')->name('patronPanel');
            });

    });




    /** ROUTES EMPLOYEE */

    Route::name('employee.')
        ->group(function (){

            /** AUTHENTICATION  */

            Route::name('auth.')
                ->prefix('auth')
                ->controller(App\Http\Controllers\Employee\AuthController::class)
                ->group(function (){

                    /**  WHILE DISCONNECTED */

                    Route::name('disconnected.')
                        ->prefix('disconnected')
                        ->group(function (){
                            Route::get('login','loginView')->name('loginView');
                            Route::post('login','login')->name('login');

                            Route::get('signup','signupView')->name('signupView');
                            Route::post('signup','signup')->name('signup');
                        });


                    /**  WHILE CONNECTED */

                    Route::name('connected.')
                        ->prefix('connected')
                        ->middleware([ \App\Http\Middleware\Tenant\EmployeeMiddleware::class ])
                        ->group(function (){
                            Route::delete('logout','logout')->name('logout');
                        });
                });



            /** SIMPLES ROUTES */

            Route::controller(App\Http\Controllers\Employee\BaseController::class)
                ->middleware([ \App\Http\Middleware\Tenant\EmployeeMiddleware::class ])
                ->group(function (){
                    Route::get('/','profileView')->name('profileView');
                    Route::get('/profile','profileView')->name('profileView');
                    Route::get('/manage-events','manageEventsView')->name('manageEvents');
                    Route::get('/manage-tickets/{event:title}','manageTicketsView')->name('manageTickets');
                });
        });


});
