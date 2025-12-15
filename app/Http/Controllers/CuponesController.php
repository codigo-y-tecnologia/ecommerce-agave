<?php

namespace App\Http\Controllers;

use App\Models\Cupon;  
use Illuminate\Http\Request;

class CuponesController extends Controller
{
    public function index()
    {
        $cupones = Cupon::orderBy('id_cupon', 'desc')->get();  
        return view('cupones.index', compact('cupones'));
    }

    public function create()
    {
        return view('cupones.create');
    }

    public function store(Request $request)
    {
        // Convertir el checkbox a booleano correctamente
        $request->merge([
            'bActivo' => $request->has('bActivo')
        ]);

        $data = $request->validate([
            'vCodigo_cupon' => 'required|string|max:50|unique:tbl_cupones',
            'dDescuento'    => 'required|numeric|min:0',
            'eTipo'         => 'required|in:porcentaje,monto',
            'dValido_desde' => 'required|date',
            'dValido_hasta' => 'required|date|after:dValido_desde',
            'iUso_maximo'   => 'nullable|integer|min:1',
            'bActivo'       => 'boolean',
        ]);

        Cupon::create($data);  // Cambiado a Cupones

        return redirect()
            ->route('cupones.index')
            ->with('success', 'Cupón creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones
        return view('cupones.show', compact('cupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones
        return view('cupones.edit', compact('cupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones
        
        $request->merge([
            'bActivo' => $request->has('bActivo')
        ]);

        $data = $request->validate([
            'vCodigo_cupon' => 'required|string|max:50|unique:tbl_cupones,vCodigo_cupon,' . $id . ',id_cupon',
            'dDescuento'    => 'required|numeric|min:0',
            'eTipo'         => 'required|in:porcentaje,monto',
            'dValido_desde' => 'required|date',
            'dValido_hasta' => 'required|date|after:dValido_desde',
            'iUso_maximo'   => 'nullable|integer|min:1',
            'bActivo'       => 'boolean',
        ]);

        $cupon->update($data);

        return redirect()
            ->route('cupones.index')
            ->with('success', 'Cupón actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cupon = Cupon::findOrFail($id);  // Cambiado a Cupones
        $cupon->delete();

        return redirect()
            ->route('cupones.index')
            ->with('success', 'Cupón eliminado correctamente.');
    }
}