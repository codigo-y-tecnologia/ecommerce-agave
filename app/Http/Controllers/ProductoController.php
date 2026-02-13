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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['marca', 'categoria', 'etiquetas']);
        
        // Agregar búsqueda por nombre o SKU
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('vNombre', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('vCodigo_barras', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        $productos = $query->get();
        
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
        // Validación personalizada para precio de oferta
        $validator = Validator::make($request->all(), [
            'vCodigo_barras' => [
                'required',
                'max:15',
                'unique:tbl_productos,vCodigo_barras',
                'regex:/^[A-Za-z0-9]+$/'
            ],
            'vNombre' => [
                'required',
                'max:100',
                'unique:tbl_productos,vNombre'
            ],
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',
            'dPrecio_compra' => 'nullable|numeric|min:0|max:9999999.99',
            'dPrecio_venta' => 'required|numeric|min:0|max:9999999.99',
            'iStock' => 'required|integer|min:0|max:9999',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagenes' => 'nullable|array|max:8',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp,jfif,svg|max:5120',
            'atributos' => 'nullable|array',
            // NUEVAS VALIDACIONES PARA DIMENSIONES Y PESO
            'dPeso' => 'nullable|numeric|min:0|max:999.999',
            'dLargo_cm' => 'nullable|numeric|min:0|max:999.99',
            'dAncho_cm' => 'nullable|numeric|min:0|max:999.99',
            'dAlto_cm' => 'nullable|numeric|min:0|max:999.99',
            'vClase_envio' => 'nullable|in:estandar,express,fragil,grandes_dimensiones',
            'etiquetas_especiales' => 'nullable|array',
            'etiquetas_especiales.*' => 'in:nuevo,popular,oferta,destacado',
            // NUEVAS VALIDACIONES PARA OFERTA (CORREGIDAS)
            'bTiene_oferta' => 'nullable|in:0,1',
            'dPrecio_oferta' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('bTiene_oferta') == 1 && $value !== null) {
                        $precioVenta = $request->dPrecio_venta;
                        if ($value >= $precioVenta) {
                            $fail('El precio de oferta debe ser menor que el precio de venta.');
                        }
                    }
                }
            ],
            'dFecha_inicio_oferta' => 'nullable|date',
            'dFecha_fin_oferta' => 'nullable|date|after_or_equal:dFecha_inicio_oferta',
            'vMotivo_oferta' => 'nullable|string|max:255',
        ], [
            'vCodigo_barras.required' => 'El SKU es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este SKU',
            'vCodigo_barras.regex' => 'El SKU solo puede contener letras y números',
            'vCodigo_barras.max' => 'El SKU no puede exceder los 15 caracteres',
            'vNombre.required' => 'El nombre del producto es obligatorio',
            'vNombre.unique' => 'Ya existe un producto con este nombre',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'dPrecio_venta.min' => 'El precio de venta no puede ser negativo',
            'dPrecio_venta.max' => 'El precio de venta máximo es 9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'iStock.max' => 'El stock no puede ser mayor a 9,999 unidades',
            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria',
            'imagenes.max' => 'No puedes subir más de 8 imágenes',
            'imagenes.*.image' => 'Solo se permiten archivos de imagen',
            'imagenes.*.mimes' => 'Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP, JFIF, SVG',
            'imagenes.*.max' => 'Cada imagen no debe superar los 5MB',
            'dPeso.numeric' => 'El peso debe ser un número válido',
            'dPeso.min' => 'El peso no puede ser negativo',
            'dPeso.max' => 'El peso máximo es 999.999 kg',
            'dLargo_cm.numeric' => 'El largo debe ser un número válido',
            'dLargo_cm.min' => 'El largo no puede ser negativo',
            'dLargo_cm.max' => 'El largo máximo es 999.99 cm',
            'dAncho_cm.numeric' => 'El ancho debe ser un número válido',
            'dAncho_cm.min' => 'El ancho no puede ser negativo',
            'dAncho_cm.max' => 'El ancho máximo es 999.99 cm',
            'dAlto_cm.numeric' => 'El alto debe ser un número válido',
            'dAlto_cm.min' => 'El alto no puede ser negativo',
            'dAlto_cm.max' => 'El alto máximo es 999.99 cm',
            'vClase_envio.in' => 'La clase de envío seleccionada no es válida',
            'bTiene_oferta.in' => 'El valor de oferta debe ser 0 o 1',
            'dPrecio_oferta.numeric' => 'El precio de oferta debe ser un número válido',
            'dPrecio_oferta.min' => 'El precio de oferta no puede ser negativo',
            'dPrecio_oferta.max' => 'El precio de oferta máximo es 9,999,999.99',
            'dFecha_inicio_oferta.date' => 'La fecha de inicio de oferta debe ser una fecha válida',
            'dFecha_fin_oferta.date' => 'La fecha de fin de oferta debe ser una fecha válida',
            'dFecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'vMotivo_oferta.max' => 'El motivo de la oferta no puede exceder los 255 caracteres',
        ]);

        $validator->sometimes('dPrecio_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        $validator->sometimes('dFecha_inicio_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        $validator->sometimes('dFecha_fin_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $productoData = [
                'vCodigo_barras' => strtoupper($request->vCodigo_barras),
                'vNombre' => $request->vNombre,
                'tDescripcion_corta' => $request->tDescripcion_corta,
                'tDescripcion_larga' => $request->tDescripcion_larga,
                'dPrecio_compra' => $request->dPrecio_compra ?: null,
                'dPrecio_venta' => $request->dPrecio_venta,
                'iStock' => $request->iStock,
                'id_categoria' => $request->id_categoria,
                'id_marca' => $request->id_marca,
                'bActivo' => $request->has('bActivo') ? true : false,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $request->vClase_envio ?: null,
                'bTiene_oferta' => $request->input('bTiene_oferta', 0) == 1 ? true : false,
                'dPrecio_oferta' => $request->dPrecio_oferta ?: null,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta ?: null,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta ?: null,
                'vMotivo_oferta' => $request->vMotivo_oferta ?: null,
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

            if ($request->has('etiquetas_especiales')) {
                Log::info('Etiquetas especiales para producto ' . $producto->id_producto . ': ', $request->etiquetas_especiales);
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
        $validator = Validator::make($request->all(), [
            'vCodigo_barras' => [
                'required',
                'max:15',
                Rule::unique('tbl_productos', 'vCodigo_barras')->ignore($producto->id_producto, 'id_producto'),
                'regex:/^[A-Za-z0-9]+$/'
            ],
            'vNombre' => [
                'required',
                'max:100',
                Rule::unique('tbl_productos', 'vNombre')->ignore($producto->id_producto, 'id_producto')
            ],
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',
            'dPrecio_compra' => 'nullable|numeric|min:0|max:9999999.99',
            'dPrecio_venta' => 'required|numeric|min:0|max:9999999.99',
            'iStock' => 'required|integer|min:0|max:9999',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagenes' => 'nullable|array|max:8',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp,jfif,svg|max:5120',
            'imagenes_a_eliminar' => 'nullable|array',
            'imagenes_a_eliminar.*' => 'string',
            'atributos' => 'nullable|array',
            'dPeso' => 'nullable|numeric|min:0|max:999.999',
            'dLargo_cm' => 'nullable|numeric|min:0|max:999.99',
            'dAncho_cm' => 'nullable|numeric|min:0|max:999.99',
            'dAlto_cm' => 'nullable|numeric|min:0|max:999.99',
            'vClase_envio' => 'nullable|in:estandar,express,fragil,grandes_dimensiones',
            'etiquetas_especiales' => 'nullable|array',
            'etiquetas_especiales.*' => 'in:nuevo,popular,oferta,destacado',
            'bTiene_oferta' => 'nullable|in:0,1',
            'dPrecio_oferta' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('bTiene_oferta') == 1 && $value !== null) {
                        $precioVenta = $request->dPrecio_venta;
                        if ($value >= $precioVenta) {
                            $fail('El precio de oferta debe ser menor que el precio de venta.');
                        }
                    }
                }
            ],
            'dFecha_inicio_oferta' => 'nullable|date',
            'dFecha_fin_oferta' => 'nullable|date|after_or_equal:dFecha_inicio_oferta',
            'vMotivo_oferta' => 'nullable|string|max:255',
        ], [
            'vCodigo_barras.required' => 'El SKU es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este SKU',
            'vCodigo_barras.regex' => 'El SKU solo puede contener letras y números',
            'vCodigo_barras.max' => 'El SKU no puede exceder los 15 caracteres',
            'vNombre.required' => 'El nombre del producto es obligatorio',
            'vNombre.unique' => 'Ya existe un producto con este nombre',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'dPrecio_venta.min' => 'El precio de venta no puede ser negativo',
            'dPrecio_venta.max' => 'El precio de venta máximo es 9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'iStock.max' => 'El stock no puede ser mayor a 9,999 unidades',
            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria',
            'imagenes.max' => 'No puedes subir más de 8 imágenes',
            'imagenes.*.image' => 'Solo se permiten archivos de imagen',
            'imagenes.*.mimes' => 'Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP, JFIF, SVG',
            'imagenes.*.max' => 'Cada imagen no debe superar los 5MB',
            'dPeso.numeric' => 'El peso debe ser un número válido',
            'dPeso.min' => 'El peso no puede ser negativo',
            'dPeso.max' => 'El peso máximo es 999.999 kg',
            'dLargo_cm.numeric' => 'El largo debe ser un número válido',
            'dLargo_cm.min' => 'El largo no puede ser negativo',
            'dLargo_cm.max' => 'El largo máximo es 999.99 cm',
            'dAncho_cm.numeric' => 'El ancho debe ser un número válido',
            'dAncho_cm.min' => 'El ancho no puede ser negativo',
            'dAncho_cm.max' => 'El ancho máximo es 999.99 cm',
            'dAlto_cm.numeric' => 'El alto debe ser un número válido',
            'dAlto_cm.min' => 'El alto no puede ser negativo',
            'dAlto_cm.max' => 'El alto máximo es 999.99 cm',
            'vClase_envio.in' => 'La clase de envío seleccionada no es válida',
            'bTiene_oferta.in' => 'El valor de oferta debe ser 0 o 1',
            'dPrecio_oferta.numeric' => 'El precio de oferta debe ser un número válido',
            'dPrecio_oferta.min' => 'El precio de oferta no puede ser negativo',
            'dPrecio_oferta.max' => 'El precio de oferta máximo es 9,999,999.99',
            'dFecha_inicio_oferta.date' => 'La fecha de inicio de oferta debe ser una fecha válida',
            'dFecha_fin_oferta.date' => 'La fecha de fin de oferta debe ser una fecha válida',
            'dFecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'vMotivo_oferta.max' => 'El motivo de la oferta no puede exceder los 255 caracteres',
        ]);

        $validator->sometimes('dPrecio_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        $validator->sometimes('dFecha_inicio_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        $validator->sometimes('dFecha_fin_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $imagenesActuales = $producto->getNumeroImagenes();
            $nuevasImagenes = $request->hasFile('imagenes') ? count($request->file('imagenes')) : 0;
            $imagenesAEliminar = $request->imagenes_a_eliminar ? count($request->imagenes_a_eliminar) : 0;
            
            $espacioDisponible = $imagenesActuales - $imagenesAEliminar + $nuevasImagenes;
            
            if ($espacioDisponible > 8) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['imagenes' => 'No puedes tener más de 8 imágenes. Actualmente tienes ' . $imagenesActuales . ' imágenes.']);
            }

            if ($request->has('imagenes_a_eliminar') && is_array($request->imagenes_a_eliminar)) {
                $producto->eliminarImagenesEspecificas($request->imagenes_a_eliminar);
            }

            $producto->update([
                'vCodigo_barras' => strtoupper($request->vCodigo_barras),
                'vNombre' => $request->vNombre,
                'tDescripcion_corta' => $request->tDescripcion_corta,
                'tDescripcion_larga' => $request->tDescripcion_larga,
                'dPrecio_compra' => $request->dPrecio_compra ?: null,
                'dPrecio_venta' => $request->dPrecio_venta,
                'iStock' => $request->iStock,
                'id_categoria' => $request->id_categoria,
                'id_marca' => $request->id_marca,
                'bActivo' => $request->has('bActivo') ? true : false,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $request->vClase_envio ?: null,
                'bTiene_oferta' => $request->input('bTiene_oferta', 0) == 1 ? true : false,
                'dPrecio_oferta' => $request->dPrecio_oferta ?: null,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta ?: null,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta ?: null,
                'vMotivo_oferta' => $request->vMotivo_oferta ?: null,
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

            if ($request->has('etiquetas_especiales')) {
                Log::info('Etiquetas especiales actualizadas para producto ' . $producto->id_producto . ': ', $request->etiquetas_especiales);
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

            $producto->eliminarTodasLasImagenes();
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
        
        return view('productos.atributos', compact('producto'));
    }

    public function asignarAtributos($id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($id);
        
        $atributos = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('iOrden');
        }])->where('bActivo', true)->get();
        
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

            DB::table('tbl_producto_atributos')->where('id_producto', $producto->id_producto)->delete();

            if ($request->has('atributos')) {
                foreach ($request->atributos as $atributoData) {
                    if (isset($atributoData['valores']) && is_array($atributoData['valores'])) {
                        foreach ($atributoData['valores'] as $valorData) {
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