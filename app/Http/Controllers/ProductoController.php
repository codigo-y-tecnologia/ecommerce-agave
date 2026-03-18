<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Impuesto;
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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::with(['marca', 'categoria', 'etiquetas', 'impuestos']);

        // Agregar búsqueda por nombre o SKU
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('vNombre', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('vCodigo_barras', 'LIKE', "%{$searchTerm}%");
            });
        }

        $productos = $query->orderBy('id_producto', 'desc')->get();

        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::with(['hijos' => function ($query) {
            $query->where('bActivo', true)
                ->with(['hijos' => function ($subQuery) {
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

        // CORREGIDO: Cargar atributos con sus valores activos - SIN orderBy('iOrden')
        $atributos = Atributo::with(['valoresActivos' => function ($query) {
            $query->where('bActivo', true);
        }])->where('bActivo', true)->get();

        // Obtener impuestos activos para el select
        $impuestos = Impuesto::where('bActivo', true)->orderBy('vNombre')->get();

        // Obtener etiquetas especiales
        $etiquetasEspeciales = Etiqueta::whereIn('vNombre', ['nuevo', 'popular', 'descuento', 'destacado'])->get();

        return view('productos.create', compact('categorias', 'marcas', 'etiquetas', 'atributos', 'impuestos', 'etiquetasEspeciales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar el tamaño total de la solicitud primero
        $contentLength = $request->server('CONTENT_LENGTH');
        $maxSize = 52 * 1024 * 1024; // 52MB en bytes

        if ($contentLength > $maxSize) {
            Log::warning('Intento de subida de archivos demasiado grande: ' . $contentLength . ' bytes');
            return redirect()->back()
                ->withInput()
                ->with('post_max_size_error', true)
                ->with('error', 'El tamaño total de los archivos (' . round($contentLength / (1024 * 1024), 2) . 'MB) excede el límite permitido de 50MB. Por favor, reduce el tamaño de los archivos o súbelos en menos cantidad.')
                ->with('swal_error', true);
        }

        // Validación personalizada para precio de descuento
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
            'dPrecio_compra' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio de compra debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dPrecio_venta' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio de venta debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'iStock' => 'required|integer|min:0|max:999999',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'id_impuesto' => 'nullable|exists:tbl_impuestos,id_impuesto',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagen_principal' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'gif_producto' => 'nullable|mimes:gif|max:10240',
            'imagenes' => 'nullable|array|max:7',
            'imagenes.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'atributos' => 'nullable|array',

            // VALIDACIONES PARA DIMENSIONES Y PESO
            'dPeso' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.999',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El peso debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El peso no puede ser negativo.');
                        } elseif ($value > 999.999) {
                            $fail('El peso no puede ser mayor a 999.999 kg.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 3) {
                                $fail('El peso debe tener máximo 3 decimales.');
                            }
                        }
                    }
                }
            ],
            'dLargo_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El largo debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El largo no puede ser negativo.');
                        } elseif ($value > 999.99) {
                            $fail('El largo no puede ser mayor a 999.99 cm.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 2) {
                                $fail('El largo debe tener máximo 2 decimales.');
                            }
                        }
                    }
                }
            ],
            'dAncho_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El ancho debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El ancho no puede ser negativo.');
                        } elseif ($value > 999.99) {
                            $fail('El ancho no puede ser mayor a 999.99 cm.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 2) {
                                $fail('El ancho debe tener máximo 2 decimales.');
                            }
                        }
                    }
                }
            ],
            'dAlto_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El alto debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El alto no puede ser negativo.');
                        } elseif ($value > 999.99) {
                            $fail('El alto no puede ser mayor a 999.99 cm.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 2) {
                                $fail('El alto debe tener máximo 2 decimales.');
                            }
                        }
                    }
                }
            ],

            'vClase_envio' => 'nullable|in:estandar,express,fragil,grandes_dimensiones',
            'etiquetas_especiales' => 'nullable|array',
            'etiquetas_especiales.*' => 'in:nuevo,popular,descuento,destacado',

            // CAMPOS DE DESCUENTO
            'bTiene_descuento' => 'nullable|in:0,1',
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

                        // Validar que el precio de descuento sea menor al precio de venta
                        if ($request->input('bTiene_descuento') == '1' && $value >= $request->dPrecio_venta) {
                            $fail('El precio de descuento debe ser menor que el precio de venta.');
                        }
                    }
                }
            ],
            'dFecha_inicio_descuento' => 'nullable|date',
            'dFecha_fin_descuento' => 'nullable|date|after_or_equal:dFecha_inicio_descuento',
            'vMotivo_descuento' => 'nullable|string|max:255',
            'variaciones' => 'nullable|array',
        ], [
            'vCodigo_barras.required' => 'El SKU es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este SKU',
            'vCodigo_barras.regex' => 'El SKU solo puede contener letras y números',
            'vCodigo_barras.max' => 'El SKU no puede exceder los 15 caracteres',
            'vNombre.required' => 'El nombre del producto es obligatorio',
            'vNombre.unique' => 'Ya existe un producto con este nombre',
            'vNombre.max' => 'El nombre no puede exceder los 100 caracteres',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.numeric' => 'El precio de venta debe ser un número válido',
            'dPrecio_venta.min' => 'El precio de venta no puede ser negativo',
            'dPrecio_venta.max' => 'El precio de venta máximo es 9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.integer' => 'El stock debe ser un número entero',
            'iStock.min' => 'El stock no puede ser negativo',
            'iStock.max' => 'El stock no puede ser mayor a 999,999 unidades',
            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria',
            'id_impuesto.exists' => 'El impuesto seleccionado no existe',
            'imagen_principal.required' => 'La imagen principal es obligatoria',
            'imagen_principal.image' => 'El archivo debe ser una imagen',
            'imagen_principal.mimes' => 'La imagen principal debe ser JPG, JPEG o PNG',
            'imagen_principal.max' => 'La imagen principal no debe superar los 5MB',
            'gif_producto.mimes' => 'El archivo debe ser un GIF',
            'gif_producto.max' => 'El GIF no debe superar los 10MB',
            'imagenes.max' => 'No puedes subir más de 7 imágenes adicionales',
            'imagenes.*.image' => 'Solo se permiten archivos de imagen',
            'imagenes.*.mimes' => 'Formatos permitidos: JPG, JPEG, PNG, WEBP',
            'imagenes.*.max' => 'Cada imagen no debe superar los 5MB',

            // Mensajes personalizados para dimensiones
            'dPeso.numeric' => 'El peso debe ser un número válido',
            'dPeso.min' => 'El peso no puede ser negativo',
            'dPeso.max' => 'El peso no puede ser mayor a 999.999 kg',
            'dLargo_cm.numeric' => 'El largo debe ser un número válido',
            'dLargo_cm.min' => 'El largo no puede ser negativo',
            'dLargo_cm.max' => 'El largo no puede ser mayor a 999.99 cm',
            'dAncho_cm.numeric' => 'El ancho debe ser un número válido',
            'dAncho_cm.min' => 'El ancho no puede ser negativo',
            'dAncho_cm.max' => 'El ancho no puede ser mayor a 999.99 cm',
            'dAlto_cm.numeric' => 'El alto debe ser un número válido',
            'dAlto_cm.min' => 'El alto no puede ser negativo',
            'dAlto_cm.max' => 'El alto no puede ser mayor a 999.99 cm',

            'vClase_envio.in' => 'La clase de envío seleccionada no es válida',
            'bTiene_descuento.in' => 'El valor de descuento debe ser 0 o 1',
            'dPrecio_descuento.numeric' => 'El precio de descuento debe ser un número válido',
            'dPrecio_descuento.min' => 'El precio de descuento no puede ser negativo',
            'dPrecio_descuento.max' => 'El precio de descuento máximo es 9,999,999.99',
            'dFecha_inicio_descuento.date' => 'La fecha de inicio de descuento debe ser una fecha válida',
            'dFecha_fin_descuento.date' => 'La fecha de fin de descuento debe ser una fecha válida',
            'dFecha_fin_descuento.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'vMotivo_descuento.max' => 'El motivo del descuento no puede exceder los 255 caracteres',
        ]);

        // Validaciones condicionales para descuento
        $validator->sometimes('dPrecio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_inicio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_fin_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        // Validación de variaciones
        if ($request->has('variaciones')) {
            $validator->after(function ($validator) use ($request) {
                foreach ($request->variaciones as $key => $variacion) {
                    $sku = $variacion['vSKU'] ?? '';
                    if (!empty($sku) && ProductoVariacion::where('vSKU', $sku)->exists()) {
                        $validator->errors()->add("variaciones.{$key}.vSKU", "El SKU '{$sku}' ya está registrado para otra variación.");
                    }

                    if (isset($variacion['dPrecio']) && !empty($variacion['dPrecio'])) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $variacion['dPrecio'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio", 'El precio debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                    }

                    // Validación para descuento en variaciones
                    if (isset($variacion['dPrecio_descuento']) && !empty($variacion['dPrecio_descuento'])) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $variacion['dPrecio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }

                        if (($variacion['bTiene_descuento'] ?? 0) == 1 && isset($variacion['dPrecio']) && $variacion['dPrecio_descuento'] >= $variacion['dPrecio']) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento debe ser menor que el precio normal.');
                        }
                    }

                    // Validación de campos requeridos cuando hay descuento
                    if (isset($variacion['bTiene_descuento']) && $variacion['bTiene_descuento'] == 1) {
                        if (empty($variacion['dPrecio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento es obligatorio cuando el descuento está activo.');
                        }
                        if (empty($variacion['dFecha_inicio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dFecha_inicio_descuento", 'La fecha de inicio es obligatoria cuando el descuento está activo.');
                        }
                        if (empty($variacion['dFecha_fin_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dFecha_fin_descuento", 'La fecha de fin es obligatoria cuando el descuento está activo.');
                        }

                        // Validar que la fecha de fin sea igual o posterior a la fecha de inicio
                        if (!empty($variacion['dFecha_inicio_descuento']) && !empty($variacion['dFecha_fin_descuento'])) {
                            if ($variacion['dFecha_fin_descuento'] < $variacion['dFecha_inicio_descuento']) {
                                $validator->errors()->add("variaciones.{$key}.dFecha_fin_descuento", 'La fecha de fin debe ser igual o posterior a la fecha de inicio.');
                            }
                        }
                    }
                }
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', true);
        }

        try {
            DB::beginTransaction();

            Log::info('Datos del formulario de producto:', [
                'bTiene_descuento' => $request->has('bTiene_descuento'),
                'bTiene_descuento_value' => $request->bTiene_descuento,
                'dPrecio_descuento' => $request->dPrecio_descuento,
                'dFecha_inicio_descuento' => $request->dFecha_inicio_descuento,
                'dFecha_fin_descuento' => $request->dFecha_fin_descuento,
                'vMotivo_descuento' => $request->vMotivo_descuento,
                'id_impuesto' => $request->id_impuesto,
                'dPeso' => $request->dPeso,
                'dLargo_cm' => $request->dLargo_cm,
                'dAncho_cm' => $request->dAncho_cm,
                'dAlto_cm' => $request->dAlto_cm,
            ]);

            // MAPEO CORRECTO: Del formulario (descuento) a la base de datos (descuento)
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

                // Dimensiones
                'dPeso' => ($request->dPeso !== null && $request->dPeso !== '') ? floatval($request->dPeso) : null,
                'dLargo_cm' => ($request->dLargo_cm !== null && $request->dLargo_cm !== '') ? floatval($request->dLargo_cm) : null,
                'dAncho_cm' => ($request->dAncho_cm !== null && $request->dAncho_cm !== '') ? floatval($request->dAncho_cm) : null,
                'dAlto_cm' => ($request->dAlto_cm !== null && $request->dAlto_cm !== '') ? floatval($request->dAlto_cm) : null,

                'vClase_envio' => $request->vClase_envio ?: null,

                // MAPEO: Los campos de descuento del formulario se guardan como descuento en la BD
                'bTiene_descuento' => $request->has('bTiene_descuento') && $request->bTiene_descuento == '1' ? true : false,
                'dPrecio_descuento' => $request->dPrecio_descuento ?: null,
                'dFecha_inicio_descuento' => $request->dFecha_inicio_descuento ?: null,
                'dFecha_fin_descuento' => $request->dFecha_fin_descuento ?: null,
                'vMotivo_descuento' => $request->vMotivo_descuento ?: null,
            ];

            $producto = Producto::create($productoData);

            // Sincronizar impuesto
            if ($request->has('id_impuesto') && !empty($request->id_impuesto)) {
                $producto->impuestos()->sync([$request->id_impuesto]);
                $producto->recalcularPrecioFinal();
            }

            Log::info('Producto creado con ID:', ['id' => $producto->id_producto]);
            Log::info('Dimensiones guardadas:', [
                'dPeso' => $producto->dPeso,
                'dLargo_cm' => $producto->dLargo_cm,
                'dAncho_cm' => $producto->dAncho_cm,
                'dAlto_cm' => $producto->dAlto_cm,
            ]);

            // Guardar imagen principal (ACTUALIZADO PARA GUARDAR EN BD)
            if ($request->hasFile('imagen_principal')) {
                $producto->guardarImagenPrincipal($request->file('imagen_principal'));
            }

            // Guardar GIF si existe (ACTUALIZADO PARA GUARDAR EN BD)
            if ($request->hasFile('gif_producto')) {
                $producto->guardarGif($request->file('gif_producto'));
            }

            // Guardar imágenes adicionales (ACTUALIZADO PARA GUARDAR EN BD)
            if ($request->hasFile('imagenes')) {
                $producto->guardarImagenesAdicionales($request->file('imagenes'));
            }

            // Sincronizar etiquetas
            if ($request->has('etiquetas')) {
                $producto->etiquetas()->sync($request->etiquetas);
            }

            // Guardar atributos del producto
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

            // Guardar variaciones (ACTUALIZADO PARA GUARDAR IMÁGENES EN BD)
            if ($request->has('variaciones')) {
                foreach ($request->variaciones as $key => $variacionData) {
                    if (!isset($variacionData['vSKU']) || empty($variacionData['vSKU'])) {
                        continue;
                    }

                    $claseEnvio = $variacionData['vClase_envio'] ?? null;
                    if (empty($claseEnvio) && $producto->vClase_envio) {
                        $claseEnvio = $producto->vClase_envio;
                    } elseif (empty($claseEnvio)) {
                        $claseEnvio = 'estandar';
                    }

                    $variacion = ProductoVariacion::create([
                        'id_producto' => $producto->id_producto,
                        'vSKU' => strtoupper($variacionData['vSKU']),
                        'dPrecio' => $variacionData['dPrecio'] ?? $producto->dPrecio_venta,

                        // MAPEO: descuento en variaciones
                        'dPrecio_descuento' => $variacionData['dPrecio_descuento'] ?? null,
                        'dFecha_inicio_descuento' => $variacionData['dFecha_inicio_descuento'] ?? null,
                        'dFecha_fin_descuento' => $variacionData['dFecha_fin_descuento'] ?? null,
                        'vMotivo_descuento' => $variacionData['vMotivo_descuento'] ?? null,
                        'bTiene_descuento' => isset($variacionData['bTiene_descuento']) && $variacionData['bTiene_descuento'] == '1' ? 1 : 0,

                        'iStock' => $variacionData['iStock'] ?? 0,

                        // Dimensiones de variación
                        'dPeso' => (isset($variacionData['dPeso']) && $variacionData['dPeso'] !== '') ? floatval($variacionData['dPeso']) : null,
                        'dLargo_cm' => (isset($variacionData['dLargo_cm']) && $variacionData['dLargo_cm'] !== '') ? floatval($variacionData['dLargo_cm']) : null,
                        'dAncho_cm' => (isset($variacionData['dAncho_cm']) && $variacionData['dAncho_cm'] !== '') ? floatval($variacionData['dAncho_cm']) : null,
                        'dAlto_cm' => (isset($variacionData['dAlto_cm']) && $variacionData['dAlto_cm'] !== '') ? floatval($variacionData['dAlto_cm']) : null,

                        'vClase_envio' => $claseEnvio,
                        'tDescripcion' => $variacionData['tDescripcion'] ?? null,
                        'bActivo' => isset($variacionData['bActivo']) ? 1 : 0,
                        'id_impuesto' => $variacionData['id_impuesto'] ?? null,
                    ]);

                    Log::info('Variación creada con ID:', ['id' => $variacion->id_variacion]);

                    // Guardar relación de atributos para la variación
                    if (isset($variacionData['id_atributo']) && isset($variacionData['id_atributo_valor'])) {
                        VariacionAtributo::create([
                            'id_variacion' => $variacion->id_variacion,
                            'id_atributo' => $variacionData['id_atributo'],
                            'id_atributo_valor' => $variacionData['id_atributo_valor']
                        ]);
                    }

                    // Guardar imagen de la variación si existe (ACTUALIZADO)
                    if ($request->hasFile("variaciones.{$key}.imagen_principal")) {
                        $variacion->guardarImagenPrincipal($request->file("variaciones.{$key}.imagen_principal"));
                    }

                    // Guardar GIF de la variación si existe (ACTUALIZADO)
                    if ($request->hasFile("variaciones.{$key}.gif")) {
                        $variacion->guardarGif($request->file("variaciones.{$key}.gif"));
                    }

                    // Guardar imágenes adicionales de la variación si existen (ACTUALIZADO)
                    if ($request->hasFile("variaciones.{$key}.imagenes_adicionales")) {
                        $imagenes = $request->file("variaciones.{$key}.imagenes_adicionales");
                        if (is_array($imagenes)) {
                            $variacion->guardarImagenesAdicionales($imagenes);
                        } else {
                            $variacion->guardarImagenesAdicionales([$imagenes]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('productos.show', $producto->id_producto)
                ->with('success', 'Producto creado exitosamente.')
                ->with('swal_success', true);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear producto: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            // Verificar si es error de tamaño POST
            if ($e instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
                return redirect()->back()
                    ->withInput()
                    ->with('post_max_size_error', true)
                    ->with('error', 'El tamaño total de los archivos excede el límite del servidor (50MB). Por favor, reduce el tamaño de los archivos.')
                    ->with('swal_error', true);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()])
                ->with('swal_error', true);
        }
    }

    /**
     * Display the specified resource (Vista pública para clientes).
     */
    public function showPublic($id)
    {
        $producto = Producto::with([
            'marca',
            'categoria',
            'etiquetas',
            'impuestos',
            'variaciones' => function ($query) {
                $query->where('bActivo', true);
            },
            'variaciones.atributos.valor',
            'variaciones.atributos.atributo',
            'variaciones.impuesto'
        ])
            ->where('bActivo', true)
            ->findOrFail($id);

        // Forzar carga de atributos de variaciones si no se cargaron correctamente
        foreach ($producto->variaciones as $variacion) {
            if (!$variacion->relationLoaded('atributos')) {
                $variacion->load('atributos.valor', 'atributos.atributo');
            }
        }

        return view('productos.show-public', compact('producto'));
    }

    /**
     * Display the specified resource (Vista de administración).
     */
    public function show(Producto $producto)
    {
        // Cargar todas las relaciones necesarias para la vista de administración
        $producto->load([
            'marca',
            'categoria',
            'etiquetas',
            'impuestos',
            'variaciones' => function ($query) {
                $query->where('bActivo', true); // Solo variaciones activas
            },
            'variaciones.atributos.valor',
            'variaciones.atributos.atributo',
            'variaciones.impuesto',
            'valoresAtributos.atributo' // Para atributos generales
        ]);

        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::with(['hijos' => function ($query) {
            $query->where('bActivo', true)
                ->with(['hijos' => function ($subQuery) {
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

        // CORREGIDO: Cargar atributos con sus valores activos - SIN orderBy('iOrden')
        $atributos = Atributo::with(['valoresActivos' => function ($query) {
            $query->where('bActivo', true);
        }])->where('bActivo', true)->get();

        // Obtener impuestos activos para el select
        $impuestos = Impuesto::where('bActivo', true)->orderBy('vNombre')->get();

        $etiquetasEspeciales = Etiqueta::whereIn('vNombre', ['nuevo', 'popular', 'descuento', 'destacado'])->get();

        $producto->load(['etiquetas', 'impuestos', 'variaciones.atributos', 'valoresAtributos.atributo', 'variaciones.impuesto']);

        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'etiquetas', 'atributos', 'impuestos', 'etiquetasEspeciales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        // Validar el tamaño total de la solicitud primero
        $contentLength = $request->server('CONTENT_LENGTH');
        $maxSize = 52 * 1024 * 1024; // 52MB en bytes

        if ($contentLength > $maxSize) {
            Log::warning('Intento de subida de archivos demasiado grande en actualización: ' . $contentLength . ' bytes');
            return redirect()->back()
                ->withInput()
                ->with('post_max_size_error', true)
                ->with('error', 'El tamaño total de los archivos (' . round($contentLength / (1024 * 1024), 2) . 'MB) excede el límite permitido de 50MB. Por favor, reduce el tamaño de los archivos.')
                ->with('swal_error', true);
        }

        // Similar al store pero con ignore para unique
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
            'dPrecio_compra' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio de compra debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'dPrecio_venta' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $value)) {
                        $fail('El precio de venta debe tener máximo 7 dígitos enteros y 2 decimales.');
                    }
                }
            ],
            'iStock' => 'required|integer|min:0|max:999999',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'id_impuesto' => 'nullable|exists:tbl_impuestos,id_impuesto',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagen_principal' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'gif_producto' => 'nullable|mimes:gif|max:10240',
            'imagenes' => 'nullable|array|max:7',
            'imagenes.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'imagenes_a_eliminar' => 'nullable|array',
            'imagenes_a_eliminar.*' => 'string',
            'atributos' => 'nullable|array',

            // VALIDACIONES PARA DIMENSIONES Y PESO
            'dPeso' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.999',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El peso debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El peso no puede ser negativo.');
                        } elseif ($value > 999.999) {
                            $fail('El peso no puede ser mayor a 999.999 kg.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 3) {
                                $fail('El peso debe tener máximo 3 decimales.');
                            }
                        }
                    }
                }
            ],
            'dLargo_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El largo debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El largo no puede ser negativo.');
                        } elseif ($value > 999.99) {
                            $fail('El largo no puede ser mayor a 999.99 cm.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 2) {
                                $fail('El largo debe tener máximo 2 decimales.');
                            }
                        }
                    }
                }
            ],
            'dAncho_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El ancho debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El ancho no puede ser negativo.');
                        } elseif ($value > 999.99) {
                            $fail('El ancho no puede ser mayor a 999.99 cm.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 2) {
                                $fail('El ancho debe tener máximo 2 decimales.');
                            }
                        }
                    }
                }
            ],
            'dAlto_cm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        if (!is_numeric($value)) {
                            $fail('El alto debe ser un número válido.');
                        } elseif ($value < 0) {
                            $fail('El alto no puede ser negativo.');
                        } elseif ($value > 999.99) {
                            $fail('El alto no puede ser mayor a 999.99 cm.');
                        } else {
                            $partes = explode('.', (string)$value);
                            if (isset($partes[1]) && strlen($partes[1]) > 2) {
                                $fail('El alto debe tener máximo 2 decimales.');
                            }
                        }
                    }
                }
            ],

            'vClase_envio' => 'nullable|in:estandar,express,fragil,grandes_dimensiones',
            'etiquetas_especiales' => 'nullable|array',
            'etiquetas_especiales.*' => 'in:nuevo,popular,descuento,destacado',

            // CAMPOS DE DESCUENTO
            'bTiene_descuento' => 'nullable|in:0,1',
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

                        if ($request->input('bTiene_descuento') == '1' && $value >= $request->dPrecio_venta) {
                            $fail('El precio de descuento debe ser menor que el precio de venta.');
                        }
                    }
                }
            ],
            'dFecha_inicio_descuento' => 'nullable|date',
            'dFecha_fin_descuento' => 'nullable|date|after_or_equal:dFecha_inicio_descuento',
            'vMotivo_descuento' => 'nullable|string|max:255',
            'variaciones' => 'nullable|array',
        ]);

        // Validaciones condicionales para descuento
        $validator->sometimes('dPrecio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_inicio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_fin_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        // Validación de variaciones (similar al store)
        if ($request->has('variaciones')) {
            $validator->after(function ($validator) use ($request, $producto) {
                foreach ($request->variaciones as $key => $variacion) {
                    $sku = $variacion['vSKU'] ?? '';
                    if (!empty($sku)) {
                        $query = ProductoVariacion::where('vSKU', $sku);

                        // Si es una variación existente, ignorar su propio ID
                        if (isset($variacion['id_variacion'])) {
                            $query->where('id_variacion', '!=', $variacion['id_variacion']);
                        }

                        if ($query->exists()) {
                            $validator->errors()->add("variaciones.{$key}.vSKU", "El SKU '{$sku}' ya está registrado para otra variación.");
                        }
                    }

                    // Validación de precio
                    if (isset($variacion['dPrecio']) && !empty($variacion['dPrecio'])) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $variacion['dPrecio'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio", 'El precio debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                    }

                    // Validación de descuento
                    if (isset($variacion['dPrecio_descuento']) && !empty($variacion['dPrecio_descuento'])) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $variacion['dPrecio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }

                        if (($variacion['bTiene_descuento'] ?? 0) == 1 && isset($variacion['dPrecio']) && $variacion['dPrecio_descuento'] >= $variacion['dPrecio']) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento debe ser menor que el precio normal.');
                        }
                    }

                    // Validación de campos requeridos cuando hay descuento
                    if (isset($variacion['bTiene_descuento']) && $variacion['bTiene_descuento'] == 1) {
                        if (empty($variacion['dPrecio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento es obligatorio cuando el descuento está activo.');
                        }
                        if (empty($variacion['dFecha_inicio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dFecha_inicio_descuento", 'La fecha de inicio es obligatoria cuando el descuento está activo.');
                        }
                        if (empty($variacion['dFecha_fin_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dFecha_fin_descuento", 'La fecha de fin es obligatoria cuando el descuento está activo.');
                        }

                        // Validar que la fecha de fin sea igual o posterior a la fecha de inicio
                        if (!empty($variacion['dFecha_inicio_descuento']) && !empty($variacion['dFecha_fin_descuento'])) {
                            if ($variacion['dFecha_fin_descuento'] < $variacion['dFecha_inicio_descuento']) {
                                $validator->errors()->add("variaciones.{$key}.dFecha_fin_descuento", 'La fecha de fin debe ser igual o posterior a la fecha de inicio.');
                            }
                        }
                    }
                }
            });
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('swal_error', true);
        }

        try {
            DB::beginTransaction();

            // Validar límite de imágenes
            $imagenesActuales = $producto->getNumeroImagenes();
            $nuevasImagenes = $request->hasFile('imagenes') ? count($request->file('imagenes')) : 0;
            $imagenesAEliminar = $request->imagenes_a_eliminar ? count($request->imagenes_a_eliminar) : 0;

            $espacioDisponible = $imagenesActuales - $imagenesAEliminar + $nuevasImagenes;

            if ($espacioDisponible > 8) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['imagenes' => 'No puedes tener más de 8 imágenes. Actualmente tienes ' . $imagenesActuales . ' imágenes.'])
                    ->with('swal_error', true);
            }

            // Eliminar imágenes seleccionadas SOLO si se especifica
            if ($request->has('imagenes_a_eliminar') && is_array($request->imagenes_a_eliminar) && count($request->imagenes_a_eliminar) > 0) {
                $producto->eliminarImagenesAdicionalesEspecificas($request->imagenes_a_eliminar);
            }

            // Actualizar producto
            $updateData = [
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

                // Dimensiones
                'dPeso' => ($request->dPeso !== null && $request->dPeso !== '') ? floatval($request->dPeso) : null,
                'dLargo_cm' => ($request->dLargo_cm !== null && $request->dLargo_cm !== '') ? floatval($request->dLargo_cm) : null,
                'dAncho_cm' => ($request->dAncho_cm !== null && $request->dAncho_cm !== '') ? floatval($request->dAncho_cm) : null,
                'dAlto_cm' => ($request->dAlto_cm !== null && $request->dAlto_cm !== '') ? floatval($request->dAlto_cm) : null,

                'vClase_envio' => $request->vClase_envio ?: null,

                // MAPEO: descuento del formulario -> descuento en BD
                'bTiene_descuento' => $request->has('bTiene_descuento') && $request->bTiene_descuento == '1' ? true : false,
                'dPrecio_descuento' => $request->dPrecio_descuento ?: null,
                'dFecha_inicio_descuento' => $request->dFecha_inicio_descuento ?: null,
                'dFecha_fin_descuento' => $request->dFecha_fin_descuento ?: null,
                'vMotivo_descuento' => $request->vMotivo_descuento ?: null,
            ];

            $producto->update($updateData);

            // Sincronizar impuesto
            if ($request->has('id_impuesto')) {
                if (!empty($request->id_impuesto)) {
                    $producto->impuestos()->sync([$request->id_impuesto]);
                } else {
                    $producto->impuestos()->sync([]);
                }
                $producto->recalcularPrecioFinal();
            }

            // Guardar imagen principal si se subió una nueva (ACTUALIZADO)
            if ($request->hasFile('imagen_principal')) {
                $producto->guardarImagenPrincipal($request->file('imagen_principal'));
            }

            // Guardar GIF si se subió uno nuevo (ACTUALIZADO)
            if ($request->hasFile('gif_producto')) {
                $producto->guardarGif($request->file('gif_producto'));
            }

            // Guardar nuevas imágenes adicionales (ACTUALIZADO)
            if ($request->hasFile('imagenes') && count($request->file('imagenes')) > 0) {
                $producto->guardarImagenesAdicionales($request->file('imagenes'));
            }

            // Sincronizar etiquetas
            $producto->etiquetas()->sync($request->etiquetas ?? []);

            // Actualizar atributos del producto
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

            // Actualizar variaciones
            if ($request->has('variaciones')) {
                $idsVariacionesExistentes = $producto->variaciones()->pluck('id_variacion')->toArray();
                $idsVariacionesProcesadas = [];

                foreach ($request->variaciones as $key => $variacionData) {
                    if (!isset($variacionData['vSKU']) || empty($variacionData['vSKU'])) {
                        continue;
                    }

                    $claseEnvio = $variacionData['vClase_envio'] ?? null;
                    if (empty($claseEnvio) && $producto->vClase_envio) {
                        $claseEnvio = $producto->vClase_envio;
                    } elseif (empty($claseEnvio)) {
                        $claseEnvio = 'estandar';
                    }

                    if (isset($variacionData['id_variacion'])) {
                        // Actualizar variación existente
                        $variacion = ProductoVariacion::find($variacionData['id_variacion']);
                        if ($variacion && $variacion->id_producto == $producto->id_producto) {
                            $variacion->update([
                                'vSKU' => strtoupper($variacionData['vSKU']),
                                'dPrecio' => $variacionData['dPrecio'] ?? $producto->dPrecio_venta,

                                // MAPEO para variaciones
                                'dPrecio_descuento' => $variacionData['dPrecio_descuento'] ?? null,
                                'dFecha_inicio_descuento' => $variacionData['dFecha_inicio_descuento'] ?? null,
                                'dFecha_fin_descuento' => $variacionData['dFecha_fin_descuento'] ?? null,
                                'vMotivo_descuento' => $variacionData['vMotivo_descuento'] ?? null,
                                'bTiene_descuento' => isset($variacionData['bTiene_descuento']) && $variacionData['bTiene_descuento'] == '1' ? 1 : 0,

                                'iStock' => $variacionData['iStock'] ?? 0,

                                // Dimensiones de variación
                                'dPeso' => (isset($variacionData['dPeso']) && $variacionData['dPeso'] !== '') ? floatval($variacionData['dPeso']) : null,
                                'dLargo_cm' => (isset($variacionData['dLargo_cm']) && $variacionData['dLargo_cm'] !== '') ? floatval($variacionData['dLargo_cm']) : null,
                                'dAncho_cm' => (isset($variacionData['dAncho_cm']) && $variacionData['dAncho_cm'] !== '') ? floatval($variacionData['dAncho_cm']) : null,
                                'dAlto_cm' => (isset($variacionData['dAlto_cm']) && $variacionData['dAlto_cm'] !== '') ? floatval($variacionData['dAlto_cm']) : null,

                                'vClase_envio' => $claseEnvio,
                                'tDescripcion' => $variacionData['tDescripcion'] ?? null,
                                'bActivo' => isset($variacionData['bActivo']) ? 1 : 0,
                                'id_impuesto' => $variacionData['id_impuesto'] ?? null,
                            ]);

                            $idsVariacionesProcesadas[] = $variacion->id_variacion;

                            // Actualizar relación de atributos
                            $variacion->atributos()->delete();

                            if (isset($variacionData['id_atributo']) && isset($variacionData['id_atributo_valor'])) {
                                VariacionAtributo::create([
                                    'id_variacion' => $variacion->id_variacion,
                                    'id_atributo' => $variacionData['id_atributo'],
                                    'id_atributo_valor' => $variacionData['id_atributo_valor']
                                ]);
                            }

                            // Actualizar imagen SOLO si se sube una nueva (ACTUALIZADO)
                            if ($request->hasFile("variaciones.{$key}.imagen_principal")) {
                                $variacion->guardarImagenPrincipal($request->file("variaciones.{$key}.imagen_principal"));
                            }

                            // Actualizar GIF SOLO si se sube uno nuevo (ACTUALIZADO)
                            if ($request->hasFile("variaciones.{$key}.gif")) {
                                $variacion->guardarGif($request->file("variaciones.{$key}.gif"));
                            }

                            // Actualizar imágenes adicionales SOLO si se suben nuevas (ACTUALIZADO)
                            if ($request->hasFile("variaciones.{$key}.imagenes_adicionales")) {
                                $imagenes = $request->file("variaciones.{$key}.imagenes_adicionales");
                                if (is_array($imagenes)) {
                                    $variacion->guardarImagenesAdicionales($imagenes);
                                } else {
                                    $variacion->guardarImagenesAdicionales([$imagenes]);
                                }
                            }

                            // Eliminar imagen SOLO si se marca explícitamente
                            if (isset($variacionData['eliminar_imagen']) && $variacionData['eliminar_imagen'] == '1') {
                                $variacion->eliminarImagenPrincipal();
                            }
                        }
                    } else {
                        // Crear nueva variación
                        $variacion = ProductoVariacion::create([
                            'id_producto' => $producto->id_producto,
                            'vSKU' => strtoupper($variacionData['vSKU']),
                            'dPrecio' => $variacionData['dPrecio'] ?? $producto->dPrecio_venta,

                            // MAPEO para nuevas variaciones
                            'dPrecio_descuento' => $variacionData['dPrecio_descuento'] ?? null,
                            'dFecha_inicio_descuento' => $variacionData['dFecha_inicio_descuento'] ?? null,
                            'dFecha_fin_descuento' => $variacionData['dFecha_fin_descuento'] ?? null,
                            'vMotivo_descuento' => $variacionData['vMotivo_descuento'] ?? null,
                            'bTiene_descuento' => isset($variacionData['bTiene_descuento']) && $variacionData['bTiene_descuento'] == '1' ? 1 : 0,

                            'iStock' => $variacionData['iStock'] ?? 0,

                            // Dimensiones de variación
                            'dPeso' => (isset($variacionData['dPeso']) && $variacionData['dPeso'] !== '') ? floatval($variacionData['dPeso']) : null,
                            'dLargo_cm' => (isset($variacionData['dLargo_cm']) && $variacionData['dLargo_cm'] !== '') ? floatval($variacionData['dLargo_cm']) : null,
                            'dAncho_cm' => (isset($variacionData['dAncho_cm']) && $variacionData['dAncho_cm'] !== '') ? floatval($variacionData['dAncho_cm']) : null,
                            'dAlto_cm' => (isset($variacionData['dAlto_cm']) && $variacionData['dAlto_cm'] !== '') ? floatval($variacionData['dAlto_cm']) : null,

                            'vClase_envio' => $claseEnvio,
                            'tDescripcion' => $variacionData['tDescripcion'] ?? null,
                            'bActivo' => isset($variacionData['bActivo']) ? 1 : 0,
                            'id_impuesto' => $variacionData['id_impuesto'] ?? null,
                        ]);

                        $idsVariacionesProcesadas[] = $variacion->id_variacion;

                        if (isset($variacionData['id_atributo']) && isset($variacionData['id_atributo_valor'])) {
                            VariacionAtributo::create([
                                'id_variacion' => $variacion->id_variacion,
                                'id_atributo' => $variacionData['id_atributo'],
                                'id_atributo_valor' => $variacionData['id_atributo_valor']
                            ]);
                        }

                        if ($request->hasFile("variaciones.{$key}.imagen_principal")) {
                            $variacion->guardarImagenPrincipal($request->file("variaciones.{$key}.imagen_principal"));
                        }

                        if ($request->hasFile("variaciones.{$key}.gif")) {
                            $variacion->guardarGif($request->file("variaciones.{$key}.gif"));
                        }

                        if ($request->hasFile("variaciones.{$key}.imagenes_adicionales")) {
                            $imagenes = $request->file("variaciones.{$key}.imagenes_adicionales");
                            if (is_array($imagenes)) {
                                $variacion->guardarImagenesAdicionales($imagenes);
                            } else {
                                $variacion->guardarImagenesAdicionales([$imagenes]);
                            }
                        }
                    }
                }

                // Eliminar variaciones que ya no están en el formulario
                $variacionesAEliminar = array_diff($idsVariacionesExistentes, $idsVariacionesProcesadas);
                foreach ($variacionesAEliminar as $idVariacion) {
                    $variacion = ProductoVariacion::find($idVariacion);
                    if ($variacion) {
                        $variacion->eliminarTodasLasImagenes();
                        $variacion->atributos()->delete();
                        $variacion->delete();
                    }
                }
            }

            DB::commit();

            return redirect()->route('productos.show', $producto->id_producto)
                ->with('success', 'Producto actualizado exitosamente.')
                ->with('swal_save', true);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar producto: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()])
                ->with('swal_error', true);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        try {
            DB::beginTransaction();

            // Eliminar imágenes de variaciones
            foreach ($producto->variaciones as $variacion) {
                $variacion->eliminarTodasLasImagenes();
                $variacion->atributos()->delete();
            }

            // Eliminar variaciones
            $producto->variaciones()->delete();

            // Eliminar imágenes principales
            $producto->eliminarTodasLasImagenes();

            // Eliminar relaciones
            $producto->etiquetas()->detach();
            $producto->impuestos()->detach();
            DB::table('tbl_producto_atributos')->where('id_producto', $producto->id_producto)->delete();

            // Eliminar producto
            $producto->delete();

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente')
                ->with('swal_success', true);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar producto: ' . $e->getMessage());

            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage())
                ->with('swal_error', true);
        }
    }

    /**
     * Show the catalog page (public).
     */
    public function catalogo()
    {
        $productos = Producto::with(['marca', 'categoria', 'etiquetas', 'impuestos', 'variaciones' => function ($query) {
            $query->where('bActivo', true);
        }])
            ->where('bActivo', true)
            ->orderBy('vNombre')
            ->get();

        return view('productos.catalogo', compact('productos'));
    }

    // ============ FUNCIONES PARA MANEJO DE IMÁGENES (AHORA USAN LOS MÉTODOS DEL MODELO) ============

    /**
     * Guardar imagen principal del producto - MANTENIDO POR COMPATIBILIDAD
     */
    private function guardarImagenPrincipal($producto, $imagen)
    {
        return $producto->guardarImagenPrincipal($imagen);
    }

    /**
     * Guardar GIF del producto - MANTENIDO POR COMPATIBILIDAD
     */
    private function guardarGif($producto, $gif)
    {
        return $producto->guardarGif($gif);
    }

    /**
     * Guardar imágenes adicionales del producto - MANTENIDO POR COMPATIBILIDAD
     */
    private function guardarImagenes($producto, $imagenes)
    {
        return $producto->guardarImagenesAdicionales($imagenes);
    }

    /**
     * Eliminar imagen principal del producto - MANTENIDO POR COMPATIBILIDAD
     */
    private function eliminarImagenPrincipal($producto)
    {
        $producto->eliminarImagenPrincipal();
    }

    /**
     * Eliminar GIF del producto - MANTENIDO POR COMPATIBILIDAD
     */
    private function eliminarGif($producto)
    {
        $producto->eliminarGif();
    }

    /**
     * Eliminar imágenes específicas del producto - MANTENIDO POR COMPATIBILIDAD
     */
    private function eliminarImagenesEspecificas($producto, $imagenesAEliminar)
    {
        $producto->eliminarImagenesAdicionalesEspecificas($imagenesAEliminar);
    }

    /**
     * Eliminar todas las imágenes del producto - MANTENIDO POR COMPATIBILIDAD
     */
    private function eliminarTodasLasImagenes($producto)
    {
        $producto->eliminarTodasLasImagenes();
    }

    /**
     * Guardar imagen de variación - MANTENIDO POR COMPATIBILIDAD
     */
    private function guardarImagenVariacion($variacion, $imagen)
    {
        return $variacion->guardarImagenPrincipal($imagen);
    }

    /**
     * Eliminar imagen de variación - MANTENIDO POR COMPATIBILIDAD
     */
    private function eliminarImagenVariacion($variacion)
    {
        $variacion->eliminarImagenPrincipal();
    }

    // ============ MÉTODOS PARA ATRIBUTOS ============

    /**
     * Show atributos page.
     */
    public function atributos($id)
    {
        $producto = Producto::with(['variaciones.atributos.atributo', 'variaciones.atributos.valor'])
            ->findOrFail($id);

        return view('productos.atributos', compact('producto'));
    }

    /**
     * Show form to assign atributos.
     */
    public function asignarAtributos($id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($id);

        // CORREGIDO: Cargar atributos con sus valores activos - SIN orderBy('iOrden')
        $atributos = Atributo::with(['valoresActivos' => function ($query) {
            $query->where('bActivo', true);
        }])->where('bActivo', true)->get();

        return view('productos.asignar-atributos', compact('producto', 'atributos'));
    }

    /**
     * Save atributos assignment.
     */
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
                ->with('success', 'Atributos asignados exitosamente')
                ->with('swal_success', true);
        } catch (\Exception $e) {
            DB::rollBack();

            $errorMessage = 'Error al asignar atributos: ' . $e->getMessage();
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                $errorMessage = 'Error: Existe un problema con los datos enviados. Verifica que todos los valores sean válidos.';
            }

            Log::error('Error al asignar atributos: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage)
                ->with('swal_error', true);
        }
    }

    // ============ MÉTODOS PARA VARIACIONES ============

    /**
     * Show variaciones page.
     */
    public function variaciones()
    {
        $productos = Producto::with(['variaciones.atributos.valor', 'variaciones.atributos.atributo', 'marca', 'categoria'])
            ->whereHas('variaciones')
            ->orderBy('vNombre')
            ->get();

        return view('productos.variaciones', compact('productos'));
    }

    // ============ MÉTODOS API PARA FORMULARIOS RÁPIDOS ============

    /**
     * Quick create categoria via AJAX.
     */
    public function quickCreateCategoria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_categorias,vNombre',
            'vSlug' => 'required|max:100|unique:tbl_categorias,vSlug',
            'id_categoria_padre' => 'nullable|exists:tbl_categorias,id_categoria',
            'tDescripcion' => 'nullable|string',
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
            $categoria = new Categoria();
            $categoria->vNombre = $request->vNombre;
            $categoria->vSlug = $request->vSlug;
            $categoria->id_categoria_padre = $request->id_categoria_padre;
            $categoria->tDescripcion = $request->tDescripcion;
            $categoria->bActivo = $request->has('bActivo') ? $request->bActivo : true;

            $ultimoOrden = Categoria::where('id_categoria_padre', $request->id_categoria_padre)->max('iOrden');
            $categoria->iOrden = $ultimoOrden ? $ultimoOrden + 1 : 0;

            $categoria->save();

            return response()->json([
                'success' => true,
                'categoria' => $categoria,
                'message' => 'Categoría creada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear categoría rápida: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick create marca via AJAX.
     */
    public function quickCreateMarca(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_marcas,vNombre',
            'tDescripcion' => 'nullable|string'
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
            Log::error('Error al crear marca rápida: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear marca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick create etiqueta via AJAX.
     */
    public function quickCreateEtiqueta(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_etiquetas,vNombre',
            'color' => 'nullable|max:7',
            'tDescripcion' => 'nullable|string'
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
            Log::error('Error al crear etiqueta rápida: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear etiqueta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick create atributo via AJAX.
     */
    public function quickCreateAtributo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre',
            'vSlug' => 'nullable|max:100|unique:tbl_atributos,vSlug',
            'tDescripcion' => 'nullable|string'
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
            Log::error('Error al crear atributo rápido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear atributo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick create valor de atributo via AJAX.
     */
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
            Log::error('Error al crear valor de atributo rápido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear valor: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ MÉTODOS API PARA OBTENER DATOS JSON ============

    /**
     * Get categorias in JSON format.
     */
    public function getJsonCategorias()
    {
        $categorias = Categoria::where('bActivo', true)
            ->orderBy('vNombre')
            ->get(['id_categoria', 'vNombre', 'id_categoria_padre', 'tDescripcion']);

        return response()->json([
            'success' => true,
            'categorias' => $categorias
        ]);
    }

    /**
     * Get marcas in JSON format.
     */
    public function getJsonMarcas()
    {
        $marcas = Marca::orderBy('vNombre')
            ->get(['id_marca', 'vNombre', 'tDescripcion']);

        return response()->json([
            'success' => true,
            'marcas' => $marcas
        ]);
    }

    /**
     * Get etiquetas in JSON format.
     */
    public function getJsonEtiquetas()
    {
        $etiquetas = Etiqueta::orderBy('vNombre')
            ->get(['id_etiqueta', 'vNombre', 'color', 'tDescripcion']);

        return response()->json([
            'success' => true,
            'etiquetas' => $etiquetas
        ]);
    }

    /**
     * Get atributos in JSON format.
     */
    public function getJsonAtributos()
    {
        // CORREGIDO: Cargar atributos con sus valores activos - SIN orderBy('iOrden')
        $atributos = Atributo::with(['valoresActivos' => function ($query) {
            $query->where('bActivo', true);
        }])->where('bActivo', true)
            ->orderBy('vNombre')
            ->get();

        return response()->json([
            'success' => true,
            'atributos' => $atributos
        ]);
    }

    /**
     * Get impuestos in JSON format.
     */
    public function getJsonImpuestos()
    {
        $impuestos = Impuesto::where('bActivo', true)
            ->orderBy('vNombre')
            ->get(['id_impuesto', 'vNombre', 'eTipo', 'dPorcentaje', 'tDescripcion']);

        return response()->json([
            'success' => true,
            'impuestos' => $impuestos
        ]);
    }

    // ============ NUEVOS MÉTODOS PARA VERIFICACIÓN EN TIEMPO REAL ============

    /**
     * Verificar si un nombre de producto ya existe (para validación en tiempo real)
     */
    public function verificarNombre(Request $request)
    {
        try {
            $nombre = $request->get('nombre');

            if (empty($nombre)) {
                return response()->json(['exists' => false]);
            }

            $exists = Producto::where('vNombre', $nombre)->exists();

            return response()->json([
                'exists' => $exists,
                'nombre' => $nombre
            ]);
        } catch (\Exception $e) {
            Log::error('Error al verificar nombre: ' . $e->getMessage());
            return response()->json([
                'exists' => false,
                'error' => 'Error al verificar'
            ], 500);
        }
    }

    /**
     * Verificar si un SKU de producto ya existe (para validación en tiempo real)
     */
    public function verificarSKU(Request $request)
    {
        try {
            $sku = $request->get('sku');

            if (empty($sku)) {
                return response()->json(['exists' => false]);
            }

            $exists = Producto::where('vCodigo_barras', $sku)->exists();

            return response()->json([
                'exists' => $exists,
                'sku' => $sku
            ]);
        } catch (\Exception $e) {
            Log::error('Error al verificar SKU: ' . $e->getMessage());
            return response()->json([
                'exists' => false,
                'error' => 'Error al verificar'
            ], 500);
        }
    }
}
