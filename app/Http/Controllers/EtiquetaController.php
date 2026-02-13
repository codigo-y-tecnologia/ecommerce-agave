<?php

namespace App\Http\Controllers;

use App\Models\Etiqueta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EtiquetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        if ($search) {
            $etiquetas = Etiqueta::where('id_etiqueta', 'LIKE', "%$search%")
                ->orWhere('vNombre', 'LIKE', "%$search%")
                ->orWhere('tDescripcion', 'LIKE', "%$search%")
                ->get();
        } else {
            $etiquetas = Etiqueta::all();
        }
        
        return view('etiquetas.index', compact('etiquetas', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('etiquetas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_etiquetas,vNombre',
            'tDescripcion' => 'nullable|max:500',
            'color' => 'nullable|max:7'
        ], [
            'vNombre.required' => 'El nombre de la etiqueta es obligatorio.',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'vNombre.unique' => 'Este nombre de etiqueta ya existe.',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres.'
        ]);
        
        try {
            Etiqueta::create($request->all());
            
            return redirect()->route('etiquetas.index')
                ->with('success', 'Etiqueta creada exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear la etiqueta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Etiqueta $etiqueta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etiqueta $etiqueta)
    {
        return view('etiquetas.edit', compact('etiqueta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Etiqueta $etiqueta)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_etiquetas,vNombre,' . $etiqueta->id_etiqueta . ',id_etiqueta',
            'tDescripcion' => 'nullable|max:500',
            'color' => 'nullable|max:7'
        ], [
            'vNombre.required' => 'El nombre de la etiqueta es obligatorio',
            'vNombre.unique' => 'Ya existe una etiqueta con este nombre',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres'
        ]);
        
        try {
            $etiqueta->update([
                'vNombre' => $request->vNombre,
                'tDescripcion' => $request->tDescripcion,
                'color' => $request->color
            ]);
            
            return redirect()->route('etiquetas.index')
                ->with('success', 'Etiqueta actualizada exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la etiqueta: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Etiqueta $etiqueta)
    {
        try {
            // Verificar si la etiqueta tiene productos asociados
            if ($etiqueta->productos()->count() > 0) {
                return redirect()->route('etiquetas.index')
                    ->with('error', 'No se puede eliminar la etiqueta porque tiene productos asociados. ' .
                           'Primero desvincula los productos de esta etiqueta.');
            }
            
            $etiqueta->delete();
            
            return redirect()->route('etiquetas.index')
                ->with('success', 'Etiqueta eliminada exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->route('etiquetas.index')
                ->with('error', 'Error al eliminar la etiqueta: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * NUEVOS MÉTODOS PARA PANEL DE GESTIÓN
     * ============================================
     */

    /**
     * Creación rápida de etiqueta desde AJAX
     */
    public function quickCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_etiquetas,vNombre',
            'color' => 'nullable|max:7'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $etiqueta = Etiqueta::create([
                'vNombre' => $request->vNombre,
                'color' => $request->color ?? '#007bff',
                'tDescripcion' => $request->tDescripcion ?? null
            ]);

            return response()->json([
                'success' => true,
                'etiqueta' => $etiqueta,
                'message' => 'Etiqueta creada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear etiqueta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener etiquetas en formato JSON
     */
    public function getJson()
    {
        $etiquetas = Etiqueta::orderBy('vNombre')
            ->get(['id_etiqueta', 'vNombre', 'color', 'tDescripcion']);
        
        return response()->json([
            'success' => true,
            'etiquetas' => $etiquetas
        ]);
    }
}