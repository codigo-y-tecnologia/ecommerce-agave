<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoVariacion;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class BusquedaController extends Controller
{
    // Método para la página de inicio
    public function inicio()
    {
        // 1. Productos destacados (los más recientes) - TODOS los productos activos
        $productosDestacados = Producto::with([
                'categoria', 
                'marca', 
                'etiquetas', 
                'variaciones' => function($query) {
                    $query->with(['atributos.atributo', 'atributos.valor'])
                          ->where('bActivo', true);
                }
            ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(12)
            ->get();
        
        // 2. Productos en descuento (incluye productos y variaciones)
        $productosDescuento = $this->obtenerProductosConDescuento(12);
        
        // 3. Productos más vendidos (simulado)
        $productosMasVendidos = Producto::with([
                'categoria', 
                'marca', 
                'etiquetas', 
                'variaciones' => function($query) {
                    $query->with(['atributos.atributo', 'atributos.valor'])
                          ->where('bActivo', true);
                }
            ])
            ->where('bActivo', true)
            ->where('iStock', '>', 0)
            ->inRandomOrder()
            ->take(8)
            ->get();
        
        // 4. TODOS los productos para mostrar en la sección principal
        $todosLosProductos = Producto::with([
                'categoria', 
                'marca', 
                'etiquetas', 
                'variaciones' => function($query) {
                    $query->with(['atributos.atributo', 'atributos.valor'])
                          ->where('bActivo', true);
                }
            ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->paginate(12); // Paginación de 12 productos por página
        
        // Depuración: Ver cuántos productos se están cargando
        \Log::info('Productos destacados: ' . $productosDestacados->count());
        \Log::info('Productos en descuento: ' . $productosDescuento->count());
        \Log::info('Productos más vendidos: ' . $productosMasVendidos->count());
        \Log::info('Total productos en catálogo: ' . $todosLosProductos->total());
        
        return view('inicio', compact(
            'productosDestacados', 
            'productosDescuento', 
            'productosMasVendidos',
            'todosLosProductos'
        ));
    }

    /**
     * Obtiene productos y variaciones con descuento
     */
    private function obtenerProductosConDescuento($limite = null)
    {
        // Colección para almacenar todos los items con descuento
        $itemsConDescuento = collect();
        
        // 1. Obtener productos con descuento activo
        $productosConDescuento = Producto::with([
                'categoria', 
                'marca', 
                'etiquetas',
                'variaciones' => function($query) {
                    $query->with(['atributos.atributo', 'atributos.valor']);
                }
            ])
            ->where('bActivo', true)
            ->where('bTiene_oferta', 1)
            ->where(function($query) {
                $query->whereNull('dFecha_fin_oferta')
                      ->orWhere('dFecha_fin_oferta', '>=', now());
            })
            ->get();
        
        foreach ($productosConDescuento as $producto) {
            // Verificar que realmente tenga descuento activo
            if ($this->tieneDescuentoActivoProducto($producto)) {
                $itemsConDescuento->push($producto);
            }
        }
        
        // 2. Obtener variaciones con descuento activo
        $variacionesConDescuento = ProductoVariacion::with([
                'productoPadre',
                'productoPadre.categoria',
                'productoPadre.marca',
                'productoPadre.etiquetas',
                'atributos.atributo',
                'atributos.valor'
            ])
            ->where('bActivo', true)
            ->where('bTiene_oferta', 1)
            ->where(function($query) {
                $query->whereNull('dFecha_fin_oferta')
                      ->orWhere('dFecha_fin_oferta', '>=', now());
            })
            ->get();
        
        foreach ($variacionesConDescuento as $variacion) {
            // Verificar que realmente tenga descuento activo
            if ($this->tieneDescuentoActivoVariacion($variacion)) {
                $itemsConDescuento->push($variacion);
            }
        }
        
        // 3. Ordenar por porcentaje de descuento (mayor a menor)
        $itemsConDescuento = $itemsConDescuento->sortByDesc(function($item) {
            if (isset($item->id_variacion)) {
                // Es una variación
                if ($item->dPrecio > 0 && $item->dPrecio_oferta) {
                    return (($item->dPrecio - $item->dPrecio_oferta) / $item->dPrecio) * 100;
                }
            } else {
                // Es un producto
                if ($item->dPrecio_venta > 0 && $item->dPrecio_oferta) {
                    return (($item->dPrecio_venta - $item->dPrecio_oferta) / $item->dPrecio_venta) * 100;
                }
            }
            return 0;
        })->values();
        
        // Aplicar límite si se especifica
        if ($limite) {
            $itemsConDescuento = $itemsConDescuento->take($limite);
        }
        
        return $itemsConDescuento;
    }

    /**
     * Verifica si un producto tiene descuento activo
     */
    private function tieneDescuentoActivoProducto($producto)
    {
        // Si no tiene descuento activado o no tiene precio de descuento
        if (!$producto->bTiene_oferta || $producto->dPrecio_oferta === null) {
            return false;
        }

        $fechaActual = now()->toDateString();

        // Caso 1: Tiene ambas fechas definidas
        if ($producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta) {
            return $fechaActual >= $producto->dFecha_inicio_oferta && 
                   $fechaActual <= $producto->dFecha_fin_oferta;
        }

        // Caso 2: Solo tiene fecha de inicio
        if ($producto->dFecha_inicio_oferta && !$producto->dFecha_fin_oferta) {
            return $fechaActual >= $producto->dFecha_inicio_oferta;
        }

        // Caso 3: Solo tiene fecha de fin
        if (!$producto->dFecha_inicio_oferta && $producto->dFecha_fin_oferta) {
            return $fechaActual <= $producto->dFecha_fin_oferta;
        }

        // Caso 4: Tiene descuento activado pero sin fechas
        return true;
    }

    /**
     * Verifica si una variación tiene descuento activo
     */
    private function tieneDescuentoActivoVariacion($variacion)
    {
        // Si no tiene descuento activado o no tiene precio de descuento
        if (!$variacion->bTiene_oferta || $variacion->dPrecio_oferta === null) {
            return false;
        }

        $fechaActual = now()->toDateString();

        // Caso 1: Tiene ambas fechas definidas
        if ($variacion->dFecha_inicio_oferta && $variacion->dFecha_fin_oferta) {
            return $fechaActual >= $variacion->dFecha_inicio_oferta && 
                   $fechaActual <= $variacion->dFecha_fin_oferta;
        }

        // Caso 2: Solo tiene fecha de inicio
        if ($variacion->dFecha_inicio_oferta && !$variacion->dFecha_fin_oferta) {
            return $fechaActual >= $variacion->dFecha_inicio_oferta;
        }

        // Caso 3: Solo tiene fecha de fin
        if (!$variacion->dFecha_inicio_oferta && $variacion->dFecha_fin_oferta) {
            return $fechaActual <= $variacion->dFecha_fin_oferta;
        }

        // Caso 4: Tiene descuento activado pero sin fechas
        return true;
    }

    public function buscar(Request $request)
    {
        // Verificar si es el filtro de descuento
        $filtroDescuento = $request->has('en_descuento') && $request->en_descuento == '1';
        
        // Obtener TODOS los productos activos
        $productosQuery = Producto::with(['categoria', 'marca', 'etiquetas', 'variaciones'])
            ->where('bActivo', true);

        // Aplicar filtros
        $this->aplicarFiltros($productosQuery, $request);
        
        // Obtener los IDs de los productos que cumplen los criterios
        $productosIds = $productosQuery->pluck('id_producto')->toArray();
        
        // Construir colección combinada de productos y variaciones
        $resultados = collect();
        
        // 1. Obtener todos los productos que cumplen los filtros
        $productosPadre = Producto::with([
                'categoria', 
                'marca', 
                'etiquetas', 
                'variaciones' => function($query) {
                    $query->where('bActivo', true);
                },
                'variaciones.atributos.atributo',
                'variaciones.atributos.valor'
            ])
            ->whereIn('id_producto', $productosIds)
            ->get();
        
        // 2. Procesar cada producto y sus variaciones
        foreach ($productosPadre as $producto) {
            
            // CASO 1: Estamos en filtro de descuento - SOLO mostrar items con descuento
            if ($filtroDescuento) {
                $itemsAgregados = false;
                
                // Verificar variaciones con descuento
                foreach ($producto->variacionesActivas as $variacion) {
                    if ($this->tieneDescuentoActivoVariacion($variacion)) {
                        if ($request->con_stock != '1' || $variacion->iStock > 0) {
                            $resultados->push($variacion);
                            $itemsAgregados = true;
                        }
                    }
                }
                
                // Verificar producto padre con descuento
                if (!$itemsAgregados && $this->tieneDescuentoActivoProducto($producto)) {
                    if ($request->con_stock != '1' || $producto->iStock > 0) {
                        $resultados->push($producto);
                    }
                }
            } 
            // CASO 2: NO estamos en filtro de descuento - MOSTRAR TODO
            else {
                // Agregar TODAS las variaciones activas
                if ($producto->variacionesActivas && $producto->variacionesActivas->count() > 0) {
                    foreach ($producto->variacionesActivas as $variacion) {
                        if ($this->variacionCumpleFiltrosBasicos($variacion, $request)) {
                            if ($request->con_stock != '1' || $variacion->iStock > 0) {
                                $resultados->push($variacion);
                            }
                        }
                    }
                }
                
                // Agregar el producto padre
                if ($this->productoCumpleFiltrosBasicos($producto, $request)) {
                    if ($request->con_stock != '1' || $producto->iStock > 0) {
                        $resultados->push($producto);
                    }
                }
            }
        }
        
        // Eliminar duplicados
        $resultados = $resultados->unique(function($item) {
            if (isset($item->id_variacion)) {
                return 'var_' . $item->id_variacion;
            }
            return 'prod_' . $item->id_producto;
        });
        
        // Aplicar ordenamiento
        $resultados = $this->aplicarOrdenamiento($resultados, $request->get('orden', 'nombre'));
        
        // Paginación manual
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $pagedData = $resultados->forPage($currentPage, $perPage);
        
        $productos = new LengthAwarePaginator(
            $pagedData->values(),
            $resultados->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categorias = Categoria::where('bActivo', true)->get();
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();

        return view('busqueda.resultados', compact(
            'productos', 
            'categorias', 
            'marcas', 
            'etiquetas'
        ));
    }
    
    /**
     * Aplica los filtros de búsqueda al query de productos
     */
    private function aplicarFiltros($query, $request)
    {
        // Búsqueda por palabras clave
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            
            $query->where(function($q) use ($searchTerm) {
                $q->where('vNombre', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('tDescripcion_corta', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('tDescripcion_larga', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('categoria', function($catQuery) use ($searchTerm) {
                      $catQuery->where('tbl_categorias.vNombre', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('marca', function($brandQuery) use ($searchTerm) {
                      $brandQuery->where('tbl_marcas.vNombre', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('etiquetas', function($tagQuery) use ($searchTerm) {
                      $tagQuery->where('tbl_etiquetas.vNombre', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Filtro de Categorías
        if ($request->has('categorias') && !empty($request->categorias)) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->whereIn('tbl_categorias.id_categoria', $request->categorias);
            });
        }

        // Filtro de Marcas
        if ($request->has('marcas') && !empty($request->marcas)) {
            $query->whereHas('marca', function($q) use ($request) {
                $q->whereIn('tbl_marcas.id_marca', $request->marcas);
            });
        }

        // Filtro por etiquetas
        if ($request->has('etiquetas') && !empty($request->etiquetas)) {
            $query->whereHas('etiquetas', function($q) use ($request) {
                $q->whereIn('tbl_etiquetas.id_etiqueta', $request->etiquetas);
            });
        }

        // Filtro por precio
        if ($request->has('precio_min') && !empty($request->precio_min)) {
            $query->where('dPrecio_venta', '>=', $request->precio_min);
        }

        if ($request->has('precio_max') && !empty($request->precio_max)) {
            $query->where('dPrecio_venta', '<=', $request->precio_max);
        }
    }
    
    /**
     * Verifica si un producto cumple con los filtros básicos
     */
    private function productoCumpleFiltrosBasicos($producto, $request)
    {
        // Filtro de precio
        if ($request->has('precio_min') && !empty($request->precio_min)) {
            $precioMin = floatval($request->precio_min);
            $precioActual = $this->tieneDescuentoActivoProducto($producto) ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
            if ($precioActual < $precioMin) {
                return false;
            }
        }
        
        if ($request->has('precio_max') && !empty($request->precio_max)) {
            $precioMax = floatval($request->precio_max);
            $precioActual = $this->tieneDescuentoActivoProducto($producto) ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
            if ($precioActual > $precioMax) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Verifica si una variación cumple con los filtros básicos
     */
    private function variacionCumpleFiltrosBasicos($variacion, $request)
    {
        // Verificar que la variación tenga producto padre
        if (!$variacion->productoPadre) {
            return false;
        }
        
        // Filtro de búsqueda por texto
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = strtolower($request->q);
            $nombreProducto = strtolower($variacion->productoPadre->vNombre ?? '');
            $atributosTexto = strtolower($variacion->getAtributosTexto() ?? '');
            
            if (strpos($nombreProducto, $searchTerm) === false && 
                strpos($atributosTexto, $searchTerm) === false) {
                return false;
            }
        }
        
        // Filtro de precio
        $precioActual = $this->tieneDescuentoActivoVariacion($variacion) ? $variacion->dPrecio_oferta : $variacion->dPrecio;
        
        if ($request->has('precio_min') && !empty($request->precio_min)) {
            if ($precioActual < floatval($request->precio_min)) {
                return false;
            }
        }
        
        if ($request->has('precio_max') && !empty($request->precio_max)) {
            if ($precioActual > floatval($request->precio_max)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Aplica ordenamiento a la colección de resultados
     */
    private function aplicarOrdenamiento($coleccion, $orden)
    {
        switch ($orden) {
            case 'precio_asc':
                return $coleccion->sortBy(function($item) {
                    if (isset($item->id_variacion)) {
                        return $this->tieneDescuentoActivoVariacion($item) ? $item->dPrecio_oferta : $item->dPrecio;
                    } else {
                        return $this->tieneDescuentoActivoProducto($item) ? $item->dPrecio_oferta : $item->dPrecio_venta;
                    }
                })->values();
                
            case 'precio_desc':
                return $coleccion->sortByDesc(function($item) {
                    if (isset($item->id_variacion)) {
                        return $this->tieneDescuentoActivoVariacion($item) ? $item->dPrecio_oferta : $item->dPrecio;
                    } else {
                        return $this->tieneDescuentoActivoProducto($item) ? $item->dPrecio_oferta : $item->dPrecio_venta;
                    }
                })->values();
                
            case 'recientes':
                return $coleccion->sortByDesc(function($item) {
                    if (isset($item->id_variacion)) {
                        return $item->tFecha_registro ?? ($item->productoPadre ? $item->productoPadre->tFecha_registro : now());
                    } else {
                        return $item->tFecha_registro ?? now();
                    }
                })->values();
                
            case 'descuento_mayor':
                return $coleccion->sortByDesc(function($item) {
                    if (isset($item->id_variacion)) {
                        if ($this->tieneDescuentoActivoVariacion($item) && $item->dPrecio > 0) {
                            return (($item->dPrecio - $item->dPrecio_oferta) / $item->dPrecio) * 100;
                        }
                    } else {
                        if ($this->tieneDescuentoActivoProducto($item) && $item->dPrecio_venta > 0) {
                            return (($item->dPrecio_venta - $item->dPrecio_oferta) / $item->dPrecio_venta) * 100;
                        }
                    }
                    return 0;
                })->values();
                
            case 'nombre':
            default:
                return $coleccion->sortBy(function($item) {
                    if (isset($item->id_variacion)) {
                        if ($item->productoPadre) {
                            return $item->productoPadre->vNombre . ' - ' . $item->getAtributosTexto();
                        } else {
                            return 'Variación - ' . ($item->vSKU ?? $item->id_variacion);
                        }
                    } else {
                        return $item->vNombre ?? 'Producto sin nombre';
                    }
                })->values();
        }
    }

    public function busquedaRapida(Request $request)
    {
        $term = $request->get('q');
        
        if (!$term || strlen($term) < 2) {
            return response()->json([]);
        }

        $sugerencias = [];

        // Buscar en productos
        $productos = Producto::with(['categoria', 'marca'])
            ->where('bActivo', true)
            ->where(function($q) use ($term) {
                $q->where('vNombre', 'LIKE', "%{$term}%")
                  ->orWhere('tDescripcion_corta', 'LIKE', "%{$term}%")
                  ->orWhereHas('categoria', function($catQuery) use ($term) {
                      $catQuery->where('tbl_categorias.vNombre', 'LIKE', "%{$term}%");
                  })
                  ->orWhereHas('marca', function($brandQuery) use ($term) {
                      $brandQuery->where('tbl_marcas.vNombre', 'LIKE', "%{$term}%");
                  });
            })
            ->take(5)
            ->get();
            
        foreach ($productos as $producto) {
            $tieneDescuento = $this->tieneDescuentoActivoProducto($producto);
            $precioMostrar = $tieneDescuento ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
            $texto = $producto->vNombre . ' - $' . number_format($precioMostrar, 2);
            
            if ($tieneDescuento) {
                $texto .= ' (¡DESCUENTO!)';
            }
            
            $sugerencias[] = [
                'id' => $producto->id_producto,
                'text' => $texto,
                'url' => route('productos.show.public', $producto->id_producto),
                'en_descuento' => $tieneDescuento,
                'tipo' => 'producto'
            ];
        }
        
        // Buscar en variaciones
        $variaciones = ProductoVariacion::with(['productoPadre', 'atributos.atributo', 'atributos.valor'])
            ->whereHas('productoPadre', function($q) use ($term) {
                $q->where('bActivo', true);
            })
            ->where(function($q) use ($term) {
                $q->whereHas('productoPadre', function($subQ) use ($term) {
                    $subQ->where('vNombre', 'LIKE', "%{$term}%");
                })->orWhereHas('atributos.valor', function($attrQ) use ($term) {
                    $attrQ->where('vValor', 'LIKE', "%{$term}%");
                });
            })
            ->take(5)
            ->get();

        foreach ($variaciones as $variacion) {
            if (!$variacion->productoPadre) {
                continue;
            }
            
            $tieneDescuento = $this->tieneDescuentoActivoVariacion($variacion);
            $precioMostrar = $tieneDescuento ? $variacion->dPrecio_oferta : $variacion->dPrecio;
            $texto = $variacion->productoPadre->vNombre . ' - ' . $variacion->getAtributosTexto() . ' - $' . number_format($precioMostrar, 2);
            
            if ($tieneDescuento) {
                $texto .= ' (¡DESCUENTO!)';
            }
            
            $sugerencias[] = [
                'id' => $variacion->id_variacion,
                'text' => $texto,
                'url' => route('productos.show.public', [$variacion->productoPadre->id_producto, 'variacion' => $variacion->id_variacion]),
                'en_descuento' => $tieneDescuento,
                'tipo' => 'variacion'
            ];
        }

        $sugerencias = collect($sugerencias)->sortByDesc('en_descuento')->take(10)->values()->toArray();

        return response()->json($sugerencias);
    }

    public function buscarProductos(Request $request)
    {
        $term = $request->get('term');
        
        if (!$term || strlen($term) < 2) {
            return response()->json([]);
        }

        $productos = Producto::where('bActivo', true)
            ->where('vNombre', 'LIKE', "%{$term}%")
            ->orWhere('tDescripcion_corta', 'LIKE', "%{$term}%")
            ->take(10)
            ->get(['id_producto', 'vNombre', 'dPrecio_venta', 'dPrecio_oferta', 'bTiene_oferta']);

        return response()->json($productos);
    }
}