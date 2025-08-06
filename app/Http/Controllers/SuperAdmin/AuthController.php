<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{


    public function loginView(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        return view('super-admin.auth.login');
    }

    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        $superAdmin = SuperAdmin::first();

        if ($superAdmin && Hash::check($request->input('password'), $superAdmin->password)) {
            Auth::guard('super-admin')->login($superAdmin);

            return redirect()->route('super-admin.manageAdminsView');
        } else {
            return back()->with(['error' => 'Mot de passe incorrect'], 401);
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('super-admin')->logout();
        return redirect()
            ->route('super-admin.auth.disconnected.login')
            ->with('success','Déconnexion réussie');
    }

}
