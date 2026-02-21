<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use Illuminate\Support\Facades\File;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;

class DashboardController extends Controller
{

    public function index()
    {

        $productos = Producto::with(['categoria', 'marca', 'etiquetas'])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc') // ✅ CORREGIDO: id_producto en lugar de created_at
            ->take(12)
            ->get();

        // Si el usuario está autenticado, lo redirigimos a su panel correspondiente
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('superadmin')) {
                return view('dashboards.superadmin');
            }

            if ($user->hasRole('admin')) {
                return view('dashboards.admin');
            }

            if ($user->hasRole('cliente')) {
                return view('dashboards.cliente', compact('productos'));
            }

            // Fallback de seguridad
            return view('dashboards.cliente', compact('productos'));
        }

        // Si no está autenticado, mostramos la vista pública (cliente como visitante)
        return view('dashboards.cliente', compact('productos'));
    }

    public function cliente()
    {
        $productos = Producto::with(['categoria', 'marca', 'etiquetas'])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc') // ✅ CORREGIDO: id_producto en lugar de created_at
            ->take(12)
            ->get();

        return view('dashboards.cliente', compact('productos'));
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
