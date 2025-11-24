<?php

namespace App\Http\Controllers;

use App\Models\Reembolsos;
use Illuminate\Http\Request;

class ReembolsosController extends Controller
{
    public function index()
    {
        $reembolsos = Reembolsos::all();
        return view('reembolsos.index', compact('reembolsos'));
    }

    public function create()
    {
        return view('reembolsos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Id_venta' => 'required|integer',
            'dMonto' => 'required|numeric|min:0',
            'vMotivo' => 'required|string|max:255',
            'eMetodo_pago' => 'required|in:paypal,transferencia,tarjeta,stripe',
            'eEstado' => 'required|in:pendiente,procesado,fallido', // Cambié los estados
        ]);

        $data = $request->all();
        $data['tFecha_reembolso'] = now()->format('Y-m-d H:i:s');

        Reembolsos::create($data);

        return redirect()->route('reembolsos.index')
            ->with('success', 'Reembolso creado correctamente.');
    }

    public function edit($id)
    {
        $reembolso = Reembolsos::findOrFail($id);
        return view('reembolsos.edit', compact('reembolso'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Id_venta' => 'required|integer',
            'dMonto' => 'required|numeric|min:0',
            'vMotivo' => 'required|string|max:255',
            'eMetodo_pago' => 'required|in:paypal,transferencia,tarjeta,stripe',
            'eEstado' => 'required|in:pendiente,procesado,fallido',
        ]);

        $reembolso = Reembolsos::findOrFail($id);
        $reembolso->update($request->all());

        return redirect()->route('reembolsos.index')
            ->with('success', 'Reembolso actualizado correctamente.');
    }

    public function destroy($id)
    {
        $reembolso = Reembolsos::findOrFail($id);
        $reembolso->delete();

        return redirect()->route('reembolsos.index')
            ->with('success', 'Reembolso eliminado correctamente.');
    }
}