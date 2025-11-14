<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $marcas = Marca::all();
        return view('marcas.index', compact('marcas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
         return view('marcas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_marcas,vNombre',
            'tDescripcion' => 'nullable|max:500'
        ], [
            'vNombre.required' => 'El nombre de la marca es obligatorio',
            'vNombre.max' => 'El nombre no debe exceder 100 caracteres',
            'vNombre.unique' => 'Ya existe una marca con este nombre'
        ]);

        Marca::create($request->all());
        
        return redirect()->route('marcas.index')
            ->with('success', 'Marca creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        //
         return view('marcas.edit', compact('marca'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        //
         $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_marcas,vNombre,' . $marca->id_marca . ',id_marca',
            'tDescripcion' => 'nullable|max:500'
        ]);

        $marca->update($request->all());
        
        return redirect()->route('marcas.index')
            ->with('success', 'Marca actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marca $marca)
    {
        if ($marca->productos()->count() > 0) {
            return redirect()->route('marcas.index')
                ->with('error', 'No se puede eliminar la marca porque tiene productos asociados');
        }

        $marca->delete();
        
        return redirect()->route('marcas.index')
            ->with('success', 'Marca eliminada exitosamente');
    }
}