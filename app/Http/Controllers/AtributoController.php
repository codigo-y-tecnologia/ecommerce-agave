<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class AtributoController extends Controller
{
    public function index(Request $request)
    {
           $query = Atributo::withCount('valores');
    
    // Búsqueda por ID o nombre - NUEVO
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('vNombre', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('id_atributo', $searchTerm);
        });
    }
    
    // Ordenamiento - NUEVO (pero mantiene el orden por defecto que ya tenías)
    if ($request->has('orden')) {
        switch ($request->orden) {
            case 'nombre':
                $query->orderBy('vNombre', 'asc');
                break;
            case 'nombre_desc':
                $query->orderBy('vNombre', 'desc');
                break;
            case 'id':
                $query->orderBy('id_atributo', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id_atributo', 'desc');
                break;
            case 'valores':
                $query->orderBy('valores_count', 'desc');
                break;
            case 'valores_desc':
                $query->orderBy('valores_count', 'asc');
                break;
            default:
                $query->orderBy('vNombre'); // Mantiene el orden que ya tenías
                break;
        }
    } else {
        $query->orderBy('vNombre'); // Orden por defecto original
    }
    
    $atributos = $query->get(); // Mantiene el get() original
    
    return view('atributos.index', compact('atributos'));

    }

    public function create()
    {
        return view('atributos.create');
    }

    public function store(Request $request)
    {
         $request->validate([
        'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre',
        'vSlug' => 'nullable|max:100|unique:tbl_atributos,vSlug',
        'tDescripcion' => 'nullable'
    ], [
        'vNombre.required' => 'El nombre del atributo es obligatorio',
        'vNombre.unique' => 'Ya existe un atributo con este nombre',
        'vSlug.unique' => 'El slug ya está en uso. Intenta con otro.'
    ]);

    try {
        DB::beginTransaction();

        $atributo = Atributo::create([
            'vNombre' => $request->vNombre,
            'vSlug' => $request->vSlug ?: Str::slug($request->vNombre),
            'tDescripcion' => $request->tDescripcion,
            'bActivo' => true
            // Si $timestamps = true en el modelo, Laravel agregará automáticamente created_at y updated_at
        ]);

        DB::commit();

        return redirect()->route('atributos.index')
            ->with('success', 'Atributo creado exitosamente');

    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Error al crear el atributo: ' . $e->getMessage()]);
    }
    }

    public function show(Atributo $atributo)
    {
        $atributo->load('valores');
        return view('atributos.show', compact('atributo'));
    }

    public function edit(Atributo $atributo)
    {
        $atributo->load('valores');
        return view('atributos.edit', compact('atributo'));
    }

    public function update(Request $request, Atributo $atributo)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre,' . $atributo->id_atributo . ',id_atributo',
            'vSlug' => 'nullable|max:100|unique:tbl_atributos,vSlug,' . $atributo->id_atributo . ',id_atributo',
            'tDescripcion' => 'nullable'
        ], [
            'vNombre.required' => 'El nombre del atributo es obligatorio',
            'vNombre.unique' => 'Ya existe un atributo con este nombre',
            'vSlug.unique' => 'El slug ya está en uso. Intenta con otro.'
        ]);

        try {
            DB::beginTransaction();

            $atributo->update([
                'vNombre' => $request->vNombre,
                'vSlug' => $request->vSlug ?: Str::slug($request->vNombre),
                'tDescripcion' => $request->tDescripcion
            ]);

            DB::commit();

            return redirect()->route('atributos.index')
                ->with('success', 'Atributo actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el atributo: ' . $e->getMessage()]);
        }
    }

    public function destroy(Atributo $atributo)
    {
        try {
            DB::beginTransaction();

            // Eliminar el atributo y sus valores (en cascada)
            $atributo->delete();

            DB::commit();

            return redirect()->route('atributos.index')
                ->with('success', 'Atributo eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('atributos.index')
                ->with('error', 'Error al eliminar el atributo: ' . $e->getMessage());
        }
    }

    // Métodos para gestionar valores del atributo
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
       $request->validate([
            'vValor' => 'required|max:100',
            'vSlug' => 'nullable|max:100|unique:tbl_atributo_valores,vSlug',
            'bActivo' => 'boolean'
        ], [
            'vValor.required' => 'El valor es obligatorio',
            'vValor.max' => 'El valor no puede tener más de 100 caracteres',
            'vSlug.unique' => 'El slug ya está en uso. Intenta con otro.'
        ]);

        try {
            DB::beginTransaction();

            AtributoValor::create([
                'id_atributo' => $atributo->id_atributo,
                'vValor' => $request->vValor,
                'vSlug' => $request->vSlug ?: Str::slug($request->vValor),
                'dPrecio_extra' => 0,
                'iStock' => 0,
                'iOrden' => 0,
                'bActivo' => $request->has('bActivo')
            ]);

            DB::commit();

            return redirect()->route('atributos.valores', $atributo)
                ->with('success', 'Valor agregado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al agregar el valor: ' . $e->getMessage()]);
        }
    }

    public function editValor(Atributo $atributo, AtributoValor $valor)
    {
          return view('atributos.valores.edit', compact('atributo', 'valor'));
    }

    public function updateValor(Request $request, Atributo $atributo, AtributoValor $valor)
    {
         $request->validate([
            'vValor' => 'required|max:100',
            'vSlug' => 'nullable|max:100|unique:tbl_atributo_valores,vSlug,' . $valor->id_atributo_valor . ',id_atributo_valor',
            'bActivo' => 'boolean'
        ], [
            'vValor.required' => 'El valor es obligatorio',
            'vValor.max' => 'El valor no puede tener más de 100 caracteres',
            'vSlug.unique' => 'El slug ya está en uso. Intenta con otro.'
        ]);

        try {
            DB::beginTransaction();

            $valor->update([
                'vValor' => $request->vValor,
                'vSlug' => $request->vSlug ?: Str::slug($request->vValor),
                'bActivo' => $request->has('bActivo')
            ]);

            DB::commit();

            return redirect()->route('atributos.valores', $atributo)
                ->with('success', 'Valor actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el valor: ' . $e->getMessage()]);
        }
    }

    public function destroyValor(Atributo $atributo, AtributoValor $valor)
    {
      try {
            DB::beginTransaction();

            $valor->delete();

            DB::commit();

            return redirect()->route('atributos.valores', $atributo)
                ->with('success', 'Valor eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('atributos.valores', $atributo)
                ->with('error', 'Error al eliminar el valor: ' . $e->getMessage());
        }
    }
}