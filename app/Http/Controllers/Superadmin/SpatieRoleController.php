<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class SpatieRoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('superadmin.roles.index', compact('roles'));
    }
}
