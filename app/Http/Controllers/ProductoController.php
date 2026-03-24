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
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
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
        
        $atributos = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true);
        }])->where('bActivo', true)->get();
        
        $impuestos = Impuesto::where('bActivo', true)
            ->orderBy('vNombre')
            ->get();
        
        return view('productos.create', compact('categorias', 'marcas', 'etiquetas', 'atributos', 'impuestos'));
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
                ->with('error', 'El tamaño total de los archivos (' . round($contentLength / (1024 * 1024), 2) . 'MB) excede el límite permitido de 50MB.')
                ->with('swal_error', true);
        }

        // ============ VALIDACIÓN CORREGIDA - IMÁGENES OPCIONALES ============
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
            ],
            'dPrecio_venta' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999.99',
            ],
            'iStock' => 'required|integer|min:0|max:999999',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'id_impuesto' => 'nullable|exists:tbl_impuestos,id_impuesto',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            
            // ============ IMÁGENES OPCIONALES (NO required) ============
            'imagen_principal' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'gif_producto' => 'nullable|mimes:gif|max:10240',
            'imagenes' => 'nullable|array|max:7',
            'imagenes.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            
            'atributos' => 'nullable|array',
            
            'dPeso' => 'nullable|numeric|min:0|max:999.999',
            'dLargo_cm' => 'nullable|numeric|min:0|max:999.99',
            'dAncho_cm' => 'nullable|numeric|min:0|max:999.99',
            'dAlto_cm' => 'nullable|numeric|min:0|max:999.99',
            'vClase_envio' => 'nullable|in:estandar,express,fragil,grandes_dimensiones',
            
            'bTiene_descuento' => 'nullable|in:0,1',
            'dPrecio_descuento' => 'nullable|numeric|min:0|max:9999999.99',
            'dFecha_inicio_descuento' => 'nullable|date',
            'dFecha_fin_descuento' => 'nullable|date|after_or_equal:dFecha_inicio_descuento',
            'vMotivo_descuento' => 'nullable|string|max:255',
            'variaciones' => 'nullable|array',
        ]);

        // Validación condicional para descuento
        $validator->sometimes('dPrecio_descuento', 'required|lt:dPrecio_venta', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_inicio_descuento', 'required|date', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_fin_descuento', 'required|date|after_or_equal:dFecha_inicio_descuento', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        // Validación de variaciones
        if ($request->has('variaciones') && !empty($request->variaciones)) {
            $validator->after(function ($validator) use ($request) {
                foreach ($request->variaciones as $key => $variacion) {
                    // Solo validar SKU si está presente
                    $sku = $variacion['vSKU'] ?? '';
                    if (!empty($sku) && ProductoVariacion::where('vSKU', $sku)->exists()) {
                        $validator->errors()->add("variaciones.{$key}.vSKU", "El SKU '{$sku}' ya está registrado.");
                    }
                    
                    // Validar precio si está presente
                    if (isset($variacion['dPrecio']) && !empty($variacion['dPrecio'])) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $variacion['dPrecio'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio", 'El precio debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                    }
                    
                    // Validar descuento si está activo
                    if (isset($variacion['bTiene_descuento']) && $variacion['bTiene_descuento'] == 1) {
                        if (empty($variacion['dPrecio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento es obligatorio.');
                        }
                        if (empty($variacion['dFecha_inicio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dFecha_inicio_descuento", 'La fecha de inicio es obligatoria.');
                        }
                        if (empty($variacion['dFecha_fin_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dFecha_fin_descuento", 'La fecha de fin es obligatoria.');
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
                'dPeso' => ($request->dPeso !== null && $request->dPeso !== '') ? floatval($request->dPeso) : null,
                'dLargo_cm' => ($request->dLargo_cm !== null && $request->dLargo_cm !== '') ? floatval($request->dLargo_cm) : null,
                'dAncho_cm' => ($request->dAncho_cm !== null && $request->dAncho_cm !== '') ? floatval($request->dAncho_cm) : null,
                'dAlto_cm' => ($request->dAlto_cm !== null && $request->dAlto_cm !== '') ? floatval($request->dAlto_cm) : null,
                'vClase_envio' => $request->vClase_envio ?: null,
                'bTiene_descuento' => $request->has('bTiene_descuento') && $request->bTiene_descuento == '1',
                'dPrecio_descuento' => $request->dPrecio_descuento ? floatval($request->dPrecio_descuento) : null,
                'vMotivo_descuento' => $request->vMotivo_descuento ?: null,
            ];

            // Guardar fechas con hora
            if ($request->dFecha_inicio_descuento) {
                $productoData['dFecha_inicio_descuento'] = $request->dFecha_inicio_descuento . ' 00:00:00';
            }
            if ($request->dFecha_fin_descuento) {
                $productoData['dFecha_fin_descuento'] = $request->dFecha_fin_descuento . ' 23:59:59';
            }

            $producto = Producto::create($productoData);

            // Sincronizar impuesto
            if ($request->id_impuesto) {
                $producto->impuestos()->sync([$request->id_impuesto]);
                $producto->recalcularPrecioFinal();
            }

            // ============ GUARDAR IMÁGENES SOLO SI EXISTEN ============
            // Guardar imagen principal SOLO si se subió
            if ($request->hasFile('imagen_principal') && $request->file('imagen_principal')->isValid()) {
                $producto->guardarImagenPrincipal($request->file('imagen_principal'));
                Log::info('Imagen principal guardada');
            }

            // Guardar GIF SOLO si se subió
            if ($request->hasFile('gif_producto') && $request->file('gif_producto')->isValid()) {
                $producto->guardarGif($request->file('gif_producto'));
                Log::info('GIF guardado');
            }

            // Guardar imágenes adicionales SOLO si existen
            if ($request->hasFile('imagenes')) {
                $imagenes = $request->file('imagenes');
                $imagenesValidas = array_filter($imagenes, function($file) {
                    return $file && $file->isValid();
                });
                
                if (!empty($imagenesValidas)) {
                    $producto->guardarImagenesAdicionales($imagenesValidas);
                    Log::info('Imágenes adicionales guardadas: ' . count($imagenesValidas));
                }
            }

            // Sincronizar etiquetas
            if ($request->has('etiquetas')) {
                $producto->etiquetas()->sync($request->etiquetas);
            }

            // Guardar atributos
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

            // Guardar variaciones
            if ($request->has('variaciones') && !empty($request->variaciones)) {
                foreach ($request->variaciones as $key => $variacionData) {
                    if (!isset($variacionData['vSKU']) || empty($variacionData['vSKU'])) {
                        continue;
                    }

                    // Solo guardar si tiene precio definido
                    if (!isset($variacionData['dPrecio']) || empty($variacionData['dPrecio'])) {
                        continue;
                    }

                    $claseEnvio = $variacionData['vClase_envio'] ?? null;
                    if (empty($claseEnvio) && $producto->vClase_envio) {
                        $claseEnvio = $producto->vClase_envio;
                    } elseif (empty($claseEnvio)) {
                        $claseEnvio = 'estandar';
                    }

                    // Calcular precio final
                    $precioBaseVariacion = floatval($variacionData['dPrecio']);
                    $tieneDescuentoVariacion = isset($variacionData['bTiene_descuento']) && $variacionData['bTiene_descuento'] == '1';
                    $precioDescuentoVariacion = isset($variacionData['dPrecio_descuento']) ? floatval($variacionData['dPrecio_descuento']) : null;

                    $aplicaDescuento = false;
                    if ($tieneDescuentoVariacion && $precioDescuentoVariacion !== null && $precioDescuentoVariacion > 0 && $precioDescuentoVariacion < $precioBaseVariacion) {
                        $fechaActual = new \DateTime();
                        $fechaActual->setTime(0, 0, 0);
                        
                        $fechaInicio = null;
                        $fechaFin = null;
                        
                        if (!empty($variacionData['dFecha_inicio_descuento'])) {
                            $fechaInicio = new \DateTime($variacionData['dFecha_inicio_descuento']);
                            $fechaInicio->setTime(0, 0, 0);
                        }
                        
                        if (!empty($variacionData['dFecha_fin_descuento'])) {
                            $fechaFin = new \DateTime($variacionData['dFecha_fin_descuento']);
                            $fechaFin->setTime(23, 59, 59);
                        }
                        
                        if ($fechaInicio && $fechaFin) {
                            $aplicaDescuento = $fechaActual >= $fechaInicio && $fechaActual <= $fechaFin;
                        } else if ($fechaInicio && !$fechaFin) {
                            $aplicaDescuento = $fechaActual >= $fechaInicio;
                        } else if (!$fechaInicio && $fechaFin) {
                            $aplicaDescuento = $fechaActual <= $fechaFin;
                        } else {
                            $aplicaDescuento = true;
                        }
                    }

                    $precioBaseParaImpuestos = $aplicaDescuento ? $precioDescuentoVariacion : $precioBaseVariacion;

                    // Calcular impuestos
                    $totalImpuestosVariacion = 0;
                    $idImpuestoVariacion = $variacionData['id_impuesto'] ?? null;

                    if ($idImpuestoVariacion) {
                        $impuestoVariacion = Impuesto::find($idImpuestoVariacion);
                        if ($impuestoVariacion && $impuestoVariacion->bActivo) {
                            $totalImpuestosVariacion = $precioBaseParaImpuestos * ($impuestoVariacion->dPorcentaje / 100);
                        }
                    } elseif ($request->id_impuesto) {
                        $impuestoPrincipal = Impuesto::find($request->id_impuesto);
                        if ($impuestoPrincipal && $impuestoPrincipal->bActivo) {
                            $totalImpuestosVariacion = $precioBaseParaImpuestos * ($impuestoPrincipal->dPorcentaje / 100);
                        }
                    }

                    $precioFinalVariacion = $precioBaseParaImpuestos + $totalImpuestosVariacion;
                    
                    $variacionDataToCreate = [
                        'id_producto' => $producto->id_producto,
                        'vSKU' => strtoupper($variacionData['vSKU']),
                        'dPrecio' => $variacionData['dPrecio'],
                        'dPrecio_descuento' => $variacionData['dPrecio_descuento'] ?? null,
                        'dPrecio_final' => $precioFinalVariacion,
                        'vMotivo_descuento' => $variacionData['vMotivo_descuento'] ?? null,
                        'bTiene_descuento' => $tieneDescuentoVariacion ? 1 : 0,
                        'iStock' => $variacionData['iStock'] ?? 0,
                        'dPeso' => (isset($variacionData['dPeso']) && $variacionData['dPeso'] !== '') ? floatval($variacionData['dPeso']) : null,
                        'dLargo_cm' => (isset($variacionData['dLargo_cm']) && $variacionData['dLargo_cm'] !== '') ? floatval($variacionData['dLargo_cm']) : null,
                        'dAncho_cm' => (isset($variacionData['dAncho_cm']) && $variacionData['dAncho_cm'] !== '') ? floatval($variacionData['dAncho_cm']) : null,
                        'dAlto_cm' => (isset($variacionData['dAlto_cm']) && $variacionData['dAlto_cm'] !== '') ? floatval($variacionData['dAlto_cm']) : null,
                        'vClase_envio' => $claseEnvio,
                        'tDescripcion' => $variacionData['tDescripcion'] ?? null,
                        'bActivo' => isset($variacionData['bActivo']) ? 1 : 0,
                        'id_impuesto' => $idImpuestoVariacion,
                    ];

                    // Guardar fechas de descuento
                    if (!empty($variacionData['dFecha_inicio_descuento'])) {
                        $variacionDataToCreate['dFecha_inicio_descuento'] = $variacionData['dFecha_inicio_descuento'] . ' 00:00:00';
                    }
                    if (!empty($variacionData['dFecha_fin_descuento'])) {
                        $variacionDataToCreate['dFecha_fin_descuento'] = $variacionData['dFecha_fin_descuento'] . ' 23:59:59';
                    }

                    $variacion = ProductoVariacion::create($variacionDataToCreate);

                    // Guardar relación con atributos
                    if (isset($variacionData['id_atributo']) && isset($variacionData['id_atributo_valor'])) {
                        VariacionAtributo::create([
                            'id_variacion' => $variacion->id_variacion,
                            'id_atributo' => $variacionData['id_atributo'],
                            'id_atributo_valor' => $variacionData['id_atributo_valor']
                        ]);
                    }

                    // Guardar imágenes de la variación SOLO si existen
                    if ($request->hasFile("variaciones.{$key}.imagen_principal") && $request->file("variaciones.{$key}.imagen_principal")->isValid()) {
                        $variacion->guardarImagenPrincipal($request->file("variaciones.{$key}.imagen_principal"));
                    }
                    
                    if ($request->hasFile("variaciones.{$key}.gif") && $request->file("variaciones.{$key}.gif")->isValid()) {
                        $variacion->guardarGif($request->file("variaciones.{$key}.gif"));
                    }
                    
                    if ($request->hasFile("variaciones.{$key}.imagenes_adicionales")) {
                        $imagenesVariacion = $request->file("variaciones.{$key}.imagenes_adicionales");
                        $imagenesValidas = array_filter($imagenesVariacion, function($file) {
                            return $file && $file->isValid();
                        });
                        if (!empty($imagenesValidas)) {
                            $variacion->guardarImagenesAdicionales($imagenesValidas);
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
            
            if ($e instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
                return redirect()->back()
                    ->withInput()
                    ->with('post_max_size_error', true)
                    ->with('error', 'El tamaño total de los archivos excede el límite del servidor (50MB).')
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
            'variaciones' => function($query) {
                $query->where('bActivo', true);
            },
            'variaciones.atributos.valor', 
            'variaciones.atributos.atributo',
            'variaciones.impuesto'
        ])
        ->where('bActivo', true)
        ->findOrFail($id);
        
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
        $producto->load([
            'marca', 
            'categoria', 
            'etiquetas', 
            'impuestos', 
            'variaciones' => function ($query) {
                $query->where('bActivo', true);
            },
            'variaciones.atributos.valor', 
            'variaciones.atributos.atributo',
            'variaciones.impuesto',
            'valoresAtributos.atributo'
        ]);
        
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
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
        
        $atributos = Atributo::with(['valoresActivos' => function($query) {
            $query->where('bActivo', true);
        }])->where('bActivo', true)->get();
        
        $impuestos = Impuesto::where('bActivo', true)
            ->orderBy('vNombre')
            ->get();
        
        $producto->load([
            'etiquetas', 
            'impuestos', 
            'variaciones.atributos.valor', 
            'variaciones.atributos.atributo',
            'variaciones.impuesto',
            'variaciones.imagenesRegistradas',
            'valoresAtributos.atributo'
        ]);
        
        $productosExistentes = Producto::where('id_producto', '!=', $producto->id_producto)
            ->select('vCodigo_barras as sku', 'vNombre as nombre')
            ->get()
            ->map(function($p) {
                return ['sku' => $p->sku, 'nombre' => $p->nombre];
            })
            ->values();
        
        $variacionesExistentes = ProductoVariacion::where('id_producto', '!=', $producto->id_producto)
            ->select('vSKU as sku')
            ->get()
            ->map(function($v) {
                return ['sku' => $v->sku];
            })
            ->values();
        
        $variacionesExistentesData = [];
        foreach($producto->variaciones as $variacion) {
            $key = '';
            foreach($variacion->atributos as $atributo) {
                $key = $atributo->id_atributo . '_' . $atributo->id_atributo_valor;
            }
            
            $imagenes = [];
            if ($variacion->imagen_principal_url) $imagenes[] = $variacion->imagen_principal_url;
            if ($variacion->gif_url) $imagenes[] = $variacion->gif_url;
            foreach ($variacion->imagenes_adicionales_urls as $img) $imagenes[] = $img;
            
            $fechaInicio = '';
            $fechaFin = '';
            
            if (!empty($variacion->dFecha_inicio_descuento)) {
                $date = new \DateTime($variacion->dFecha_inicio_descuento);
                $fechaInicio = $date->format('Y-m-d');
            }
            
            if (!empty($variacion->dFecha_fin_descuento)) {
                $date = new \DateTime($variacion->dFecha_fin_descuento);
                $fechaFin = $date->format('Y-m-d');
            }
            
            $variacionesExistentesData[$key] = [
                'id_variacion' => $variacion->id_variacion,
                'vSKU' => $variacion->vSKU,
                'dPrecio' => $variacion->dPrecio,
                'dPrecio_descuento' => $variacion->dPrecio_descuento,
                'dPrecio_final' => $variacion->dPrecio_final,
                'bTiene_descuento' => $variacion->bTiene_descuento,
                'dFecha_inicio_descuento' => $fechaInicio,
                'dFecha_fin_descuento' => $fechaFin,
                'vMotivo_descuento' => $variacion->vMotivo_descuento,
                'iStock' => $variacion->iStock,
                'bActivo' => $variacion->bActivo,
                'dPeso' => $variacion->dPeso,
                'dLargo_cm' => $variacion->dLargo_cm,
                'dAncho_cm' => $variacion->dAncho_cm,
                'dAlto_cm' => $variacion->dAlto_cm,
                'vClase_envio' => $variacion->vClase_envio,
                'tDescripcion' => $variacion->tDescripcion,
                'id_impuesto' => $variacion->id_impuesto,
                'imagenes' => $imagenes,
                'imagen_principal_url' => $variacion->imagen_principal_url,
                'gif_url' => $variacion->gif_url,
                'imagenes_adicionales_urls' => $variacion->imagenes_adicionales_urls
            ];
        }
        
        $valoresSeleccionadosAttr = [];
        foreach($producto->valoresAtributos->groupBy('id_atributo') as $atributoId => $valores) {
            $atributo = $valores->first()->atributo;
            if($atributo) {
                $valoresSeleccionadosAttr[$atributoId] = [
                    'id' => $atributo->id_atributo,
                    'nombre' => $atributo->vNombre,
                    'valores' => $valores->mapWithKeys(function($valor) {
                        return [$valor->id_atributo_valor => $valor->vValor];
                    })->toArray()
                ];
            }
        }
        
        $imagenesActuales = $producto->imagenes ?? [];
        
        return view('productos.edit', compact(
            'producto',
            'categorias',
            'marcas',
            'etiquetas',
            'atributos',
            'impuestos',
            'productosExistentes',
            'variacionesExistentes',
            'variacionesExistentesData',
            'valoresSeleccionadosAttr',
            'imagenesActuales'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $contentLength = $request->server('CONTENT_LENGTH');
        $maxSize = 52 * 1024 * 1024;
        
        if ($contentLength > $maxSize) {
            Log::warning('Intento de subida de archivos demasiado grande en actualización: ' . $contentLength . ' bytes');
            return redirect()->back()
                ->withInput()
                ->with('post_max_size_error', true)
                ->with('error', 'El tamaño total de los archivos (' . round($contentLength / (1024 * 1024), 2) . 'MB) excede el límite permitido de 50MB.')
                ->with('swal_error', true);
        }

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
            'imagenes_adicionales_a_eliminar' => 'nullable|string',
            'eliminar_imagen_principal_producto' => 'nullable|in:0,1',
            'eliminar_gif_producto' => 'nullable|in:0,1',
            'atributos' => 'nullable|array',
            
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

        $validator->sometimes('dPrecio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_inicio_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        $validator->sometimes('dFecha_fin_descuento', 'required', function ($input) {
            return $input->bTiene_descuento == 1;
        });

        if ($request->has('variaciones')) {
            $validator->after(function ($validator) use ($request, $producto) {
                foreach ($request->variaciones as $key => $variacion) {
                    $sku = $variacion['vSKU'] ?? '';
                    if (!empty($sku)) {
                        $query = ProductoVariacion::where('vSKU', $sku);
                        if (isset($variacion['id_variacion'])) {
                            $query->where('id_variacion', '!=', $variacion['id_variacion']);
                        }
                        if ($query->exists()) {
                            $validator->errors()->add("variaciones.{$key}.vSKU", "El SKU '{$sku}' ya está registrado para otra variación.");
                        }
                    }
                    
                    if (isset($variacion['dPrecio']) && !empty($variacion['dPrecio'])) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $variacion['dPrecio'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio", 'El precio debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                    }
                    
                    if (isset($variacion['dPrecio_descuento']) && !empty($variacion['dPrecio_descuento'])) {
                        if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $variacion['dPrecio_descuento'])) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento debe tener máximo 7 dígitos enteros y 2 decimales.');
                        }
                        
                        if (($variacion['bTiene_descuento'] ?? 0) == 1 && isset($variacion['dPrecio']) && $variacion['dPrecio_descuento'] >= $variacion['dPrecio']) {
                            $validator->errors()->add("variaciones.{$key}.dPrecio_descuento", 'El precio de descuento debe ser menor que el precio normal.');
                        }
                    }
                    
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

            Log::info('=== INICIO ACTUALIZACIÓN PRODUCTO ID: ' . $producto->id_producto . ' ===');

            $imagenesActuales = $producto->getNumeroImagenes();
            $nuevasImagenes = $request->hasFile('imagenes') ? count($request->file('imagenes')) : 0;
            
            $imagenesAEliminar = [];
            if ($request->has('imagenes_adicionales_a_eliminar') && !empty($request->imagenes_adicionales_a_eliminar)) {
                $imagenesAEliminar = json_decode($request->imagenes_adicionales_a_eliminar, true);
                if (!is_array($imagenesAEliminar)) {
                    $imagenesAEliminar = [];
                }
            }
            
            $espacioDisponible = $imagenesActuales - count($imagenesAEliminar) + $nuevasImagenes;
            
            if ($espacioDisponible > 8) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['imagenes' => 'No puedes tener más de 8 imágenes. Actualmente tienes ' . $imagenesActuales . ' imágenes.'])
                    ->with('swal_error', true);
            }

            if (!empty($imagenesAEliminar) && is_array($imagenesAEliminar)) {
                try {
                    $producto->eliminarImagenesAdicionalesEspecificas($imagenesAEliminar);
                    Log::info('Imágenes adicionales eliminadas correctamente del producto ID: ' . $producto->id_producto);
                    $producto->refresh();
                } catch (\Exception $e) {
                    Log::error('ERROR en eliminarImagenesAdicionalesEspecificas: ' . $e->getMessage());
                    throw $e;
                }
            }

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
                
                'dPeso' => ($request->dPeso !== null && $request->dPeso !== '') ? floatval($request->dPeso) : null,
                'dLargo_cm' => ($request->dLargo_cm !== null && $request->dLargo_cm !== '') ? floatval($request->dLargo_cm) : null,
                'dAncho_cm' => ($request->dAncho_cm !== null && $request->dAncho_cm !== '') ? floatval($request->dAncho_cm) : null,
                'dAlto_cm' => ($request->dAlto_cm !== null && $request->dAlto_cm !== '') ? floatval($request->dAlto_cm) : null,
                
                'vClase_envio' => $request->vClase_envio ?: null,
                
                'bTiene_descuento' => $request->has('bTiene_descuento') && $request->bTiene_descuento == '1',
                'dPrecio_descuento' => $request->dPrecio_descuento ? floatval($request->dPrecio_descuento) : null,
                'vMotivo_descuento' => $request->vMotivo_descuento ?: null,
            ];

            if ($request->dFecha_inicio_descuento) {
                $updateData['dFecha_inicio_descuento'] = $request->dFecha_inicio_descuento . ' 00:00:00';
            } else {
                $updateData['dFecha_inicio_descuento'] = null;
            }
            
            if ($request->dFecha_fin_descuento) {
                $updateData['dFecha_fin_descuento'] = $request->dFecha_fin_descuento . ' 23:59:59';
            } else {
                $updateData['dFecha_fin_descuento'] = null;
            }

            $producto->update($updateData);

            if ($request->has('id_impuesto')) {
                if (!empty($request->id_impuesto)) {
                    $producto->impuestos()->sync([$request->id_impuesto]);
                } else {
                    $producto->impuestos()->sync([]);
                }
                $producto->recalcularPrecioFinal();
            }

            if ($request->has('eliminar_imagen_principal_producto') && $request->eliminar_imagen_principal_producto == '1') {
                $producto->eliminarImagenPrincipal();
                Log::info('Imagen principal eliminada del producto ID: ' . $producto->id_producto);
            }
            
            if ($request->hasFile('imagen_principal')) {
                $producto->guardarImagenPrincipal($request->file('imagen_principal'));
                Log::info('Nueva imagen principal guardada para producto ID: ' . $producto->id_producto);
            }

            if ($request->has('eliminar_gif_producto') && $request->eliminar_gif_producto == '1') {
                $producto->eliminarGif();
                Log::info('GIF eliminado del producto ID: ' . $producto->id_producto);
            }
            
            if ($request->hasFile('gif_producto')) {
                $producto->guardarGif($request->file('gif_producto'));
                Log::info('Nuevo GIF guardado para producto ID: ' . $producto->id_producto);
            }

            if ($request->hasFile('imagenes') && count($request->file('imagenes')) > 0) {
                $producto->guardarImagenesAdicionales($request->file('imagenes'));
                Log::info('Nuevas imágenes adicionales guardadas para producto ID: ' . $producto->id_producto);
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

                    // ========== CÁLCULO CORREGIDO CON VALIDACIÓN DE FECHAS ==========
                    $precioBaseVariacion = floatval($variacionData['dPrecio'] ?? $producto->dPrecio_venta);
                    $tieneDescuentoVariacion = isset($variacionData['bTiene_descuento']) && $variacionData['bTiene_descuento'] == '1';
                    $precioDescuentoVariacion = isset($variacionData['dPrecio_descuento']) ? floatval($variacionData['dPrecio_descuento']) : null;
                    
                    // Validar si el descuento está vigente según las fechas
                    $aplicaDescuento = false;
                    if ($tieneDescuentoVariacion && $precioDescuentoVariacion !== null && $precioDescuentoVariacion > 0 && $precioDescuentoVariacion < $precioBaseVariacion) {
                        $fechaActual = new \DateTime();
                        $fechaActual->setTime(0, 0, 0);
                        
                        $fechaInicio = null;
                        $fechaFin = null;
                        
                        if (!empty($variacionData['dFecha_inicio_descuento'])) {
                            $fechaInicio = new \DateTime($variacionData['dFecha_inicio_descuento']);
                            $fechaInicio->setTime(0, 0, 0);
                        }
                        
                        if (!empty($variacionData['dFecha_fin_descuento'])) {
                            $fechaFin = new \DateTime($variacionData['dFecha_fin_descuento']);
                            $fechaFin->setTime(23, 59, 59);
                        }
                        
                        if ($fechaInicio && $fechaFin) {
                            $aplicaDescuento = $fechaActual >= $fechaInicio && $fechaActual <= $fechaFin;
                        } else if ($fechaInicio && !$fechaFin) {
                            $aplicaDescuento = $fechaActual >= $fechaInicio;
                        } else if (!$fechaInicio && $fechaFin) {
                            $aplicaDescuento = $fechaActual <= $fechaFin;
                        } else {
                            $aplicaDescuento = true;
                        }
                    }
                    
                    // Usar el precio base que corresponda
                    $precioBaseParaImpuestos = $aplicaDescuento ? $precioDescuentoVariacion : $precioBaseVariacion;
                    
                    // Calcular impuestos sobre el precio base correcto
                    $totalImpuestosVariacion = 0;
                    $idImpuestoVariacion = $variacionData['id_impuesto'] ?? null;
                    
                    if ($idImpuestoVariacion) {
                        $impuestoVariacion = Impuesto::find($idImpuestoVariacion);
                        if ($impuestoVariacion && $impuestoVariacion->bActivo) {
                            $totalImpuestosVariacion = $precioBaseParaImpuestos * ($impuestoVariacion->dPorcentaje / 100);
                        }
                    } elseif ($request->id_impuesto) {
                        $impuestoPrincipal = Impuesto::find($request->id_impuesto);
                        if ($impuestoPrincipal && $impuestoPrincipal->bActivo) {
                            $totalImpuestosVariacion = $precioBaseParaImpuestos * ($impuestoPrincipal->dPorcentaje / 100);
                        }
                    }
                    
                    $precioFinalVariacion = $precioBaseParaImpuestos + $totalImpuestosVariacion;

                    if (isset($variacionData['id_variacion'])) {
                        $variacion = ProductoVariacion::find($variacionData['id_variacion']);
                        if ($variacion && $variacion->id_producto == $producto->id_producto) {
                            $variacionUpdateData = [
                                'vSKU' => strtoupper($variacionData['vSKU']),
                                'dPrecio' => $variacionData['dPrecio'] ?? $producto->dPrecio_venta,
                                'dPrecio_descuento' => $variacionData['dPrecio_descuento'] ?? null,
                                'dPrecio_final' => $precioFinalVariacion,
                                'vMotivo_descuento' => $variacionData['vMotivo_descuento'] ?? null,
                                'bTiene_descuento' => $tieneDescuentoVariacion ? 1 : 0,
                                'iStock' => $variacionData['iStock'] ?? 0,
                                
                                'dPeso' => (isset($variacionData['dPeso']) && $variacionData['dPeso'] !== '') ? floatval($variacionData['dPeso']) : null,
                                'dLargo_cm' => (isset($variacionData['dLargo_cm']) && $variacionData['dLargo_cm'] !== '') ? floatval($variacionData['dLargo_cm']) : null,
                                'dAncho_cm' => (isset($variacionData['dAncho_cm']) && $variacionData['dAncho_cm'] !== '') ? floatval($variacionData['dAncho_cm']) : null,
                                'dAlto_cm' => (isset($variacionData['dAlto_cm']) && $variacionData['dAlto_cm'] !== '') ? floatval($variacionData['dAlto_cm']) : null,
                                
                                'vClase_envio' => $claseEnvio,
                                'tDescripcion' => $variacionData['tDescripcion'] ?? null,
                                'bActivo' => isset($variacionData['bActivo']) ? 1 : 0,
                                'id_impuesto' => $idImpuestoVariacion,
                            ];

                            if (!empty($variacionData['dFecha_inicio_descuento'])) {
                                $variacionUpdateData['dFecha_inicio_descuento'] = $variacionData['dFecha_inicio_descuento'] . ' 00:00:00';
                            } else {
                                $variacionUpdateData['dFecha_inicio_descuento'] = null;
                            }
                            
                            if (!empty($variacionData['dFecha_fin_descuento'])) {
                                $variacionUpdateData['dFecha_fin_descuento'] = $variacionData['dFecha_fin_descuento'] . ' 23:59:59';
                            } else {
                                $variacionUpdateData['dFecha_fin_descuento'] = null;
                            }

                            $variacion->update($variacionUpdateData);
                            $idsVariacionesProcesadas[] = $variacion->id_variacion;

                            $variacion->atributos()->delete();
                            
                            if (isset($variacionData['id_atributo']) && isset($variacionData['id_atributo_valor'])) {
                                VariacionAtributo::create([
                                    'id_variacion' => $variacion->id_variacion,
                                    'id_atributo' => $variacionData['id_atributo'],
                                    'id_atributo_valor' => $variacionData['id_atributo_valor']
                                ]);
                            }

                            // Gestión de imágenes de variación
                            if (isset($variacionData['eliminar_imagen_principal']) && $variacionData['eliminar_imagen_principal'] == '1') {
                                $variacion->eliminarImagenPrincipal();
                                Log::info('Imagen principal eliminada de variación ID: ' . $variacion->id_variacion);
                            }
                            
                            if ($request->hasFile("variaciones.{$key}.imagen_principal")) {
                                $variacion->guardarImagenPrincipal($request->file("variaciones.{$key}.imagen_principal"));
                                Log::info('Nueva imagen principal guardada para variación ID: ' . $variacion->id_variacion);
                            }
                            
                            if (isset($variacionData['eliminar_gif']) && $variacionData['eliminar_gif'] == '1') {
                                $variacion->eliminarGif();
                                Log::info('GIF eliminado de variación ID: ' . $variacion->id_variacion);
                            }
                            
                            if ($request->hasFile("variaciones.{$key}.gif")) {
                                $variacion->guardarGif($request->file("variaciones.{$key}.gif"));
                                Log::info('Nuevo GIF guardado para variación ID: ' . $variacion->id_variacion);
                            }
                            
                            if (isset($variacionData['imagenes_a_eliminar']) && !empty($variacionData['imagenes_a_eliminar'])) {
                                $imagenesAEliminarVar = json_decode($variacionData['imagenes_a_eliminar'], true);
                                if (!empty($imagenesAEliminarVar)) {
                                    try {
                                        $variacion->eliminarImagenesAdicionalesEspecificas($imagenesAEliminarVar);
                                        Log::info('Imágenes adicionales eliminadas de variación ID: ' . $variacion->id_variacion);
                                    } catch (\Exception $e) {
                                        Log::error('ERROR al eliminar imágenes adicionales de variación: ' . $e->getMessage());
                                        throw $e;
                                    }
                                }
                            }
                            
                            if ($request->hasFile("variaciones.{$key}.imagenes_adicionales")) {
                                $imagenes = $request->file("variaciones.{$key}.imagenes_adicionales");
                                if (!empty($imagenes)) {
                                    if (!is_array($imagenes)) {
                                        $imagenes = [$imagenes];
                                    }
                                    $imagenesValidas = array_filter($imagenes, function($file) {
                                        return $file && $file->isValid();
                                    });
                                    if (!empty($imagenesValidas)) {
                                        $variacion->guardarImagenesAdicionales($imagenesValidas);
                                        Log::info('Nuevas imágenes adicionales guardadas para variación ID: ' . $variacion->id_variacion);
                                    }
                                }
                            }
                        }
                    } else {
                        // Crear nueva variación
                        $nuevaVariacionData = [
                            'id_producto' => $producto->id_producto,
                            'vSKU' => strtoupper($variacionData['vSKU']),
                            'dPrecio' => $variacionData['dPrecio'] ?? $producto->dPrecio_venta,
                            'dPrecio_descuento' => $variacionData['dPrecio_descuento'] ?? null,
                            'dPrecio_final' => $precioFinalVariacion,
                            'vMotivo_descuento' => $variacionData['vMotivo_descuento'] ?? null,
                            'bTiene_descuento' => $tieneDescuentoVariacion ? 1 : 0,
                            'iStock' => $variacionData['iStock'] ?? 0,
                            
                            'dPeso' => (isset($variacionData['dPeso']) && $variacionData['dPeso'] !== '') ? floatval($variacionData['dPeso']) : null,
                            'dLargo_cm' => (isset($variacionData['dLargo_cm']) && $variacionData['dLargo_cm'] !== '') ? floatval($variacionData['dLargo_cm']) : null,
                            'dAncho_cm' => (isset($variacionData['dAncho_cm']) && $variacionData['dAncho_cm'] !== '') ? floatval($variacionData['dAncho_cm']) : null,
                            'dAlto_cm' => (isset($variacionData['dAlto_cm']) && $variacionData['dAlto_cm'] !== '') ? floatval($variacionData['dAlto_cm']) : null,
                            
                            'vClase_envio' => $claseEnvio,
                            'tDescripcion' => $variacionData['tDescripcion'] ?? null,
                            'bActivo' => isset($variacionData['bActivo']) ? 1 : 0,
                            'id_impuesto' => $idImpuestoVariacion,
                        ];

                        if (!empty($variacionData['dFecha_inicio_descuento'])) {
                            $nuevaVariacionData['dFecha_inicio_descuento'] = $variacionData['dFecha_inicio_descuento'] . ' 00:00:00';
                        }
                        if (!empty($variacionData['dFecha_fin_descuento'])) {
                            $nuevaVariacionData['dFecha_fin_descuento'] = $variacionData['dFecha_fin_descuento'] . ' 23:59:59';
                        }

                        $variacion = ProductoVariacion::create($nuevaVariacionData);
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
                            if (!empty($imagenes)) {
                                if (!is_array($imagenes)) {
                                    $imagenes = [$imagenes];
                                }
                                $imagenesValidas = array_filter($imagenes, function($file) {
                                    return $file && $file->isValid();
                                });
                                if (!empty($imagenesValidas)) {
                                    $variacion->guardarImagenesAdicionales($imagenesValidas);
                                }
                            }
                        }
                    }
                }

                $variacionesAEliminar = array_diff($idsVariacionesExistentes, $idsVariacionesProcesadas);
                foreach ($variacionesAEliminar as $idVariacion) {
                    $variacion = ProductoVariacion::find($idVariacion);
                    if ($variacion) {
                        $variacion->eliminarTodasLasImagenes();
                        $variacion->atributos()->delete();
                        $variacion->delete();
                        Log::info('Variación eliminada ID: ' . $idVariacion);
                    }
                }
            }

            DB::commit();

            Log::info('=== FIN ACTUALIZACIÓN PRODUCTO ID: ' . $producto->id_producto . ' ===');

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

            foreach ($producto->variaciones as $variacion) {
                $variacion->eliminarTodasLasImagenes();
                $variacion->atributos()->delete();
            }

            $producto->variaciones()->delete();

            $producto->eliminarTodasLasImagenes();

            $producto->etiquetas()->detach();
            $producto->impuestos()->detach();
            DB::table('tbl_producto_atributos')->where('id_producto', $producto->id_producto)->delete();

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
        $productos = Producto::with(['marca', 'categoria', 'etiquetas', 'impuestos', 'variaciones' => function($query) {
            $query->where('bActivo', true);
        }])
        ->where('bActivo', true)
        ->orderBy('vNombre')
        ->get();
        
        return view('productos.catalogo', compact('productos'));
    }

    // ============ FUNCIONES PARA ATRIBUTOS ============

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
            $query->where('bActivo', true);
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

    // ============ MÉTODOS API PARA VERIFICACIÓN EN TIEMPO REAL ============

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