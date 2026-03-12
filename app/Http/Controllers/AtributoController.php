<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AtributoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Atributo::withCount('valores');
        
        // Búsqueda por nombre o ID
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vNombre', 'LIKE', "%{$search}%")
                  ->orWhere('id_atributo', 'LIKE', "%{$search}%");
            });
        }
        
        $atributos = $query->orderBy('id_atributo', 'desc')->get();
        
        return view('atributos.index', compact('atributos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('atributos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre',
            'vSlug' => 'nullable|max:100|unique:tbl_atributos,vSlug',
            'tDescripcion' => 'nullable|max:500',
        ], [
            'vNombre.required' => 'El nombre del atributo es obligatorio',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'vNombre.unique' => 'Ya existe un atributo con este nombre',
            'vSlug.unique' => 'Ya existe un atributo con este slug',
            'vSlug.max' => 'El slug no puede tener más de 100 caracteres',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $atributo = Atributo::create([
                'vNombre' => $request->vNombre,
                'vSlug' => $request->vSlug ?? Str::slug($request->vNombre),
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0
            ]);

            return redirect()->route('atributos.index')
                ->with('success', 'Atributo creado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el atributo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Atributo $atributo)
    {
        $atributo->load(['valores' => function($query) {
            $query->orderBy('vValor');
        }]);
        
        return view('atributos.show', compact('atributo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Atributo $atributo)
    {
        return view('atributos.edit', compact('atributo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atributo $atributo)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre,' . $atributo->id_atributo . ',id_atributo',
            'vSlug' => 'nullable|max:100|unique:tbl_atributos,vSlug,' . $atributo->id_atributo . ',id_atributo',
            'tDescripcion' => 'nullable|max:500',
        ], [
            'vNombre.required' => 'El nombre del atributo es obligatorio',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'vNombre.unique' => 'Ya existe otro atributo con este nombre',
            'vSlug.unique' => 'Ya existe otro atributo con este slug',
            'vSlug.max' => 'El slug no puede tener más de 100 caracteres',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $atributo->update([
                'vNombre' => $request->vNombre,
                'vSlug' => $request->vSlug ?? Str::slug($request->vNombre),
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0
            ]);

            return redirect()->route('atributos.index')
                ->with('success', 'Atributo actualizado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el atributo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atributo $atributo)
    {
        try {
            // Verificar si el atributo está asignado a productos
            if ($atributo->productos()->count() > 0) {
                return redirect()->route('atributos.index')
                    ->with('error', 'No se puede eliminar el atributo porque está asignado a productos.');
            }

            $atributo->delete();
            
            return redirect()->route('atributos.index')
                ->with('success', 'Atributo eliminado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->route('atributos.index')
                ->with('error', 'Error al eliminar el atributo: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * MÉTODOS PARA VALORES DE ATRIBUTOS
     * ============================================
     */
    
    public function valores(Atributo $atributo)
    {
        $valores = $atributo->valores()->orderBy('vValor')->get();
        return view('atributos.valores.index', compact('atributo', 'valores'));
    }

    public function createValor(Atributo $atributo)
    {
        return view('atributos.valores.create', compact('atributo'));
    }

    public function storeValor(Request $request, Atributo $atributo)
    {
        $validator = Validator::make($request->all(), [
            'vValor' => 'required|max:100',
            'vSlug' => 'nullable|max:100',
            'bActivo' => 'nullable|boolean'
        ], [
            'vValor.required' => 'El valor es obligatorio',
            'vValor.max' => 'El valor no puede tener más de 100 caracteres',
            'vSlug.max' => 'El slug no puede tener más de 100 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Generar slug si no se proporciona
            $slug = $request->vSlug ? Str::slug($request->vSlug) : Str::slug($request->vValor);
            
            // Verificar si ya existe un valor con el mismo slug para este atributo
            $exists = AtributoValor::where('id_atributo', $atributo->id_atributo)
                ->where('vSlug', $slug)
                ->exists();
                
            if ($exists) {
                return redirect()->back()
                    ->with('error', 'Ya existe un valor con este slug para este atributo.')
                    ->withInput();
            }

            // Verificar si ya existe el mismo valor
            $existsValor = AtributoValor::where('id_atributo', $atributo->id_atributo)
                ->where('vValor', $request->vValor)
                ->exists();
                
            if ($existsValor) {
                return redirect()->back()
                    ->with('error', 'Ya existe este valor para el atributo.')
                    ->withInput();
            }

            AtributoValor::create([
                'id_atributo' => $atributo->id_atributo,
                'vValor' => $request->vValor,
                'vSlug' => $slug,
                'bActivo' => $request->has('bActivo') ? 1 : 0
            ]);

            return redirect()->route('atributos.valores', $atributo)
                ->with('success', 'Valor creado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el valor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editValor(Atributo $atributo, AtributoValor $valor)
    {
        // Verificar que el valor pertenezca al atributo
        if ($valor->id_atributo != $atributo->id_atributo) {
            abort(404);
        }
        
        return view('atributos.valores.edit', compact('atributo', 'valor'));
    }

    public function updateValor(Request $request, Atributo $atributo, AtributoValor $valor)
    {
        // Verificar que el valor pertenezca al atributo
        if ($valor->id_atributo != $atributo->id_atributo) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'vValor' => 'required|max:100',
            'vSlug' => 'nullable|max:100',
            'bActivo' => 'nullable|boolean'
        ], [
            'vValor.required' => 'El valor es obligatorio',
            'vValor.max' => 'El valor no puede tener más de 100 caracteres',
            'vSlug.max' => 'El slug no puede tener más de 100 caracteres',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $slug = $request->vSlug ? Str::slug($request->vSlug) : Str::slug($request->vValor);
            
            // Verificar si ya existe otro valor con el mismo slug para este atributo
            $exists = AtributoValor::where('id_atributo', $atributo->id_atributo)
                ->where('vSlug', $slug)
                ->where('id_atributo_valor', '!=', $valor->id_atributo_valor)
                ->exists();
                
            if ($exists) {
                return redirect()->back()
                    ->with('error', 'Ya existe otro valor con este slug para este atributo.')
                    ->withInput();
            }

            // Verificar si ya existe otro valor con el mismo texto
            $existsValor = AtributoValor::where('id_atributo', $atributo->id_atributo)
                ->where('vValor', $request->vValor)
                ->where('id_atributo_valor', '!=', $valor->id_atributo_valor)
                ->exists();
                
            if ($existsValor) {
                return redirect()->back()
                    ->with('error', 'Ya existe otro valor con este texto para el atributo.')
                    ->withInput();
            }

            $valor->update([
                'vValor' => $request->vValor,
                'vSlug' => $slug,
                'bActivo' => $request->has('bActivo') ? 1 : 0
            ]);

            return redirect()->route('atributos.valores', $atributo)
                ->with('success', 'Valor actualizado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el valor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyValor(Atributo $atributo, AtributoValor $valor)
    {
        // Verificar que el valor pertenezca al atributo
        if ($valor->id_atributo != $atributo->id_atributo) {
            abort(404);
        }

        try {
            // Verificar si el valor está asignado a productos
            if ($valor->productos()->count() > 0) {
                return redirect()->route('atributos.valores', $atributo)
                    ->with('error', 'No se puede eliminar el valor porque está asignado a productos.');
            }

            $valor->delete();
            
            return redirect()->route('atributos.valores', $atributo)
                ->with('success', 'Valor eliminado exitosamente');
                
        } catch (\Exception $e) {
            return redirect()->route('atributos.valores', $atributo)
                ->with('error', 'Error al eliminar el valor: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * MÉTODOS PARA PANEL DE GESTIÓN RÁPIDA
     * ============================================
     */

    /**
     * Creación rápida de atributo desde AJAX
     */
    public function quickCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre',
            'vSlug' => 'nullable|max:100|unique:tbl_atributos,vSlug',
            'tDescripcion' => 'nullable|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $atributo = Atributo::create([
                'vNombre' => $request->vNombre,
                'vSlug' => $request->vSlug ?? Str::slug($request->vNombre),
                'tDescripcion' => $request->tDescripcion ?? null,
                'bActivo' => true
            ]);

            return response()->json([
                'success' => true,
                'atributo' => $atributo,
                'message' => 'Atributo creado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear atributo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Creación rápida de valor de atributo desde AJAX
     */
    public function quickCreateValor(Request $request, Atributo $atributo)
    {
        $validator = Validator::make($request->all(), [
            'vValor' => 'required|max:100',
            'vSlug' => 'nullable|max:100',
            'bActivo' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generar slug si no se proporciona
            $slug = $request->vSlug ? (string)$request->vSlug : Str::slug($request->vValor);
            
            // Verificar unicidad del slug
            $slugOriginal = $slug;
            $contador = 1;
            
            while (AtributoValor::where('id_atributo', $atributo->id_atributo)
                               ->where('vSlug', $slug)
                               ->exists()) {
                $slug = $slugOriginal . '-' . $contador;
                $contador++;
            }
            
            // Verificar unicidad del valor
            $valorOriginal = $request->vValor;
            $contadorValor = 1;
            
            while (AtributoValor::where('id_atributo', $atributo->id_atributo)
                               ->where('vValor', $valorOriginal)
                               ->exists()) {
                $valorOriginal = $request->vValor . ' ' . $contadorValor;
                $contadorValor++;
            }
            
            $valor = AtributoValor::create([
                'id_atributo' => $atributo->id_atributo,
                'vValor' => $valorOriginal,
                'vSlug' => $slug,
                'bActivo' => $request->has('bActivo') ? 1 : 1
            ]);

            return response()->json([
                'success' => true,
                'valor' => $valor,
                'message' => 'Valor creado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear valor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener atributos en formato JSON
     */
    public function getJson()
    {
        $atributos = Atributo::with(['valores' => function($query) {
            $query->where('bActivo', true)->orderBy('vValor');
        }])
        ->where('bActivo', true)
        ->orderBy('vNombre')
        ->get();
        
        return response()->json([
            'success' => true,
            'atributos' => $atributos
        ]);
    }
}