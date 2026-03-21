<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Marca::withCount('productos');
        
        // Búsqueda por nombre, ID o descripción
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                // Búsqueda por nombre (LIKE para búsqueda parcial)
                $q->orWhere('vNombre', 'LIKE', "%{$search}%");
                
                // Búsqueda por descripción (LIKE para búsqueda parcial)
                $q->orWhere('tDescripcion', 'LIKE', "%{$search}%");
                
                // Búsqueda por ID (si es numérico)
                if (is_numeric($search)) {
                    $q->orWhere('id_marca', '=', $search);
                }
            });
        }
        
        // Ordenamiento
        $sort = $request->get('sort', 'id_marca');
        $order = $request->get('order', 'asc');
        
        $validSorts = ['id_marca', 'vNombre'];
        $sort = in_array($sort, $validSorts) ? $sort : 'id_marca';
        $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';
        
        $query->orderBy($sort, $order);
        
        // Paginación - 10 items por página
        $marcas = $query->paginate(10)->withQueryString();
        
        return view('marcas.index', compact('marcas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('marcas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        // Si deseas implementar la vista de detalles
        // return view('marcas.show', compact('marca'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        return view('marcas.edit', compact('marca'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_marcas,vNombre,' . $marca->id_marca . ',id_marca',
            'tDescripcion' => 'nullable|max:500'
        ], [
            'vNombre.required' => 'El nombre de la marca es obligatorio',
            'vNombre.max' => 'El nombre no debe exceder 100 caracteres',
            'vNombre.unique' => 'Ya existe una marca con este nombre'
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

    /**
     * ============================================
     * NUEVOS MÉTODOS PARA PANEL DE GESTIÓN
     * ============================================
     */

    /**
     * Creación rápida de marca desde AJAX
     */
   public function quickCreate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vNombre' => 'required|max:100|unique:tbl_marcas,vNombre',
                'tDescripcion' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $marca = Marca::create([
                'vNombre' => $request->vNombre,
                'tDescripcion' => $request->tDescripcion ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Marca creada exitosamente',
                'marca' => [
                    'id_marca' => $marca->id_marca,
                    'vNombre' => $marca->vNombre,
                    'tDescripcion' => $marca->tDescripcion
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear marca rápida: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la marca: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Obtener marcas en formato JSON
     */
    public function getJson()
    {
        $marcas = Marca::orderBy('vNombre')
            ->get(['id_marca', 'vNombre', 'tDescripcion']);
        
        return response()->json([
            'success' => true,
            'marcas' => $marcas
        ]);
    }
}