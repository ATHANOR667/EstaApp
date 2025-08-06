<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organizer;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public function profileView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('admin.pages.profile',
                     [
                         'admin' => Auth::guard('admin')->user(),
                     ]);
    }

    public function calendarView(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('admin.pages.calendar',
                     [
                         'admin' => Auth::guard('admin')->user(),
                     ]);
    }


}
