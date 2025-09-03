<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\BanService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{


    public function signupView(): Factory|View|Application
    {
        return view('admin.auth.signup');
    }



    public function loginView(): Factory|View|Application
    {
        return view('admin.auth.login');
    }

    public function login(Request $request ): RedirectResponse
    {
        try {
            $admin = Admin::where('email',$request->input('email'));

            if (!$admin->exists()) {
                return back()->with('error', 'Adresse inconue.');
            }

            $admin = $admin->first();

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'authentification');
        }


        if ($admin && Hash::check($request->input('password'), $admin->password)) {
            Auth::guard('admin')->login($admin);
            $request->session()->regenerate();
            return redirect()->route('admin.profileView');
        } else {
            return back()->with(['error' => 'Mot de passe incorrect'], 401);
        }
    }


    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();
        return redirect()
            ->route('admin.auth.disconnected.login')
            ->with('success','Déconnexion réussie');
    }

}
