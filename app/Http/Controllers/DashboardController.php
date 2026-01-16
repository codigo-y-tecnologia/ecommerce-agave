<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function cliente()
    {
        return view('dashboards.cliente');
    }

    public function admin()
    {
        return view('dashboards.admin');
    }

    public function superadmin()
    {
        return view('dashboards.superadmin');
    }
}
