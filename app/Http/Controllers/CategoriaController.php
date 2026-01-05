<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener parámetros de búsqueda
        $search = $request->get('search');
        $tipo = $request->get('tipo');
        $estado = $request->get('estado');
        
        // Iniciar consulta
        $query = Categoria::with(['padre', 'hijos', 'productos']);
        
        // Aplicar filtros de búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                // Buscar por ID (si es número)
                if (is_numeric($search)) {
                    $q->where('id_categoria', $search);
                }
                // Buscar por nombre
                $q->orWhere('vNombre', 'like', "%{$search}%")
                  ->orWhere('tDescripcion', 'like', "%{$search}%")
                  ->orWhere('vSlug', 'like', "%{$search}%");
            });
        }
        
        // Filtrar por tipo
        if ($tipo == 'raiz') {
            $query->whereNull('id_categoria_padre');
        } elseif ($tipo == 'hijo') {
            $query->whereNotNull('id_categoria_padre');
        }
        
        // Filtrar por estado
        if ($estado == 'activo') {
            $query->where('bActivo', true);
        } elseif ($estado == 'inactivo') {
            $query->where('bActivo', false);
        }
        
        // Ordenar
        $query->orderBy('id_categoria_padre')
              ->orderBy('iOrden')
              ->orderBy('vNombre');
        
        $categorias = $query->get();
        
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categoriasPadre = Categoria::paraSelect();
        
        return view('categorias.create', compact('categoriasPadre'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_categorias,vNombre',
            'id_categoria_padre' => 'nullable|exists:tbl_categorias,id_categoria',
            'tDescripcion' => 'nullable|max:500',
            'iOrden' => 'nullable|integer|min:0',
            'vImagen' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048'
        ], [
            'vNombre.required' => 'El nombre de la categoría es obligatorio',
            'vNombre.unique' => 'Ya existe una categoría con este nombre',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'id_categoria_padre.exists' => 'La categoría padre seleccionada no existe',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres',
            'iOrden.integer' => 'El orden debe ser un número entero',
            'iOrden.min' => 'El orden no puede ser negativo',
            'vImagen.image' => 'El archivo debe ser una imagen válida',
            'vImagen.mimes' => 'Las imágenes deben ser de tipo: jpg, jpeg, png, gif o webp',
            'vImagen.max' => 'La imagen no debe pesar más de 2MB'
        ]);

        try {
            DB::beginTransaction();

            // Crear objeto Categoria manualmente
            $categoria = new Categoria();
            
            $categoria->vNombre = $request->vNombre;
            $categoria->id_categoria_padre = $request->id_categoria_padre;
            $categoria->tDescripcion = $request->tDescripcion;
            
            // Si no se especifica orden, usar el último orden + 1
            if (empty($request->iOrden)) {
                $ultimoOrden = Categoria::where('id_categoria_padre', $request->id_categoria_padre)
                                        ->max('iOrden');
                $categoria->iOrden = $ultimoOrden ? $ultimoOrden + 1 : 0;
            } else {
                $categoria->iOrden = $request->iOrden;
            }
            
            $categoria->bActivo = $request->has('bActivo') ? 1 : 0;
            
            // Generar slug automáticamente
            $categoria->vSlug = Str::slug($request->vNombre);
            
            // Manejar la imagen si se subió
            if ($request->hasFile('vImagen')) {
                $imagen = $request->file('vImagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                $imagen->storeAs('public/categorias', $nombreImagen);
                $categoria->vImagen = $nombreImagen; // Solo el nombre del archivo
            }
            
            $categoria->save();
            
            DB::commit();

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
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
        $categoria->load(['padre', 'hijos', 'productos']);
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        $categoriasPadre = Categoria::paraSelect($categoria->id_categoria);
        
        return view('categorias.edit', compact('categoria', 'categoriasPadre'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_categorias,vNombre,' . $categoria->id_categoria . ',id_categoria',
            'id_categoria_padre' => 'nullable|exists:tbl_categorias,id_categoria',
            'tDescripcion' => 'nullable|max:500',
            'iOrden' => 'nullable|integer|min:0',
            'vImagen' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048'
        ], [
            'vNombre.required' => 'El nombre de la categoría es obligatorio',
            'vNombre.unique' => 'Ya existe una categoría con este nombre',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'id_categoria_padre.exists' => 'La categoría padre seleccionada no existe',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres',
            'iOrden.integer' => 'El orden debe ser un número entero',
            'iOrden.min' => 'El orden no puede ser negativo',
            'vImagen.image' => 'El archivo debe ser una imagen válida',
            'vImagen.mimes' => 'Las imágenes deben ser de tipo: jpg, jpeg, png, gif o webp',
            'vImagen.max' => 'La imagen no debe pesar más de 2MB'
        ]);

        // Validar que no se asigne como padre a sí misma o a sus descendientes
        if ($request->id_categoria_padre == $categoria->id_categoria) {
            return redirect()->back()
                ->with('error', 'No puedes asignar una categoría como padre de sí misma')
                ->withInput();
        }

        // Verificar si intenta asignar un descendiente como padre
        $descendientesIds = $categoria->obtenerIdsCategoriasDescendientes();
        if (in_array($request->id_categoria_padre, $descendientesIds)) {
            return redirect()->back()
                ->with('error', 'No puedes asignar una categoría descendiente como padre')
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Actualizar campos básicos
            $categoria->vNombre = $request->vNombre;
            $categoria->id_categoria_padre = $request->id_categoria_padre;
            $categoria->tDescripcion = $request->tDescripcion;
            $categoria->iOrden = $request->iOrden;
            $categoria->bActivo = $request->has('bActivo') ? 1 : 0;
            
            // Actualizar slug si cambió el nombre
            if ($categoria->isDirty('vNombre')) {
                $categoria->vSlug = Str::slug($request->vNombre);
            }

            // Manejar la imagen si se subió
            if ($request->hasFile('vImagen')) {
                // Eliminar imagen anterior si existe
                if ($categoria->vImagen) {
                    $rutaImagen = storage_path('app/public/categorias/' . $categoria->vImagen);
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }

                $imagen = $request->file('vImagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                $imagen->storeAs('public/categorias', $nombreImagen);
                $categoria->vImagen = $nombreImagen;
            } elseif ($request->has('eliminar_imagen') && $request->eliminar_imagen == '1') {
                // Eliminar imagen si se solicitó
                if ($categoria->vImagen) {
                    $rutaImagen = storage_path('app/public/categorias/' . $categoria->vImagen);
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }
                $categoria->vImagen = null;
            }

            $categoria->save();
            
            DB::commit();

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría actualizada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
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
        // Verificar si hay productos asociados
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados');
        }

        // Verificar si tiene categorías hijas
        if ($categoria->hijos()->count() > 0) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene subcategorías. Elimine primero las subcategorías.');
        }

        try {
            DB::beginTransaction();

            // Eliminar imagen si existe
            if ($categoria->vImagen) {
                $rutaImagen = storage_path('app/public/categorias/' . $categoria->vImagen);
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            $categoria->delete();
            
            DB::commit();

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('categorias.index')
                ->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }
}