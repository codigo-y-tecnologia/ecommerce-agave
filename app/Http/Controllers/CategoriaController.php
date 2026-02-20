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
            'vSlug' => 'required|max:100|unique:tbl_categorias,vSlug',
            'id_categoria_padre' => 'nullable|exists:tbl_categorias,id_categoria',
            'tDescripcion' => 'nullable|max:500',
            'iOrden' => 'nullable|integer|min:0',
            'vImagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'vNombre.required' => 'El nombre de la categoría es obligatorio',
            'vNombre.unique' => 'Ya existe una categoría con este nombre',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'vSlug.required' => 'El slug es obligatorio',
            'vSlug.unique' => 'Ya existe una categoría con este slug',
            'vSlug.max' => 'El slug no puede tener más de 100 caracteres',
            'id_categoria_padre.exists' => 'La categoría padre seleccionada no existe',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres',
            'iOrden.integer' => 'El orden debe ser un número entero',
            'iOrden.min' => 'El orden no puede ser negativo',
            'vImagen.image' => 'El archivo debe ser una imagen válida',
            'vImagen.mimes' => 'Las imágenes deben ser de tipo: jpg, jpeg, png o webp',
            'vImagen.max' => 'La imagen no debe pesar más de 2MB'
        ]);

        try {
            DB::beginTransaction();

            // Crear objeto Categoria manualmente
            $categoria = new Categoria();
            
            $categoria->vNombre = $request->vNombre;
            $categoria->vSlug = $request->vSlug;
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
            
            // Manejar la imagen si se subió
            if ($request->hasFile('vImagen') && $request->file('vImagen')->isValid()) {
                $imagen = $request->file('vImagen');
                
                // Generar nombre único para la imagen
                $nombreImagen = 'categoria_' . time() . '_' . Str::slug($request->vNombre) . '.' . $imagen->getClientOriginalExtension();
                
                // Mover la imagen directamente a public/storage/categorias/
                $directPath = public_path('storage/categorias');
                
                // Asegurar que el directorio existe
                if (!file_exists($directPath)) {
                    mkdir($directPath, 0755, true);
                }
                
                // Mover el archivo
                $imagen->move($directPath, $nombreImagen);
                
                // Asignar nombre de imagen a la categoría
                $categoria->vImagen = $nombreImagen;
            }
            
            $categoria->save();
            
            DB::commit();

            // Cargar la relación padre para obtener información jerárquica
            $categoria->load('padre');

            // Determinar el nivel jerárquico y prefijo para mostrar
            $nivel = 0;
            $padreActual = $categoria->padre;
            while ($padreActual) {
                $nivel++;
                $padreActual = $padreActual->padre;
            }

            // Construir el nombre con prefijo para mostrar en selects - CORREGIDO para mostrar flechas
            $prefijo = '';
            $icono = '';
            
            if ($nivel === 0) {
                // Categoría raíz
                $icono = '🏠 ';
                $prefijo = '';
            } else {
                // Subcategoría - mostrar flechas según el nivel
                for ($i = 0; $i < $nivel; $i++) {
                    $prefijo .= '&nbsp;&nbsp;&nbsp;';
                }
                $icono = '↳ ';
            }

            $categoria->display_name = $prefijo . $icono . $categoria->vNombre;
            $categoria->nivel = $nivel;
            $categoria->icono = $icono;
            $categoria->prefijo = $prefijo;

            // Verificar si es una petición AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'categoria' => $categoria,
                    'message' => 'Categoría creada exitosamente'
                ]);
            }

            return redirect()->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la categoría: ' . $e->getMessage(),
                    'errors' => ['error' => [$e->getMessage()]]
                ], 422);
            }
            
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
            'vSlug' => 'required|max:100|unique:tbl_categorias,vSlug,' . $categoria->id_categoria . ',id_categoria',
            'id_categoria_padre' => 'nullable|exists:tbl_categorias,id_categoria',
            'tDescripcion' => 'nullable|max:500',
            'iOrden' => 'nullable|integer|min:0',
            'vImagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ], [
            'vNombre.required' => 'El nombre de la categoría es obligatorio',
            'vNombre.unique' => 'Ya existe una categoría con este nombre',
            'vNombre.max' => 'El nombre no puede tener más de 100 caracteres',
            'vSlug.required' => 'El slug es obligatorio',
            'vSlug.unique' => 'Ya existe una categoría con este slug',
            'vSlug.max' => 'El slug no puede tener más de 100 caracteres',
            'id_categoria_padre.exists' => 'La categoría padre seleccionada no existe',
            'tDescripcion.max' => 'La descripción no puede tener más de 500 caracteres',
            'iOrden.integer' => 'El orden debe ser un número entero',
            'iOrden.min' => 'El orden no puede ser negativo',
            'vImagen.image' => 'El archivo debe ser una imagen válida',
            'vImagen.mimes' => 'Las imágenes deben ser de tipo: jpg, jpeg, png o webp',
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
            $categoria->vSlug = $request->vSlug;
            $categoria->id_categoria_padre = $request->id_categoria_padre;
            $categoria->tDescripcion = $request->tDescripcion;
            $categoria->iOrden = $request->iOrden;
            $categoria->bActivo = $request->has('bActivo') ? 1 : 0;

            // Manejo de imagen - CORREGIDO: Solo eliminar si se sube una nueva o se marca para eliminar
            if ($request->hasFile('vImagen') && $request->file('vImagen')->isValid()) {
                // Eliminar imagen anterior si existe (solo cuando se sube una nueva)
                if ($categoria->vImagen) {
                    $rutaImagen = public_path('storage/categorias/' . $categoria->vImagen);
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }

                $imagen = $request->file('vImagen');
                // Generar nombre único para la imagen
                $nombreImagen = 'categoria_' . time() . '_' . Str::slug($request->vNombre) . '.' . $imagen->getClientOriginalExtension();
                
                // Mover la imagen directamente a public/storage/categorias/
                $directPath = public_path('storage/categorias');
                
                // Asegurar que el directorio existe
                if (!file_exists($directPath)) {
                    mkdir($directPath, 0755, true);
                }
                
                // Mover el archivo
                $imagen->move($directPath, $nombreImagen);
                
                // Asignar nombre de imagen a la categoría
                $categoria->vImagen = $nombreImagen;
            } 
            elseif ($request->has('eliminar_imagen') && $request->eliminar_imagen == '1') {
                // Solo eliminar si se marca explícitamente la casilla de eliminar
                if ($categoria->vImagen) {
                    $rutaImagen = public_path('storage/categorias/' . $categoria->vImagen);
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }
                $categoria->vImagen = null;
            }
            // Si no hay archivo y no se marca eliminar, mantener la imagen actual (no hacer nada)

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
                $rutaImagen = public_path('storage/categorias/' . $categoria->vImagen);
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

    /**
     * Creación rápida de categoría desde AJAX
     */
    public function quickCreate(Request $request)
    {
        $request->validate([
            'vNombre' => 'required|max:100',
            'id_categoria_padre' => 'nullable|exists:tbl_categorias,id_categoria'
        ]);

        try {
            $categoria = Categoria::create([
                'vNombre' => $request->vNombre,
                'vSlug' => Str::slug($request->vNombre),
                'id_categoria_padre' => $request->id_categoria_padre,
                'bActivo' => true,
                'iOrden' => 0
            ]);

            return response()->json([
                'success' => true,
                'categoria' => $categoria,
                'message' => 'Categoría creada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener categorías en formato JSON
     */
    public function getJson()
    {
        $categorias = Categoria::where('bActivo', true)
            ->orderBy('vNombre')
            ->get();
        
        return response()->json([
            'success' => true,
            'categorias' => $categorias
        ]);
    }
}