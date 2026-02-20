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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        $atributosGlobales = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('iOrden');
        }])->where('bActivo', true)->get();
        
        return view('variaciones.create', compact(
            'producto', 
            'atributos', 
            'categorias', 
            'marcas', 
            'etiquetas', 
            'atributosGlobales'
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
            'dPrecio_descuento' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El precio de descuento debe ser un número válido.');
                        } elseif (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', (string)$value)) {
                            $fail('El precio de descuento debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                        
                        if ($request->input('bTiene_descuento') == '1' && $value >= $request->dPrecio) {
                            $fail('El precio de descuento debe ser menor que el precio normal.');
                        }
                    }
                }
            ],
            'iStock' => 'required|integer|min:0|max:999999',
            'dPeso' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,4}(\.\d{1,3})?$/', $value)) {
                        $fail('El peso debe tener máximo 4 dígitos enteros y 3 decimales.');
                    }
                }
            ],
            'dLargo_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:500',
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
                'max:500',
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
                'max:500',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El alto debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,gif,webp,bmp,svg',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor',
            'bTiene_descuento' => 'nullable|in:0,1',
            'dFecha_inicio_descuento' => 'nullable|date',
            'dFecha_fin_descuento' => 'nullable|date|after_or_equal:dFecha_inicio_descuento',
            'vMotivo_descuento' => 'nullable|string|max:255',
        ], [
            'vSKU.required' => 'El SKU es obligatorio',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'vSKU.max' => 'El SKU no puede exceder los 50 caracteres',
            'dPrecio.required' => 'El precio es obligatorio',
            'dPrecio.numeric' => 'El precio debe ser un número válido',
            'dPrecio.min' => 'El precio no puede ser negativo',
            'dPrecio.max' => 'El precio no puede exceder $9,999,999.99',
            'dPrecio_descuento.numeric' => 'El precio de descuento debe ser un número válido',
            'dPrecio_descuento.min' => 'El precio de descuento no puede ser negativo',
            'dPrecio_descuento.max' => 'El precio de descuento no puede exceder $9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'iStock.max' => 'El stock no puede exceder 999,999 unidades',
            'dPeso.numeric' => 'El peso debe ser un número válido',
            'dPeso.min' => 'El peso no puede ser negativo',
            'dPeso.max' => 'El peso no puede exceder 1000 kg',
            'dLargo_cm.numeric' => 'El largo debe ser un número válido',
            'dLargo_cm.min' => 'El largo no puede ser negativo',
            'dLargo_cm.max' => 'El largo no puede exceder 500 cm',
            'dAncho_cm.numeric' => 'El ancho debe ser un número válido',
            'dAncho_cm.min' => 'El ancho no puede ser negativo',
            'dAncho_cm.max' => 'El ancho no puede exceder 500 cm',
            'dAlto_cm.numeric' => 'El alto debe ser un número válido',
            'dAlto_cm.min' => 'El alto no puede ser negativo',
            'dAlto_cm.max' => 'El alto no puede exceder 500 cm',
            'vClase_envio.max' => 'La clase de envío no puede exceder los 50 caracteres',
            'imagen.image' => 'El archivo debe ser una imagen válida',
            'imagen.max' => 'La imagen no debe pesar más de 5MB',
            'imagen.mimes' => 'Formatos aceptados: JPG, JPEG, PNG, GIF, WebP, BMP, SVG',
            'atributos.required' => 'Debes seleccionar valores para todos los atributos',
            'atributos.*.required' => 'Debes seleccionar un valor para cada atributo',
            'atributos.*.exists' => 'El valor seleccionado no es válido',
            'bTiene_descuento.in' => 'El valor de descuento debe ser 0 o 1',
            'dFecha_inicio_descuento.date' => 'La fecha de inicio debe ser una fecha válida',
            'dFecha_fin_descuento.date' => 'La fecha de fin debe ser una fecha válida',
            'dFecha_fin_descuento.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'vMotivo_descuento.max' => 'El motivo del descuento no puede exceder los 255 caracteres',
        ]);

        $validator->sometimes('dPrecio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_inicio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_fin_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', true);
        }

        try {
            DB::beginTransaction();

            \Log::info('Datos del formulario recibidos en VariacionController:', [
                'bTiene_descuento' => $request->has('bTiene_descuento'),
                'bTiene_descuento_value' => $request->bTiene_descuento,
                'dPrecio_descuento' => $request->dPrecio_descuento,
                'dFecha_inicio_descuento' => $request->dFecha_inicio_descuento,
                'dFecha_fin_descuento' => $request->dFecha_fin_descuento,
                'vMotivo_descuento' => $request->vMotivo_descuento,
            ]);

            $claseEnvio = $request->vClase_envio;
            if (empty($claseEnvio) && $productoPadre->vClase_envio) {
                $claseEnvio = $productoPadre->vClase_envio;
            } elseif (empty($claseEnvio)) {
                $claseEnvio = 'estandar';
            }

            $variacionData = [
                'id_producto' => $producto_id,
                'vSKU' => strtoupper($request->vSKU),
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_descuento ?: null,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $claseEnvio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0,
                'bTiene_oferta' => $request->has('bTiene_descuento') && $request->bTiene_descuento == '1' ? 1 : 0,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_descuento ?: null,
                'dFecha_fin_oferta' => $request->dFecha_fin_descuento ?: null,
                'vMotivo_oferta' => $request->vMotivo_descuento ?: null,
            ];

            \Log::info('Datos que se van a guardar en la variación:', $variacionData);

            $variacion = ProductoVariacion::create($variacionData);

            \Log::info('Variación creada con ID:', ['id' => $variacion->id_variacion]);
            \Log::info('Campos de oferta guardados:', [
                'bTiene_oferta' => $variacion->bTiene_oferta,
                'dPrecio_oferta' => $variacion->dPrecio_oferta,
                'dFecha_inicio_oferta' => $variacion->dFecha_inicio_oferta,
                'dFecha_fin_oferta' => $variacion->dFecha_fin_oferta,
                'vMotivo_oferta' => $variacion->vMotivo_oferta,
            ]);

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            }

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
        $atributosGlobales = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true)->orderBy('iOrden');
        }])->where('bActivo', true)->get();
        
        return view('variaciones.edit', compact(
            'producto', 
            'variacion', 
            'atributos', 
            'valoresSeleccionados',
            'categorias',
            'marcas',
            'etiquetas',
            'atributosGlobales'
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
            'vSKU' => 'required|unique:tbl_producto_variaciones,vSKU,' . $variacion_id . ',id_variacion|max:50',
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
            'dPrecio_descuento' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El precio de descuento debe ser un número válido.');
                        } elseif (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', (string)$value)) {
                            $fail('El precio de descuento debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                        
                        if ($request->input('bTiene_descuento') == '1' && $value >= $request->dPrecio) {
                            $fail('El precio de descuento debe ser menor que el precio normal.');
                        }
                    }
                }
            ],
            'iStock' => 'required|integer|min:0|max:999999',
            'dPeso' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,4}(\.\d{1,3})?$/', $value)) {
                        $fail('El peso debe tener máximo 4 dígitos enteros y 3 decimales.');
                    }
                }
            ],
            'dLargo_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:500',
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
                'max:500',
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
                'max:500',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,3}(\.\d{1,2})?$/', $value)) {
                        $fail('El alto debe tener máximo 3 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,gif,webp,bmp,svg',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor',
            'bTiene_descuento' => 'nullable|in:0,1',
            'dFecha_inicio_descuento' => 'nullable|date',
            'dFecha_fin_descuento' => 'nullable|date|after_or_equal:dFecha_inicio_descuento',
            'vMotivo_descuento' => 'nullable|string|max:255',
        ], [
            'vSKU.required' => 'El SKU es obligatorio',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'vSKU.max' => 'El SKU no puede exceder los 50 caracteres',
            'dPrecio.required' => 'El precio es obligatorio',
            'dPrecio.numeric' => 'El precio debe ser un número válido',
            'dPrecio.min' => 'El precio no puede ser negativo',
            'dPrecio.max' => 'El precio no puede exceder $9,999,999.99',
            'dPrecio_descuento.numeric' => 'El precio de descuento debe ser un número válido',
            'dPrecio_descuento.min' => 'El precio de descuento no puede ser negativo',
            'dPrecio_descuento.max' => 'El precio de descuento no puede exceder $9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'iStock.max' => 'El stock no puede exceder 999,999 unidades',
            'dPeso.numeric' => 'El peso debe ser un número válido',
            'dPeso.min' => 'El peso no puede ser negativo',
            'dPeso.max' => 'El peso no puede exceder 1000 kg',
            'dLargo_cm.numeric' => 'El largo debe ser un número válido',
            'dLargo_cm.min' => 'El largo no puede ser negativo',
            'dLargo_cm.max' => 'El largo no puede exceder 500 cm',
            'dAncho_cm.numeric' => 'El ancho debe ser un número válido',
            'dAncho_cm.min' => 'El ancho no puede ser negativo',
            'dAncho_cm.max' => 'El ancho no puede exceder 500 cm',
            'dAlto_cm.numeric' => 'El alto debe ser un número válido',
            'dAlto_cm.min' => 'El alto no puede ser negativo',
            'dAlto_cm.max' => 'El alto no puede exceder 500 cm',
            'vClase_envio.max' => 'La clase de envío no puede exceder los 50 caracteres',
            'imagen.image' => 'El archivo debe ser una imagen válida',
            'imagen.max' => 'La imagen no debe pesar más de 5MB',
            'imagen.mimes' => 'Formatos aceptados: JPG, JPEG, PNG, GIF, WebP, BMP, SVG',
            'atributos.required' => 'Debes seleccionar valores para todos los atributos',
            'atributos.*.required' => 'Debes seleccionar un valor para cada atributo',
            'atributos.*.exists' => 'El valor seleccionado no es válido',
            'bTiene_descuento.in' => 'El valor de descuento debe ser 0 o 1',
            'dFecha_inicio_descuento.date' => 'La fecha de inicio debe ser una fecha válida',
            'dFecha_fin_descuento.date' => 'La fecha de fin debe ser una fecha válida',
            'dFecha_fin_descuento.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'vMotivo_descuento.max' => 'El motivo del descuento no puede exceder los 255 caracteres',
        ]);

        $validator->sometimes('dPrecio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_inicio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_fin_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', true);
        }

        try {
            DB::beginTransaction();

            \Log::info('Datos del formulario de actualización en VariacionController:', [
                'bTiene_descuento' => $request->has('bTiene_descuento'),
                'bTiene_descuento_value' => $request->bTiene_descuento,
                'dPrecio_descuento' => $request->dPrecio_descuento,
                'dFecha_inicio_descuento' => $request->dFecha_inicio_descuento,
                'dFecha_fin_descuento' => $request->dFecha_fin_descuento,
                'vMotivo_descuento' => $request->vMotivo_descuento,
            ]);

            $claseEnvio = $request->vClase_envio;
            if (empty($claseEnvio) && $productoPadre->vClase_envio) {
                $claseEnvio = $productoPadre->vClase_envio;
            } elseif (empty($claseEnvio)) {
                $claseEnvio = $variacion->vClase_envio ?: 'estandar';
            }

            $updateData = [
                'vSKU' => strtoupper($request->vSKU),
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_descuento ?: null,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $claseEnvio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0,
                'bTiene_oferta' => $request->has('bTiene_descuento') && $request->bTiene_descuento == '1' ? 1 : 0,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_descuento ?: null,
                'dFecha_fin_oferta' => $request->dFecha_fin_descuento ?: null,
                'vMotivo_oferta' => $request->vMotivo_descuento ?: null,
            ];

            \Log::info('Datos de actualización que se van a guardar:', $updateData);

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $this->eliminarImagenVariacion($variacion);
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            } elseif ($request->has('eliminar_imagen') && $request->eliminar_imagen == '1') {
                $this->eliminarImagenVariacion($variacion);
            }

            $variacion->update($updateData);

            \Log::info('Variación actualizada con ID:', ['id' => $variacion->id_variacion]);
            \Log::info('Campos de oferta actualizados:', [
                'bTiene_oferta' => $variacion->bTiene_oferta,
                'dPrecio_oferta' => $variacion->dPrecio_oferta,
                'dFecha_inicio_oferta' => $variacion->dFecha_inicio_oferta,
                'dFecha_fin_oferta' => $variacion->dFecha_fin_oferta,
                'vMotivo_oferta' => $variacion->vMotivo_oferta,
            ]);

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

            return redirect()->route('variaciones.show', $producto_id)
                ->with('success', 'Variación actualizada exitosamente')
                ->with('swal_save', true);

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
            
            $this->eliminarImagenVariacion($variacion);
            $variacion->atributos()->delete();
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

    private function guardarImagenVariacion($variacion, $imagen)
    {
        $carpeta = 'variaciones/' . $variacion->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $extension = strtolower($imagen->getClientOriginalExtension());
        
        $extensionesValidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'tiff', 'ico', 'heic', 'heif'];
        
        if (!$extension || !in_array($extension, $extensionesValidas)) {
            $extension = 'jpg';
        }
        
        $nombreArchivo = 'imagen_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        $variacion->vImagen = Storage::url($ruta);
        $variacion->save();
    }

    private function eliminarImagenVariacion($variacion)
    {
        if ($variacion->vImagen) {
            $urlBase = Storage::url('');
            $rutaRelativa = str_replace($urlBase, '', $variacion->vImagen);
            
            if (Storage::disk('public')->exists($rutaRelativa)) {
                Storage::disk('public')->delete($rutaRelativa);
            }
            
            $carpeta = 'variaciones/' . $variacion->id_variacion;
            if (Storage::disk('public')->exists($carpeta)) {
                $archivos = Storage::disk('public')->files($carpeta);
                if (empty($archivos)) {
                    Storage::disk('public')->deleteDirectory($carpeta);
                }
            }
            
            $variacion->vImagen = null;
            $variacion->save();
        }
    }

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
            
            $ultimoOrden = AtributoValor::where('id_atributo', $atributo_id)->max('iOrden');
            $valor->iOrden = $ultimoOrden ? $ultimoOrden + 1 : 0;
            
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
            $query->orderBy('iOrden')->orderBy('vValor');
        }])->where('bActivo', true)
        ->orderBy('vNombre')
        ->get();
        
        return response()->json([
            'success' => true,
            'atributos' => $atributos
        ]);
    }
}