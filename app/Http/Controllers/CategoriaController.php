<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
          $categorias = Categoria::all();
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
       $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_categorias,vNombre',
            'tDescripcion' => 'nullable|max:500'
        ], [
            'vNombre.required' => 'El nombre de la categoría es obligatorio',
            'vNombre.unique' => 'Ya existe una categoría con este nombre',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres'
        ]);

        try {
            Categoria::create([
                'vNombre' => $request->vNombre,
                'tDescripcion' => $request->tDescripcion
            ]);
            
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la categoría: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
         return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        //
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        //
         $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_categorias,vNombre,' . $categoria->id_categoria . ',id_categoria',
            'tDescripcion' => 'nullable|max:500'
        ], [
            'vNombre.required' => 'El nombre de la categoría es obligatorio',
            'vNombre.unique' => 'Ya existe una categoría con este nombre',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres'
        ]);
    try {
        $categoria->update([
            'vNombre' => $request->vNombre,
            'tDescripcion' => $request->tDescripcion
        ]);
        
        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente');
            
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error al actualizar la categoría: ' . $e->getMessage())
            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        //
        // Verificar si hay productos asociados
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados');
        }

        $categoria->delete();
        
        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada exitosamente');
    }
}
