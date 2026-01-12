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
        $categorias = Categoria::orderBy('vNombre', 'asc')->get();
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
        $categorias = Categoria::orderBy('vNombre', 'asc')->get();
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
        
        $atributos = Atributo::with('valoresActivos')->where('bActivo', true)->get();
        
        return view('productos.atributos', compact('producto', 'atributos'));
    }

    public function guardarVariaciones(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $request->validate([
            'variaciones' => 'required|array|min:1',
            'variaciones.*.vSKU' => 'required|unique:tbl_producto_variaciones,vSKU',
            'variaciones.*.dPrecio' => 'required|numeric|min:0',
            'variaciones.*.iStock' => 'required|integer|min:0',
            'variaciones.*.atributos' => 'required|array|min:1',
            'variaciones.*.atributos.*.id_atributo' => 'required|exists:tbl_atributos,id_atributo',
            'variaciones.*.atributos.*.id_atributo_valor' => 'required|exists:tbl_atributo_valores,id_atributo_valor'
        ]);

        try {
            DB::beginTransaction();

            $producto->variaciones()->delete();

            foreach ($request->variaciones as $variacionData) {
                $variacion = ProductoVariacion::create([
                    'id_producto' => $producto->id_producto,
                    'vSKU' => $variacionData['vSKU'],
                    'vCodigo_barras' => $variacionData['vCodigo_barras'] ?? null,
                    'dPrecio' => $variacionData['dPrecio'],
                    'dPrecio_oferta' => $variacionData['dPrecio_oferta'] ?? null,
                    'iStock' => $variacionData['iStock'],
                    'dPeso' => $variacionData['dPeso'] ?? null,
                    'dAncho' => $variacionData['dAncho'] ?? null,
                    'dAlto' => $variacionData['dAlto'] ?? null,
                    'dProfundidad' => $variacionData['dProfundidad'] ?? null,
                    'bActivo' => true
                ]);

                foreach ($variacionData['atributos'] as $atributoData) {
                    VariacionAtributo::create([
                        'id_variacion' => $variacion->id_variacion,
                        'id_atributo' => $atributoData['id_atributo'],
                        'id_atributo_valor' => $atributoData['id_atributo_valor']
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variaciones guardadas exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar variaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarCombinaciones(Request $request)
    {
        $request->validate([
            'atributos_seleccionados' => 'required|array|min:1',
            'atributos_seleccionados.*.id_atributo' => 'required|exists:tbl_atributos,id_atributo',
            'atributos_seleccionados.*.valores' => 'required|array|min:1',
            'atributos_seleccionados.*.valores.*' => 'exists:tbl_atributo_valores,id_atributo_valor'
        ]);

        try {
            $atributos = $request->atributos_seleccionados;
            
            $arraysParaCombinar = [];
            foreach ($atributos as $atributo) {
                $valores = [];
                foreach ($atributo['valores'] as $idValor) {
                    $valor = AtributoValor::with('atributo')->find($idValor);
                    $valores[] = [
                        'id_atributo' => $atributo['id_atributo'],
                        'id_atributo_valor' => $valor->id_atributo_valor,
                        'nombre_atributo' => $valor->atributo->vNombre,
                        'valor' => $valor->vValor,
                        'color' => $valor->vHexColor ?? null,
                        'imagen' => $valor->vImagenUrl ?? null
                    ];
                }
                $arraysParaCombinar[] = $valores;
            }

            $combinaciones = $this->generarCombinacionesRecursivo($arraysParaCombinar);
            
            $prefijo = 'SKU-' . strtoupper(substr(md5(time()), 0, 6)) . '-';
            $combinacionesConSKU = [];
            
            foreach ($combinaciones as $index => $combinacion) {
                $nombresAtributos = [];
                foreach ($combinacion as $atributo) {
                    $nombresAtributos[] = substr($atributo['nombre_atributo'], 0, 3) . '-' . substr($atributo['valor'], 0, 3);
                }
                
                $sku = $prefijo . ($index + 1);
                $codigoBarras = 'CB' . str_pad($index + 1, 10, '0', STR_PAD_LEFT);
                
                $combinacionesConSKU[] = [
                    'sku' => $sku,
                    'codigo_barras' => $codigoBarras,
                    'atributos' => $combinacion,
                    'precio' => 0,
                    'precio_oferta' => null,
                    'stock' => 0,
                    'peso' => null,
                    'ancho' => null,
                    'alto' => null,
                    'profundidad' => null
                ];
            }

            return response()->json([
                'success' => true,
                'combinaciones' => $combinacionesConSKU,
                'total' => count($combinacionesConSKU)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar combinaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generarCombinacionesRecursivo($arrays, $i = 0)
    {
        if ($i == count($arrays)) {
            return [[]];
        }
        
        $combinacionesTemp = $this->generarCombinacionesRecursivo($arrays, $i + 1);
        $resultado = [];
        
        foreach ($arrays[$i] as $elemento) {
            foreach ($combinacionesTemp as $combinacion) {
                $resultado[] = array_merge([$elemento], $combinacion);
            }
        }
        
        return $resultado;
    }
    
    public function asignarAtributos($id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($id);
        $atributos = Atributo::with('valoresActivos')->where('bActivo', true)->get();
        
        return view('productos.asignar-atributos', compact('producto', 'atributos'));
    }

    public function guardarAtributos(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $request->validate([
            'atributos' => 'nullable|array',
            'atributos.*.id_atributo' => 'required|exists:tbl_atributos,id_atributo',
            'atributos.*.valores' => 'required|array|min:1',
            'atributos.*.valores.*.id_valor' => 'required|exists:tbl_atributo_valores,id_atributo_valor',
            'atributos.*.valores.*.precio_extra' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            DB::table('tbl_producto_atributos')->where('id_producto', $producto->id_producto)->delete();

            if ($request->has('atributos')) {
                foreach ($request->atributos as $atributoData) {
                    foreach ($atributoData['valores'] as $valorData) {
                        DB::table('tbl_producto_atributos')->insert([
                            'id_producto' => $producto->id_producto,
                            'id_atributo' => $atributoData['id_atributo'],
                            'id_atributo_valor' => $valorData['id_valor'],
                            'dPrecio_extra' => $valorData['precio_extra'] ?? 0
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('productos.asignar-atributos', $producto->id_producto)
                ->with('success', 'Atributos asignados exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al asignar atributos: ' . $e->getMessage()]);
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