<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoVariacion;
use App\Models\VariacionAtributo;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Etiqueta;
use App\Models\Impuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class VariacionController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['marca', 'categoria', 'variaciones.atributos.valor'])
            ->whereHas('valoresAtributos')
            ->orderBy('vNombre')
            ->get();
            
        return view('variaciones.index', compact('productos'));
    }

    public function show($id)
    {
        $producto = Producto::with(['variaciones.atributos.valor', 'variaciones.atributos.atributo', 'marca', 'categoria'])
            ->findOrFail($id);
            
        return view('variaciones.show', compact('producto'));
    }

    /**
     * Mostrar detalle de una variación específica
     */
    public function showVariacion($producto_id, $variacion_id)
    {
        $producto = Producto::with(['marca', 'categoria', 'impuestos'])->findOrFail($producto_id);
        $variacion = ProductoVariacion::with(['atributos.valor', 'atributos.atributo', 'impuesto'])
            ->findOrFail($variacion_id);
        
        if ($variacion->id_producto != $producto_id) {
            return redirect()->route('variaciones.index')
                ->with('error', 'La variación no pertenece a este producto');
        }
        
        return view('variaciones.show-variacion', compact('producto', 'variacion'));
    }

    public function create($producto_id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($producto_id);
        
        if ($producto->valoresAtributos->count() === 0) {
            return redirect()->route('variaciones.show', $producto->id_producto)
                ->with('warning', 'Primero debes asignar atributos al producto desde la página de edición.')
                ->with('swal_error', true);
        }
        
        $atributos = [];
        foreach ($producto->valoresAtributos as $valor) {
            $nombreAtributo = $valor->atributo->vNombre;
            if (!isset($atributos[$nombreAtributo])) {
                $atributos[$nombreAtributo] = [];
            }
            $atributos[$nombreAtributo][] = $valor;
        }
        
        $categorias = Categoria::with(['hijos' => function($query) {
            $query->where('bActivo', true)
                  ->with(['hijos' => function($subQuery) {
                      $subQuery->where('bActivo', true)
                               ->orderBy('vNombre');
                  }])
                  ->orderBy('vNombre');
        }])
        ->whereNull('id_categoria_padre')
        ->where('bActivo', true)
        ->orderBy('vNombre')
        ->get();
        
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        
        // CORREGIDO: Eliminado orderBy('iOrden')
        $atributosGlobales = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('vValor');
        }])->where('bActivo', true)->get();
        
        // Obtener impuestos activos
        $impuestos = Impuesto::where('bActivo', true)->orderBy('vNombre')->get();
        
        return view('variaciones.create', compact(
            'producto', 
            'atributos', 
            'categorias', 
            'marcas', 
            'etiquetas', 
            'atributosGlobales',
            'impuestos'
        ));
    }

    public function store(Request $request, $producto_id)
    {
        $productoPadre = Producto::findOrFail($producto_id);
        
        $validator = Validator::make($request->all(), [
            'vSKU' => 'required|unique:tbl_producto_variaciones,vSKU|max:50',
            'dPrecio' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dPrecio_oferta' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio de oferta debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'iStock' => 'required|integer|min:0|max:999999',
            'bTiene_oferta' => 'nullable|in:0,1',
            'dFecha_inicio_oferta' => 'nullable|required_if:bTiene_oferta,1|date',
            'dFecha_fin_oferta' => 'nullable|required_if:bTiene_oferta,1|date|after_or_equal:dFecha_inicio_oferta',
            'vMotivo_oferta' => 'nullable|string|max:255',
            'dPeso' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.999',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,3})?$/', $value)) {
                        $fail('El peso debe tener máximo 3 dígitos enteros y 3 decimales.');
                    }
                }
            ],
            'dLargo_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El largo debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dAncho_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El ancho debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dAlto_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El alto debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'vClase_envio' => 'nullable|in:estandar,express,fragil,grandes_dimensiones|max:50',
            'tDescripcion' => 'nullable|string',
            'bActivo' => 'nullable|boolean',
            'id_impuesto' => 'nullable|exists:tbl_impuestos,id_impuesto',
            
            // CAMPOS DE IMÁGENES MÚLTIPLES
            'imagen_principal' => 'nullable|image|max:5120|mimes:jpeg,jpg,png',
            'gif' => 'nullable|mimes:gif|max:10240',
            'imagenes_adicionales' => 'nullable|array|max:7',
            'imagenes_adicionales.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor',
        ], [
            'vSKU.required' => 'El SKU es obligatorio',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'vSKU.max' => 'El SKU no puede exceder los 50 caracteres',
            'dPrecio.required' => 'El precio es obligatorio',
            'dPrecio.numeric' => 'El precio debe ser un número válido',
            'dPrecio.min' => 'El precio no puede ser negativo',
            'dPrecio.max' => 'El precio no puede exceder $9,999,999.99',
            'dPrecio_oferta.numeric' => 'El precio de oferta debe ser un número válido',
            'dPrecio_oferta.min' => 'El precio de oferta no puede ser negativo',
            'dPrecio_oferta.max' => 'El precio de oferta no puede exceder $9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'iStock.max' => 'El stock no puede exceder 999,999 unidades',
            'dFecha_inicio_oferta.required_if' => 'La fecha de inicio es obligatoria cuando la oferta está activa',
            'dFecha_fin_oferta.required_if' => 'La fecha de fin es obligatoria cuando la oferta está activa',
            'dFecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'dPeso.numeric' => 'El peso debe ser un número válido',
            'dPeso.min' => 'El peso no puede ser negativo',
            'dPeso.max' => 'El peso no puede exceder 999.999 kg',
            'dLargo_cm.numeric' => 'El largo debe ser un número válido',
            'dLargo_cm.min' => 'El largo no puede ser negativo',
            'dLargo_cm.max' => 'El largo no puede exceder 999.99 cm',
            'dAncho_cm.numeric' => 'El ancho debe ser un número válido',
            'dAncho_cm.min' => 'El ancho no puede ser negativo',
            'dAncho_cm.max' => 'El ancho no puede exceder 999.99 cm',
            'dAlto_cm.numeric' => 'El alto debe ser un número válido',
            'dAlto_cm.min' => 'El alto no puede ser negativo',
            'dAlto_cm.max' => 'El alto no puede exceder 999.99 cm',
            'vClase_envio.in' => 'La clase de envío seleccionada no es válida',
            'vClase_envio.max' => 'La clase de envío no puede exceder los 50 caracteres',
            'id_impuesto.exists' => 'El impuesto seleccionado no existe',
            
            // Mensajes para imágenes
            'imagen_principal.image' => 'El archivo debe ser una imagen válida',
            'imagen_principal.max' => 'La imagen principal no debe pesar más de 5MB',
            'imagen_principal.mimes' => 'La imagen principal debe ser JPG, JPEG o PNG',
            'gif.mimes' => 'El archivo debe ser un GIF',
            'gif.max' => 'El GIF no debe pesar más de 10MB',
            'imagenes_adicionales.max' => 'No puedes subir más de 7 imágenes adicionales',
            'imagenes_adicionales.*.image' => 'Solo se permiten archivos de imagen',
            'imagenes_adicionales.*.mimes' => 'Formatos permitidos: JPG, JPEG, PNG, WEBP',
            'imagenes_adicionales.*.max' => 'Cada imagen no debe superar los 5MB',
            
            'atributos.required' => 'Debes seleccionar valores para todos los atributos',
            'atributos.*.required' => 'Debes seleccionar un valor para cada atributo',
            'atributos.*.exists' => 'El valor seleccionado no es válido',
        ]);

        // Validación condicional para precio de oferta
        $validator->sometimes('dPrecio_oferta', 'required|lt:dPrecio', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', true);
        }

        try {
            DB::beginTransaction();

            // Determinar clase de envío
            $claseEnvio = $request->vClase_envio;
            if (empty($claseEnvio) && $productoPadre->vClase_envio) {
                $claseEnvio = $productoPadre->vClase_envio;
            } elseif (empty($claseEnvio)) {
                $claseEnvio = 'estandar';
            }

            // Crear la variación
            $variacionData = [
                'id_producto' => $producto_id,
                'vSKU' => strtoupper($request->vSKU),
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta ?? null,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta ?? null,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta ?? null,
                'vMotivo_oferta' => $request->vMotivo_oferta ?? null,
                'bTiene_oferta' => $request->has('bTiene_oferta') && $request->bTiene_oferta == '1' ? 1 : 0,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $claseEnvio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0,
                'id_impuesto' => $request->id_impuesto ?: null,
            ];

            $variacion = ProductoVariacion::create($variacionData);

            // Guardar imagen principal si existe
            if ($request->hasFile('imagen_principal')) {
                $variacion->guardarImagenPrincipal($request->file('imagen_principal'));
            }

            // Guardar GIF si existe
            if ($request->hasFile('gif')) {
                $variacion->guardarGif($request->file('gif'));
            }

            // Guardar imágenes adicionales si existen
            if ($request->hasFile('imagenes_adicionales')) {
                $variacion->guardarImagenesAdicionales($request->file('imagenes_adicionales'));
            }

            // Guardar relaciones con atributos
            foreach ($request->atributos as $atributo_id => $valor_id) {
                $atributoValor = AtributoValor::where('id_atributo_valor', $valor_id)
                    ->where('id_atributo', $atributo_id)
                    ->first();
                
                if ($atributoValor) {
                    VariacionAtributo::create([
                        'id_variacion' => $variacion->id_variacion,
                        'id_atributo' => $atributo_id,
                        'id_atributo_valor' => $valor_id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('variaciones.show', $producto_id)
                ->with('success', 'Variación creada exitosamente')
                ->with('swal_success', true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al crear variación: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear variación: ' . $e->getMessage()])
                ->with('swal_error', true);
        }
    }

    public function edit($producto_id, $variacion_id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($producto_id);
        $variacion = ProductoVariacion::with('atributos.valor', 'atributos.atributo')
            ->findOrFail($variacion_id);
        
        if ($variacion->id_producto != $producto_id) {
            return redirect()->route('variaciones.index')
                ->with('error', 'La variación no pertenece a este producto')
                ->with('swal_error', true);
        }
        
        $atributos = [];
        foreach ($producto->valoresAtributos as $valor) {
            $nombreAtributo = $valor->atributo->vNombre;
            if (!isset($atributos[$nombreAtributo])) {
                $atributos[$nombreAtributo] = [];
            }
            $atributos[$nombreAtributo][] = $valor;
        }
        
        $valoresSeleccionados = [];
        foreach ($variacion->atributos as $atributoVariacion) {
            $valoresSeleccionados[$atributoVariacion->id_atributo] = $atributoVariacion->id_atributo_valor;
        }
        
        $categorias = Categoria::with(['hijos' => function($query) {
            $query->where('bActivo', true)
                  ->with(['hijos' => function($subQuery) {
                      $subQuery->where('bActivo', true)
                               ->orderBy('vNombre');
                  }])
                  ->orderBy('vNombre');
        }])
        ->whereNull('id_categoria_padre')
        ->where('bActivo', true)
        ->orderBy('vNombre')
        ->get();
        
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        
        // CORREGIDO: Eliminado orderBy('iOrden')
        $atributosGlobales = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('vValor');
        }])->where('bActivo', true)->get();
        
        // Obtener impuestos activos
        $impuestos = Impuesto::where('bActivo', true)->orderBy('vNombre')->get();
        
        // Obtener nombres de archivos de imágenes adicionales
        $imagenesAdicionalesNombres = $variacion->getNombresArchivosImagenesAdicionales();
        
        return view('variaciones.edit', compact(
            'producto', 
            'variacion', 
            'atributos', 
            'valoresSeleccionados',
            'categorias',
            'marcas',
            'etiquetas',
            'atributosGlobales',
            'impuestos',
            'imagenesAdicionalesNombres'
        ));
    }

    public function update(Request $request, $producto_id, $variacion_id)
    {
        $productoPadre = Producto::findOrFail($producto_id);
        $variacion = ProductoVariacion::findOrFail($variacion_id);
        
        if ($variacion->id_producto != $producto_id) {
            return redirect()->route('variaciones.index')
                ->with('error', 'La variación no pertenece a este producto')
                ->with('swal_error', true);
        }
        
        $validator = Validator::make($request->all(), [
            'vSKU' => [
                'required',
                'max:50',
                Rule::unique('tbl_producto_variaciones', 'vSKU')->ignore($variacion_id, 'id_variacion')
            ],
            'dPrecio' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dPrecio_oferta' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio de oferta debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'iStock' => 'required|integer|min:0|max:999999',
            'bTiene_oferta' => 'nullable|in:0,1',
            'dFecha_inicio_oferta' => 'nullable|required_if:bTiene_oferta,1|date',
            'dFecha_fin_oferta' => 'nullable|required_if:bTiene_oferta,1|date|after_or_equal:dFecha_inicio_oferta',
            'vMotivo_oferta' => 'nullable|string|max:255',
            'dPeso' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.999',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,3})?$/', $value)) {
                        $fail('El peso debe tener máximo 3 dígitos enteros y 3 decimales.');
                    }
                }
            ],
            'dLargo_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El largo debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dAncho_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El ancho debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dAlto_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El alto debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'vClase_envio' => 'nullable|in:estandar,express,fragil,grandes_dimensiones|max:50',
            'tDescripcion' => 'nullable|string',
            'bActivo' => 'nullable|boolean',
            'id_impuesto' => 'nullable|exists:tbl_impuestos,id_impuesto',
            
            // CAMPOS DE IMÁGENES MÚLTIPLES
            'imagen_principal' => 'nullable|image|max:5120|mimes:jpeg,jpg,png',
            'gif' => 'nullable|mimes:gif|max:10240',
            'imagenes_adicionales' => 'nullable|array|max:7',
            'imagenes_adicionales.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'imagenes_a_eliminar' => 'nullable|array',
            'imagenes_a_eliminar.*' => 'string',
            'eliminar_imagen_principal' => 'nullable|in:0,1',
            'eliminar_gif' => 'nullable|in:0,1',
            
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor',
        ], [
            'vSKU.required' => 'El SKU es obligatorio',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'vSKU.max' => 'El SKU no puede exceder los 50 caracteres',
            'dPrecio.required' => 'El precio es obligatorio',
            'dPrecio.numeric' => 'El precio debe ser un número válido',
            'dPrecio.min' => 'El precio no puede ser negativo',
            'dPrecio.max' => 'El precio no puede exceder $9,999,999.99',
            'dPrecio_oferta.numeric' => 'El precio de oferta debe ser un número válido',
            'dPrecio_oferta.min' => 'El precio de oferta no puede ser negativo',
            'dPrecio_oferta.max' => 'El precio de oferta no puede exceder $9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'iStock.max' => 'El stock no puede exceder 999,999 unidades',
            'dFecha_inicio_oferta.required_if' => 'La fecha de inicio es obligatoria cuando la oferta está activa',
            'dFecha_fin_oferta.required_if' => 'La fecha de fin es obligatoria cuando la oferta está activa',
            'dFecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'dPeso.numeric' => 'El peso debe ser un número válido',
            'dPeso.min' => 'El peso no puede ser negativo',
            'dPeso.max' => 'El peso no puede exceder 999.999 kg',
            'dLargo_cm.numeric' => 'El largo debe ser un número válido',
            'dLargo_cm.min' => 'El largo no puede ser negativo',
            'dLargo_cm.max' => 'El largo no puede exceder 999.99 cm',
            'dAncho_cm.numeric' => 'El ancho debe ser un número válido',
            'dAncho_cm.min' => 'El ancho no puede ser negativo',
            'dAncho_cm.max' => 'El ancho no puede exceder 999.99 cm',
            'dAlto_cm.numeric' => 'El alto debe ser un número válido',
            'dAlto_cm.min' => 'El alto no puede ser negativo',
            'dAlto_cm.max' => 'El alto no puede exceder 999.99 cm',
            'vClase_envio.in' => 'La clase de envío seleccionada no es válida',
            'vClase_envio.max' => 'La clase de envío no puede exceder los 50 caracteres',
            'id_impuesto.exists' => 'El impuesto seleccionado no existe',
            
            // Mensajes para imágenes
            'imagen_principal.image' => 'El archivo debe ser una imagen válida',
            'imagen_principal.max' => 'La imagen principal no debe pesar más de 5MB',
            'imagen_principal.mimes' => 'La imagen principal debe ser JPG, JPEG o PNG',
            'gif.mimes' => 'El archivo debe ser un GIF',
            'gif.max' => 'El GIF no debe pesar más de 10MB',
            'imagenes_adicionales.max' => 'No puedes subir más de 7 imágenes adicionales',
            'imagenes_adicionales.*.image' => 'Solo se permiten archivos de imagen',
            'imagenes_adicionales.*.mimes' => 'Formatos permitidos: JPG, JPEG, PNG, WEBP',
            'imagenes_adicionales.*.max' => 'Cada imagen no debe superar los 5MB',
            
            'atributos.required' => 'Debes seleccionar valores para todos los atributos',
            'atributos.*.required' => 'Debes seleccionar un valor para cada atributo',
            'atributos.*.exists' => 'El valor seleccionado no es válido',
        ]);

        // Validación condicional para precio de oferta
        $validator->sometimes('dPrecio_oferta', 'required|lt:dPrecio', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', true);
        }

        try {
            DB::beginTransaction();

            // Validar límite de imágenes
            $imagenesActuales = $variacion->numero_imagenes;
            $nuevasImagenes = 0;
            
            if ($request->hasFile('imagenes_adicionales')) {
                $nuevasImagenes = count($request->file('imagenes_adicionales'));
            }
            
            $imagenesAEliminar = $request->imagenes_a_eliminar ? count($request->imagenes_a_eliminar) : 0;
            
            $espacioDisponible = $imagenesActuales - $imagenesAEliminar + 
                                ($request->hasFile('imagen_principal') ? 1 : 0) + 
                                ($request->hasFile('gif') ? 1 : 0) + 
                                $nuevasImagenes;
            
            if ($espacioDisponible > 9) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['imagenes_adicionales' => 'No puedes tener más de 9 archivos multimedia (1 principal + 1 gif + 7 adicionales). Actualmente tienes ' . $imagenesActuales . ' archivos.'])
                    ->with('swal_error', true);
            }

            // Eliminar imágenes adicionales seleccionadas
            if ($request->has('imagenes_a_eliminar') && is_array($request->imagenes_a_eliminar) && count($request->imagenes_a_eliminar) > 0) {
                $variacion->eliminarImagenesAdicionalesEspecificas($request->imagenes_a_eliminar);
            }

            // Determinar clase de envío
            $claseEnvio = $request->vClase_envio;
            if (empty($claseEnvio) && $productoPadre->vClase_envio) {
                $claseEnvio = $productoPadre->vClase_envio;
            } elseif (empty($claseEnvio)) {
                $claseEnvio = $variacion->vClase_envio ?: 'estandar';
            }

            // Actualizar datos básicos
            $updateData = [
                'vSKU' => strtoupper($request->vSKU),
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta ?? null,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta ?? null,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta ?? null,
                'vMotivo_oferta' => $request->vMotivo_oferta ?? null,
                'bTiene_oferta' => $request->has('bTiene_oferta') && $request->bTiene_oferta == '1' ? 1 : 0,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $claseEnvio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0,
                'id_impuesto' => $request->id_impuesto ?: null,
            ];

            $variacion->update($updateData);

            // Gestionar imagen principal
            if ($request->hasFile('imagen_principal')) {
                // Subir nueva imagen principal
                $variacion->guardarImagenPrincipal($request->file('imagen_principal'));
            } elseif ($request->has('eliminar_imagen_principal') && $request->eliminar_imagen_principal == '1') {
                // Eliminar imagen principal existente
                $variacion->eliminarImagenPrincipal();
            }

            // Gestionar GIF
            if ($request->hasFile('gif')) {
                $variacion->guardarGif($request->file('gif'));
            } elseif ($request->has('eliminar_gif') && $request->eliminar_gif == '1') {
                $variacion->eliminarGif();
            }

            // Guardar nuevas imágenes adicionales
            if ($request->hasFile('imagenes_adicionales')) {
                $variacion->guardarImagenesAdicionales($request->file('imagenes_adicionales'));
            }

            // Actualizar relaciones con atributos
            $variacion->atributos()->delete();
            
            foreach ($request->atributos as $atributo_id => $valor_id) {
                $atributoValor = AtributoValor::where('id_atributo_valor', $valor_id)
                    ->where('id_atributo', $atributo_id)
                    ->first();
                
                if ($atributoValor) {
                    VariacionAtributo::create([
                        'id_variacion' => $variacion->id_variacion,
                        'id_atributo' => $atributo_id,
                        'id_atributo_valor' => $valor_id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('variaciones.show.variacion', ['producto_id' => $producto_id, 'variacion_id' => $variacion->id_variacion])
                ->with('success', 'Variación actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al actualizar variación: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar variación: ' . $e->getMessage()])
                ->with('swal_error', true);
        }
    }

    public function destroy($producto_id, $variacion_id)
    {
        try {
            DB::beginTransaction();

            $variacion = ProductoVariacion::findOrFail($variacion_id);
            
            if ($variacion->id_producto != $producto_id) {
                return redirect()->route('variaciones.index')
                    ->with('error', 'La variación no pertenece a este producto')
                    ->with('swal_error', true);
            }
            
            // Eliminar todas las imágenes asociadas
            $variacion->eliminarTodasLasImagenes();
            
            // Eliminar relaciones con atributos
            $variacion->atributos()->delete();
            
            // Eliminar la variación
            $variacion->delete();

            DB::commit();

            return redirect()->route('variaciones.show', $producto_id)
                ->with('success', 'Variación eliminada exitosamente')
                ->with('swal_success', true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al eliminar variación: ' . $e->getMessage());
            
            return redirect()->route('variaciones.show', $producto_id)
                ->with('error', 'Error al eliminar variación: ' . $e->getMessage())
                ->with('swal_error', true);
        }
    }

    // Métodos para formularios rápidos (API)
    public function quickCreateCategoria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_categorias,vNombre',
            'vSlug' => 'required|max:100|unique:tbl_categorias,vSlug',
            'id_categoria_padre' => 'nullable|exists:tbl_categorias,id_categoria',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoria = new Categoria();
            $categoria->vNombre = $request->vNombre;
            $categoria->vSlug = $request->vSlug;
            $categoria->id_categoria_padre = $request->id_categoria_padre;
            $categoria->bActivo = true;
            
            $ultimoOrden = Categoria::where('id_categoria_padre', $request->id_categoria_padre)->max('iOrden');
            $categoria->iOrden = $ultimoOrden ? $ultimoOrden + 1 : 0;
            
            $categoria->save();

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

    public function quickCreateMarca(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_marcas,vNombre'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $marca = Marca::create([
                'vNombre' => $request->vNombre,
                'tDescripcion' => $request->tDescripcion ?? null
            ]);

            return response()->json([
                'success' => true,
                'marca' => $marca,
                'message' => 'Marca creada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear marca: ' . $e->getMessage()
            ], 500);
        }
    }

    public function quickCreateEtiqueta(Request $request)
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

    public function quickCreateAtributo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $atributo = new Atributo();
            $atributo->vNombre = $request->vNombre;
            $atributo->vSlug = $request->vSlug ?: Str::slug($request->vNombre);
            $atributo->tDescripcion = $request->tDescripcion ?? null;
            $atributo->bActivo = true;
            $atributo->save();

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

    public function quickCreateValorAtributo(Request $request, $atributo_id)
    {
        $validator = Validator::make($request->all(), [
            'vValor' => 'required|max:100',
            'vSlug' => 'nullable|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $atributo = Atributo::findOrFail($atributo_id);
            
            $valor = new AtributoValor();
            $valor->id_atributo = $atributo_id;
            $valor->vValor = $request->vValor;
            $valor->vSlug = $request->vSlug ?: Str::slug($request->vValor);
            $valor->bActivo = true;
            
            $valor->save();

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

    public function getJsonCategorias()
    {
        $categorias = Categoria::where('bActivo', true)
            ->orderBy('vNombre')
            ->get(['id_categoria', 'vNombre', 'id_categoria_padre']);
        
        return response()->json([
            'success' => true,
            'categorias' => $categorias
        ]);
    }

    public function getJsonMarcas()
    {
        $marcas = Marca::orderBy('vNombre')
            ->get(['id_marca', 'vNombre', 'tDescripcion']);
        
        return response()->json([
            'success' => true,
            'marcas' => $marcas
        ]);
    }

    public function getJsonEtiquetas()
    {
        $etiquetas = Etiqueta::orderBy('vNombre')
            ->get(['id_etiqueta', 'vNombre', 'color', 'tDescripcion']);
        
        return response()->json([
            'success' => true,
            'etiquetas' => $etiquetas
        ]);
    }

    public function getJsonAtributos()
    {
        $atributos = Atributo::with(['valoresActivos' => function($query) {
            $query->orderBy('vValor');
        }])->where('bActivo', true)
        ->orderBy('vNombre')
        ->get();
        
        return response()->json([
            'success' => true,
            'atributos' => $atributos
        ]);
    }

    // ============ NUEVO MÉTODO PARA VERIFICACIÓN EN TIEMPO REAL ============

    /**
     * Verificar si un SKU de variación ya existe (para validación en tiempo real)
     */
    public function verificarSKU(Request $request)
    {
        try {
            $sku = $request->get('sku');
            
            if (empty($sku)) {
                return response()->json(['exists' => false]);
            }
            
            $exists = ProductoVariacion::where('vSKU', $sku)->exists();
            
            return response()->json([
                'exists' => $exists,
                'sku' => $sku
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al verificar SKU de variación: ' . $e->getMessage());
            return response()->json([
                'exists' => false,
                'error' => 'Error al verificar'
            ], 500);
        }
    }
}