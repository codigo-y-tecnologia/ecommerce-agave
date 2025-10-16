<?php

namespace App\Http\Controllers;

use App\Models\Impuestos;
use Illuminate\Http\Request;

class ImpuestosController extends Controller
{
    public function index()
    {
        $impuestos = Impuestos::all();
        return view('impuestos.index', compact('impuestos'));
    }

    public function create()
    {
        return view('impuestos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'vNombre' => 'required|string|max:100',
            'eTipo' => 'required|in:IVA,IEPS,OTRO',
            'dPorcentaje' => 'required|numeric|min:0|max:100',
            // REMOVER la validación boolean de bActivo
        ]);

        // Preparar datos manualmente
        $data = $request->all();
        $data['dFecha_creacion'] = now();
        $data['bActivo'] = $request->has('bActivo') ? 1 : 0; // Convertir a 1 o 0

        Impuestos::create($data);

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto creado correctamente.');
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        $impuesto = Impuestos::findOrFail($id);
        return view('impuestos.edit', compact('impuesto'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'vNombre' => 'required|string|max:100',
            'eTipo' => 'required|in:IVA,IEPS,OTRO',
            'dPorcentaje' => 'required|numeric|min:0|max:100',
            // REMOVER la validación boolean de bActivo
        ]);

        $impuesto = Impuestos::findOrFail($id);
        
        // Preparar datos manualmente
        $data = $request->all();
        $data['bActivo'] = $request->has('bActivo') ? 1 : 0; // Convertir a 1 o 0
        
        $impuesto->update($data);

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $impuesto = Impuestos::findOrFail($id);
        $impuesto->delete();

        return redirect()->route('impuestos.index')
            ->with('success', 'Impuesto eliminado correctamente.');
    }
}