<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoVariacion;
use App\Models\VariacionAtributo;
use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ValoracionController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['marca', 'categoria', 'variaciones.atributos.valor'])
            ->whereHas('valoresAtributos')
            ->orderBy('vNombre')
            ->get();
            
        return view('valoraciones.index', compact('productos'));
    }

    public function show($id)
    {
        $producto = Producto::with(['variaciones.atributos.valor', 'variaciones.atributos.atributo', 'marca', 'categoria'])
            ->findOrFail($id);
            
        return view('valoraciones.show', compact('producto'));
    }

    public function create($producto_id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($producto_id);
        
        if ($producto->valoresAtributos->count() === 0) {
            return redirect()->route('valoraciones.show', $producto->id_producto)
                ->with('warning', 'Primero debes asignar atributos al producto desde la página de edición.');
        }
        
        $atributos = [];
        foreach ($producto->valoresAtributos as $valor) {
            $nombreAtributo = $valor->atributo->vNombre;
            if (!isset($atributos[$nombreAtributo])) {
                $atributos[$nombreAtributo] = [];
            }
            $atributos[$nombreAtributo][] = $valor;
        }
        
        return view('valoraciones.create', compact('producto', 'atributos'));
    }

    public function store(Request $request, $producto_id)
    {
        // Validar que el producto exista
        $productoPadre = Producto::findOrFail($producto_id);
        
        // Validación de campos
        $validator = \Validator::make($request->all(), [
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
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                            $fail('El precio de oferta debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                        
                        if ($request->input('bTiene_oferta') == '1' && $value >= $request->dPrecio) {
                            $fail('El precio de oferta debe ser menor que el precio normal.');
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
            // Campos para oferta especial
            'bTiene_oferta' => 'nullable|in:0,1',
            'dFecha_inicio_oferta' => 'nullable|date',
            'dFecha_fin_oferta' => 'nullable|date|after_or_equal:dFecha_inicio_oferta',
            'vMotivo_oferta' => 'nullable|string|max:255',
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
            'bTiene_oferta.in' => 'El valor de oferta debe ser 0 o 1',
            'dFecha_inicio_oferta.date' => 'La fecha de inicio debe ser una fecha válida',
            'dFecha_fin_oferta.date' => 'La fecha de fin debe ser una fecha válida',
            'dFecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'vMotivo_oferta.max' => 'El motivo de la oferta no puede exceder los 255 caracteres',
        ]);

        // Validación adicional: si bTiene_oferta es 1, entonces dPrecio_oferta es requerido
        $validator->sometimes('dPrecio_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        // Validación adicional: si bTiene_oferta es 1, entonces fechas son requeridas
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

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            // Log para depuración
            \Log::info('Datos del formulario recibidos:', [
                'bTiene_oferta' => $request->has('bTiene_oferta'),
                'bTiene_oferta_value' => $request->bTiene_oferta,
                'dPrecio_oferta' => $request->dPrecio_oferta,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta,
                'vMotivo_oferta' => $request->vMotivo_oferta,
            ]);

            // Determinar clase de envío
            $claseEnvio = $request->vClase_envio;
            if (empty($claseEnvio) && $productoPadre->vClase_envio) {
                $claseEnvio = $productoPadre->vClase_envio;
            } elseif (empty($claseEnvio)) {
                $claseEnvio = 'Estándar';
            }

            // Preparar datos para la variación
            $variacionData = [
                'id_producto' => $producto_id,
                'vSKU' => $request->vSKU,
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta ?: null,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $claseEnvio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0,
                // Campos de oferta - CORREGIDO
                'bTiene_oferta' => $request->has('bTiene_oferta') && $request->bTiene_oferta == '1' ? 1 : 0,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta ?: null,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta ?: null,
                'vMotivo_oferta' => $request->vMotivo_oferta ?: null,
            ];

            \Log::info('Datos que se van a guardar en la variación:', $variacionData);

            $variacion = ProductoVariacion::create($variacionData);

            // Log para verificar creación
            \Log::info('Variación creada con ID:', ['id' => $variacion->id_variacion]);
            \Log::info('Campos de oferta guardados:', [
                'bTiene_oferta' => $variacion->bTiene_oferta,
                'dPrecio_oferta' => $variacion->dPrecio_oferta,
                'dFecha_inicio_oferta' => $variacion->dFecha_inicio_oferta,
                'dFecha_fin_oferta' => $variacion->dFecha_fin_oferta,
                'vMotivo_oferta' => $variacion->vMotivo_oferta,
            ]);

            // Guardar imagen si existe
            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            }

            // Guardar atributos
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

            return redirect()->route('valoraciones.show', $producto_id)
                ->with('success', 'Valoración creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al crear valoración: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear valoración: ' . $e->getMessage()]);
        }
    }

    public function edit($producto_id, $variacion_id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($producto_id);
        $variacion = ProductoVariacion::with('atributos.valor', 'atributos.atributo')
            ->findOrFail($variacion_id);
        
        if ($variacion->id_producto != $producto_id) {
            return redirect()->route('valoraciones.index')
                ->with('error', 'La variación no pertenece a este producto');
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
        
        return view('valoraciones.edit', compact('producto', 'variacion', 'atributos', 'valoresSeleccionados'));
    }

    public function update(Request $request, $producto_id, $variacion_id)
    {
        // Validar que el producto y la variación existan
        $productoPadre = Producto::findOrFail($producto_id);
        $variacion = ProductoVariacion::findOrFail($variacion_id);
        
        // Verificar que la variación pertenece al producto
        if ($variacion->id_producto != $producto_id) {
            return redirect()->route('valoraciones.index')
                ->with('error', 'La variación no pertenece a este producto');
        }
        
        // Validación de campos
        $validator = \Validator::make($request->all(), [
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
            'dPrecio_oferta' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                            $fail('El precio de oferta debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                        
                        if ($request->input('bTiene_oferta') == '1' && $value >= $request->dPrecio) {
                            $fail('El precio de oferta debe ser menor que el precio normal.');
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
            // Campos para oferta especial
            'bTiene_oferta' => 'nullable|in:0,1',
            'dFecha_inicio_oferta' => 'nullable|date',
            'dFecha_fin_oferta' => 'nullable|date|after_or_equal:dFecha_inicio_oferta',
            'vMotivo_oferta' => 'nullable|string|max:255',
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
            'bTiene_oferta.in' => 'El valor de oferta debe ser 0 o 1',
            'dFecha_inicio_oferta.date' => 'La fecha de inicio debe ser una fecha válida',
            'dFecha_fin_oferta.date' => 'La fecha de fin debe ser una fecha válida',
            'dFecha_fin_oferta.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'vMotivo_oferta.max' => 'El motivo de la oferta no puede exceder los 255 caracteres',
        ]);

        // Validación adicional: si bTiene_oferta es 1, entonces dPrecio_oferta es requerido
        $validator->sometimes('dPrecio_oferta', 'required', function ($input) {
            return $input->bTiene_oferta == 1;
        });

        // Validación adicional: si bTiene_oferta es 1, entonces fechas son requeridas
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

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            // Log para depuración
            \Log::info('Datos del formulario de actualización:', [
                'bTiene_oferta' => $request->has('bTiene_oferta'),
                'bTiene_oferta_value' => $request->bTiene_oferta,
                'dPrecio_oferta' => $request->dPrecio_oferta,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta,
                'vMotivo_oferta' => $request->vMotivo_oferta,
            ]);

            // Determinar clase de envío
            $claseEnvio = $request->vClase_envio;
            if (empty($claseEnvio) && $productoPadre->vClase_envio) {
                $claseEnvio = $productoPadre->vClase_envio;
            } elseif (empty($claseEnvio)) {
                $claseEnvio = $variacion->vClase_envio ?: 'Estándar';
            }

            // Preparar datos para actualización
            $updateData = [
                'vSKU' => $request->vSKU,
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta ?: null,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso ?: null,
                'dLargo_cm' => $request->dLargo_cm ?: null,
                'dAncho_cm' => $request->dAncho_cm ?: null,
                'dAlto_cm' => $request->dAlto_cm ?: null,
                'vClase_envio' => $claseEnvio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo') ? 1 : 0,
                // Campos de oferta - CORREGIDO
                'bTiene_oferta' => $request->has('bTiene_oferta') && $request->bTiene_oferta == '1' ? 1 : 0,
                'dFecha_inicio_oferta' => $request->dFecha_inicio_oferta ?: null,
                'dFecha_fin_oferta' => $request->dFecha_fin_oferta ?: null,
                'vMotivo_oferta' => $request->vMotivo_oferta ?: null,
            ];

            \Log::info('Datos de actualización que se van a guardar:', $updateData);

            // Manejo de imagen
            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $this->eliminarImagenVariacion($variacion);
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            } elseif ($request->has('eliminar_imagen') && $request->eliminar_imagen == '1') {
                $this->eliminarImagenVariacion($variacion);
            }

            // Actualizar la variación
            $variacion->update($updateData);

            // Log para verificar actualización
            \Log::info('Variación actualizada con ID:', ['id' => $variacion->id_variacion]);
            \Log::info('Campos de oferta actualizados:', [
                'bTiene_oferta' => $variacion->bTiene_oferta,
                'dPrecio_oferta' => $variacion->dPrecio_oferta,
                'dFecha_inicio_oferta' => $variacion->dFecha_inicio_oferta,
                'dFecha_fin_oferta' => $variacion->dFecha_fin_oferta,
                'vMotivo_oferta' => $variacion->vMotivo_oferta,
            ]);

            // Actualizar atributos
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

            return redirect()->route('valoraciones.show', $producto_id)
                ->with('success', 'Valoración actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al actualizar valoración: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar valoración: ' . $e->getMessage()]);
        }
    }

    public function destroy($producto_id, $variacion_id)
    {
        try {
            DB::beginTransaction();

            $variacion = ProductoVariacion::findOrFail($variacion_id);
            
            if ($variacion->id_producto != $producto_id) {
                return redirect()->route('valoraciones.index')
                    ->with('error', 'La variación no pertenece a este producto');
            }
            
            // Eliminar imagen si existe
            $this->eliminarImagenVariacion($variacion);
            
            // Eliminar atributos asociados
            $variacion->atributos()->delete();
            
            // Eliminar la variación
            $variacion->delete();

            DB::commit();

            return redirect()->route('valoraciones.show', $producto_id)
                ->with('success', 'Valoración eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al eliminar valoración: ' . $e->getMessage());
            
            return redirect()->route('valoraciones.show', $producto_id)
                ->with('error', 'Error al eliminar valoración: ' . $e->getMessage());
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
}