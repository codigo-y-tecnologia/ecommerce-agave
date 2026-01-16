<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\ProductoVariacion;
use App\Models\VariacionAtributo;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::with(['hijos' => function($query) {
        $query->where('bActivo', true)
              ->with(['hijos' => function($subQuery) {
                  $subQuery->where('bActivo', true)
                           ->orderBy('iOrden')
                           ->orderBy('vNombre');
              }])
              ->orderBy('iOrden')
              ->orderBy('vNombre');
        }])
        ->whereNull('id_categoria_padre')
        ->where('bActivo', true)
        ->orderBy('iOrden')
        ->orderBy('vNombre')
        ->get();
        
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        $atributos = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('iOrden');
        }])->where('bActivo', true)->get();
        
        return view('productos.create', compact('categorias', 'marcas', 'etiquetas', 'atributos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vCodigo_barras' => [
                'required',
                'max:20',
                'unique:tbl_productos,vCodigo_barras',
                'regex:/^[0-9]+$/'
            ],
            'vNombre' => [
                'required',
                'max:100',
                'unique:tbl_productos,vNombre'
            ],
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',
            'dPrecio_compra' => 'nullable|numeric|min:0',
            'dPrecio_venta' => 'required|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagenes' => 'nullable|array|max:6',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'atributos' => 'nullable|array',
        ], [
            'vCodigo_barras.required' => 'El código de barras es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este código de barras',
            'vCodigo_barras.regex' => 'El código de barras solo puede contener números',
            'vNombre.required' => 'El nombre del producto es obligatorio',
            'vNombre.unique' => 'Ya existe un producto con este nombre',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'dPrecio_venta.min' => 'El precio de venta no puede ser negativo',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria',
        ]);

        try {
            DB::beginTransaction();

            $productoData = [
                'vCodigo_barras' => $request->vCodigo_barras,
                'vNombre' => $request->vNombre,
                'tDescripcion_corta' => $request->tDescripcion_corta,
                'tDescripcion_larga' => $request->tDescripcion_larga,
                'dPrecio_compra' => $request->dPrecio_compra ?: null,
                'dPrecio_venta' => $request->dPrecio_venta,
                'iStock' => $request->iStock,
                'id_categoria' => $request->id_categoria,
                'id_marca' => $request->id_marca,
                'bActivo' => $request->has('bActivo') ? true : false,
            ];

            $producto = Producto::create($productoData);

            if ($request->hasFile('imagenes')) {
                $producto->guardarImagenes($request->file('imagenes'));
            }

            if ($request->has('etiquetas')) {
                $producto->etiquetas()->sync($request->etiquetas);
            }

            if ($request->has('atributos')) {
                foreach ($request->atributos as $atributoId => $valores) {
                    if (!empty($valores) && is_array($valores)) {
                        foreach ($valores as $valorId) {
                            $valor = AtributoValor::where('id_atributo_valor', $valorId)
                                ->where('id_atributo', $atributoId)
                                ->first();
                            
                            if ($valor) {
                                DB::table('tbl_producto_atributos')->insert([
                                    'id_producto' => $producto->id_producto,
                                    'id_atributo' => $atributoId,
                                    'id_atributo_valor' => $valorId,
                                    'dPrecio_extra' => 0
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
    }

    public function showPublic($id)
    {
        $producto = Producto::with(['marca', 'categoria', 'etiquetas'])
                            ->where('bActivo', true)
                            ->findOrFail($id);
        
        return view('productos.show-public', compact('producto'));
    }

    public function catalogo()
    {
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])
                            ->where('bActivo', true)
                            ->orderBy('vNombre')
                            ->get();
        
        return view('productos.catalogo', compact('productos'));
    }

    public function show(Producto $producto)
    {
        $producto->load(['marca', 'categoria', 'etiquetas', 'variaciones.atributos.valor', 'variaciones.atributos.atributo']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::with(['hijos' => function($query) {
        $query->where('bActivo', true)
              ->with(['hijos' => function($subQuery) {
                  $subQuery->where('bActivo', true)
                           ->orderBy('iOrden')
                           ->orderBy('vNombre');
              }])
              ->orderBy('iOrden')
              ->orderBy('vNombre');
        }])
        ->whereNull('id_categoria_padre')
        ->where('bActivo', true)
        ->orderBy('iOrden')
        ->orderBy('vNombre')
        ->get();
        
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        $atributos = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('iOrden');
        }])->where('bActivo', true)->get();
        $producto->load(['etiquetas', 'variaciones.atributos', 'valoresAtributos.atributo']);
        
        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'etiquetas', 'atributos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'vCodigo_barras' => [
                'required',
                'max:20',
                Rule::unique('tbl_productos', 'vCodigo_barras')->ignore($producto->id_producto, 'id_producto'),
                'regex:/^[0-9]+$/'
            ],
            'vNombre' => [
                'required',
                'max:100',
                Rule::unique('tbl_productos', 'vNombre')->ignore($producto->id_producto, 'id_producto')
            ],
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',
            'dPrecio_compra' => 'nullable|numeric|min:0',
            'dPrecio_venta' => 'required|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'atributos' => 'nullable|array',
        ], [
            'vCodigo_barras.required' => 'El código de barras es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este código de barras',
            'vCodigo_barras.regex' => 'El código de barras solo puede contener números',
            'vNombre.required' => 'El nombre del producto es obligatorio',
            'vNombre.unique' => 'Ya existe un producto con este nombre',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'dPrecio_venta.min' => 'El precio de venta no puede ser negativo',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria',
        ]);

        try {
            DB::beginTransaction();

            $imagenesActuales = $producto->getNumeroImagenes();
            $nuevasImagenes = $request->hasFile('imagenes') ? count($request->file('imagenes')) : 0;
            
            if (($imagenesActuales + $nuevasImagenes) > 6) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['imagenes' => 'No puedes tener más de 6 imágenes. Actualmente tienes ' . $imagenesActuales . ' imágenes.']);
            }

            $producto->update([
                'vCodigo_barras' => $request->vCodigo_barras,
                'vNombre' => $request->vNombre,
                'tDescripcion_corta' => $request->tDescripcion_corta,
                'tDescripcion_larga' => $request->tDescripcion_larga,
                'dPrecio_compra' => $request->dPrecio_compra ?: null,
                'dPrecio_venta' => $request->dPrecio_venta,
                'iStock' => $request->iStock,
                'id_categoria' => $request->id_categoria,
                'id_marca' => $request->id_marca,
                'bActivo' => $request->has('bActivo') ? true : false,
            ]);
            
            if ($request->hasFile('imagenes')) {
                $producto->guardarImagenes($request->file('imagenes'));
            }

            $producto->etiquetas()->sync($request->etiquetas ?? []);

            DB::table('tbl_producto_atributos')->where('id_producto', $producto->id_producto)->delete();
            
            if ($request->has('atributos')) {
                foreach ($request->atributos as $atributoId => $valores) {
                    if (!empty($valores) && is_array($valores)) {
                        foreach ($valores as $valorId) {
                            $valor = AtributoValor::where('id_atributo_valor', $valorId)
                                ->where('id_atributo', $atributoId)
                                ->first();
                            
                            if ($valor) {
                                DB::table('tbl_producto_atributos')->insert([
                                    'id_producto' => $producto->id_producto,
                                    'id_atributo' => $atributoId,
                                    'id_atributo_valor' => $valorId,
                                    'dPrecio_extra' => 0
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('productos.show', $producto->id_producto)
                ->with('success', 'Producto actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()]);
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            DB::beginTransaction();

            $producto->eliminarImagenes();
            $producto->etiquetas()->detach();
            DB::table('tbl_producto_atributos')->where('id_producto', $producto->id_producto)->delete();
            $producto->delete();
            
            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function atributos($id)
    {
        $producto = Producto::with(['variaciones.atributos.atributo', 'variaciones.atributos.valor'])
            ->findOrFail($id);
        
        // Vista simplificada - solo muestra atributos y variaciones existentes
        return view('productos.atributos', compact('producto'));
    }

    public function asignarAtributos($id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($id);
        
        // Cargar atributos con valores activos
        $atributos = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('iOrden');
        }])->where('bActivo', true)->get();
        
        return view('productos.asignar-atributos', compact('producto', 'atributos'));
    }

    public function guardarAtributos(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        // Validación más flexible para permitir múltiples atributos
        $request->validate([
            'atributos' => 'nullable|array',
            'atributos.*.id_atributo' => 'required|exists:tbl_atributos,id_atributo',
            'atributos.*.valores' => 'required|array|min:1',
            'atributos.*.valores.*.id_valor' => 'required|exists:tbl_atributo_valores,id_atributo_valor',
            'atributos.*.valores.*.precio_extra' => 'nullable|numeric|min:0'
        ], [
            'atributos.*.id_atributo.required' => 'El atributo es obligatorio',
            'atributos.*.id_atributo.exists' => 'El atributo seleccionado no existe',
            'atributos.*.valores.required' => 'Debe seleccionar al menos un valor para cada atributo',
            'atributos.*.valores.min' => 'Debe seleccionar al menos un valor para cada atributo',
            'atributos.*.valores.*.id_valor.required' => 'El valor del atributo es obligatorio',
            'atributos.*.valores.*.id_valor.exists' => 'El valor seleccionado no existe',
            'atributos.*.valores.*.precio_extra.numeric' => 'El precio extra debe ser un número válido',
            'atributos.*.valores.*.precio_extra.min' => 'El precio extra no puede ser negativo'
        ]);

        try {
            DB::beginTransaction();

            // Eliminar atributos existentes del producto
            DB::table('tbl_producto_atributos')->where('id_producto', $producto->id_producto)->delete();

            if ($request->has('atributos')) {
                foreach ($request->atributos as $atributoData) {
                    // Verificar que el atributo tenga valores seleccionados
                    if (isset($atributoData['valores']) && is_array($atributoData['valores'])) {
                        foreach ($atributoData['valores'] as $valorData) {
                            // Verificar que todos los datos necesarios estén presentes
                            if (isset($valorData['id_valor'])) {
                                DB::table('tbl_producto_atributos')->insert([
                                    'id_producto' => $producto->id_producto,
                                    'id_atributo' => $atributoData['id_atributo'],
                                    'id_atributo_valor' => $valorData['id_valor'],
                                    'dPrecio_extra' => isset($valorData['precio_extra']) ? floatval($valorData['precio_extra']) : 0
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('productos.asignar-atributos', $producto->id_producto)
                ->with('success', 'Atributos asignados exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Mensaje de error más descriptivo
            $errorMessage = 'Error al asignar atributos: ' . $e->getMessage();
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                $errorMessage = 'Error: Existe un problema con los datos enviados. Verifica que todos los valores sean válidos.';
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    public function valoraciones()
    {
        $productos = Producto::with(['variaciones.atributos.valor', 'variaciones.atributos.atributo', 'marca', 'categoria'])
            ->whereHas('variaciones')
            ->orderBy('vNombre')
            ->get();
            
        return view('productos.valoraciones', compact('productos'));
    }
}