<?php

namespace App\Http\Controllers;

use App\Models\cupones_usados;
use Illuminate\Http\Request;

class CuponesUsadosController extends Controller
{
    public function index(Request $request)
    {
        $query = cupones_usados::with(['venta', 'usuario']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_venta', $search)
                  ->orWhere('guest_token', 'like', "%{$search}%");
            });
        }

        $cuponesUsados = $query->orderByDesc('tFecha_uso')->paginate(15);

        return view('cupones_usados.index', compact('cuponesUsados'));
    }

    public function create()
    {
        return view('cupones_usados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_venta'    => 'required|integer|exists:tbl_ventas,id_venta',
            'id_usuario'  => 'nullable|integer|exists:tbl_usuarios,id_usuario',
            'guest_token' => 'nullable|string|max:36',
        ]);

        cupones_usados::create($request->only(['id_venta', 'id_usuario', 'guest_token']));

        return redirect()->route('cupones_usados.index')
                         ->with('success', 'Cupón registrado correctamente.');
    }

    public function show(cupones_usados $cupones_usados)
    {
        $cupones_usados->load(['venta', 'usuario']);
        return view('cupones_usados.show', compact('cupones_usados'));
    }

    public function edit(cupones_usados $cupones_usados)
    {
        return view('cupones_usados.edit', compact('cupones_usados'));
    }

    public function update(Request $request, cupones_usados $cupones_usados)
    {
        $request->validate([
            'id_venta'    => 'required|integer|exists:tbl_ventas,id_venta',
            'id_usuario'  => 'nullable|integer|exists:tbl_usuarios,id_usuario',
            'guest_token' => 'nullable|string|max:36',
        ]);

        $cupones_usados->update($request->only(['id_venta', 'id_usuario', 'guest_token']));

        return redirect()->route('cupones_usados.index')
                         ->with('success', 'Cupón actualizado correctamente.');
    }

    public function destroy(cupones_usados $cupones_usados)
    {
        $cupones_usados->delete();

        return redirect()->route('cupones_usados.index')
                         ->with('success', 'Cupón eliminado correctamente.');
    }
}