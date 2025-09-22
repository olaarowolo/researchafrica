<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController
{
    public function index()
    {
        return view('home');
    }


    public function logout(Request $request)
    {
        # code...
        Auth::logout();
        $request->session()->flush();

        return to_route('admin.login');
    }
}
