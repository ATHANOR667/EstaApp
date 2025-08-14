<?php

use App\Http\Controllers\ContratController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->group(function () {
    Broadcast::routes();
});
Route::get('/contrats/download/{contrat}', [ContratController::class, 'downloadPdf'])->name('contrats.download_pdf')->middleware('signed');

/** ROUTES SUPER-ADMIN */

Route::name('super-admin.')
    ->prefix('super-admin')
    ->group(function (){

        /** AUTHENTICATION  */

        Route::name('auth.')
            ->prefix('auth')
            ->controller(App\Http\Controllers\SuperAdmin\AuthController::class)
            ->group(function (){

                /**  WHILE DISCONNECTED */

                Route::name('disconnected.')
                    ->prefix('disconnected')
                    ->group(function (){
                        Route::get('login','loginView')->name('loginView');
                        Route::post('login','login')
                            ->middleware('throttle:super-admin-login')
                            ->name('login');
                    });


                /**  WHILE CONNECTED */

                Route::name('connected.')
                    ->prefix('connected')
                    ->middleware([ App\Http\Middleware\SuperAdminMiddleware::class ])
                    ->group(function (){
                        Route::delete('logout','logout')->name('logout');
                    });
            });






        /** SIMPLES ROUTES */

        Route::controller(App\Http\Controllers\SuperAdmin\BaseController::class)
            ->middleware([ App\Http\Middleware\SuperAdminMiddleware::class ])
            ->group(function (){
                Route::get('/','profileView')->name('profileView');
                Route::get('/profile','profileView')->name('profileView');
                Route::get('/manage-admins','manageAdminsView')->name('manageAdminsView');
            });





    });




/** ROUTES ADMIN */

Route::name('admin.')
    ->prefix('admin')
    ->group(function (){

        /** AUTHENTICATION  */

        Route::name('auth.')
            ->prefix('auth')
            ->controller(App\Http\Controllers\Admin\AuthController::class)
            ->group(function (){

                /**  WHILE DISCONNECTED */

                Route::name('disconnected.')
                    ->prefix('disconnected')
                    ->group(function (){
                        Route::get('login','loginView')->name('loginView');
                        Route::post('login','login')
                            ->middleware('throttle:login')
                            ->name('login');

                        Route::get('signup','signupView')->name('signupView');
                        Route::post('signup','signup')->name('signup');
                    });


                /**  WHILE CONNECTED */

                Route::name('connected.')
                    ->prefix('connected')
                    ->middleware([ App\Http\Middleware\AdminMiddleware::class ])
                    ->group(function (){
                        Route::delete('logout','logout')->name('logout');
                    });
            });






        /** SIMPLES ROUTES */

        Route::controller(App\Http\Controllers\Admin\BaseController::class)
            ->middleware([ App\Http\Middleware\AdminMiddleware::class ])
            ->group(function (){
                Route::get('/','profileView')->name('profileView');
                Route::get('/profile','profileView')->name('profileView');
                Route::get('/calendar','calendarView')->name('calendarView');

            });


    });




