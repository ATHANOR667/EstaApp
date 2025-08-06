<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public function profileView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('super-admin.pages.profile',
                     [
                         'superAdmin' => Auth::guard('super-admin')->user(),
                     ]);
    }

    public function manageAdminsView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('super-admin.pages.manage-admins',
                     [
                         'superAdmin' => Auth::guard('super-admin')->user(),
                     ]);
    }
}
