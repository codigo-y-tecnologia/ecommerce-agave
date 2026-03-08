<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Variacion;
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
        // Productos destacados (los más recientes)
        $productos = Producto::with(['categoria', 'marca', 'etiquetas', 'variaciones'])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(12)
            ->get();
        
        // Productos en descuento (incluyendo variaciones)
        $productosDescuento = $this->obtenerProductosConDescuento(8);
        
        // Productos más vendidos (simulado)
        $productosMasVendidos = Producto::with(['categoria', 'marca', 'etiquetas', 'variaciones'])
            ->where('bActivo', true)
            ->where('iStock', '>', 0)
            ->inRandomOrder()
            ->take(8)
            ->get();
        
        return view('inicio', compact('productos', 'productosDescuento', 'productosMasVendidos'));
    }

    /**
     * Obtiene productos y variaciones con descuento
     */
    private function obtenerProductosConDescuento($limite = null)
    {
        // Obtener productos con descuento
        $productosConDescuento = Producto::with(['categoria', 'marca', 'etiquetas', 'variaciones'])
            ->where('bActivo', true)
            ->where(function($query) {
                $query->where(function($q) {
                    // Productos con oferta activa
                    $q->where('bTiene_oferta', 1)
                      ->where(function($dateQuery) {
                          $dateQuery->whereNull('dFecha_fin_oferta')
                                    ->orWhere('dFecha_fin_oferta', '>=', now());
                      });
                })->orWhereHas('variaciones', function($q) {
                    // Productos que tienen variaciones con oferta activa
                    $q->where('bTiene_oferta', 1)
                      ->where(function($dateQuery) {
                          $dateQuery->whereNull('dFecha_fin_oferta')
                                    ->orWhere('dFecha_fin_oferta', '>=', now());
                      });
                });
            })
            ->orderByRaw('((dPrecio_venta - dPrecio_oferta) / dPrecio_venta) DESC');
            
        if ($limite) {
            $productosConDescuento = $productosConDescuento->take($limite);
        }
        
        return $productosConDescuento->get();
    }

    public function buscar(Request $request)
    {
        // Obtener IDs de productos que cumplen los criterios
        $productosQuery = Producto::with(['categoria', 'marca', 'etiquetas', 'variaciones'])
            ->where('bActivo', true);

        // Aplicar filtros al query de productos
        $this->aplicarFiltros($productosQuery, $request);
        
        // Obtener los IDs de los productos que cumplen los criterios
        $productosIds = $productosQuery->pluck('id_producto')->toArray();
        
        // Construir colección combinada de productos y variaciones
        $resultados = collect();
        
        // 1. Agregar productos padres (si cumplen filtros y tienen stock > 0 o se permite sin stock)
        $productosPadre = Producto::with(['categoria', 'marca', 'etiquetas', 'variaciones'])
            ->whereIn('id_producto', $productosIds)
            ->get();
            
        foreach ($productosPadre as $producto) {
            // Agregar el producto padre si cumple con el filtro de stock
            if ($request->con_stock != '1' || $producto->iStock > 0) {
                $resultados->push($producto);
            }
            
            // Agregar variaciones activas
            if ($producto->variacionesActivas) {
                foreach ($producto->variacionesActivas as $variacion) {
                    // Verificar si la variación cumple con los filtros
                    if ($this->variacionCumpleFiltros($variacion, $request)) {
                        // Si hay filtro de stock, verificar que la variación tenga stock
                        if ($request->con_stock != '1' || $variacion->iStock > 0) {
                            $resultados->push($variacion);
                        }
                    }
                }
            }
        }
        
        // Aplicar ordenamiento a la colección combinada
        $resultados = $this->aplicarOrdenamiento($resultados, $request->get('orden', 'nombre'));
        
        // Paginación manual
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $pagedData = $resultados->forPage($currentPage, $perPage);
        
        // Crear paginador personalizado
        $productos = new LengthAwarePaginator(
            $pagedData->values(),
            $resultados->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categorias = Categoria::all();
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

        // FILTRO DE BÚSQUEDA POR DESCUENTO
        if ($request->has('en_descuento') && $request->en_descuento == '1') {
            $query->where(function($q) {
                $q->where(function($subQ) {
                    // Productos con oferta activa
                    $subQ->where('bTiene_oferta', 1)
                         ->where(function($dateQuery) {
                             $dateQuery->whereNull('dFecha_fin_oferta')
                                       ->orWhere('dFecha_fin_oferta', '>=', now());
                         });
                })->orWhereHas('variaciones', function($varQ) {
                    // Productos que tienen variaciones con oferta activa
                    $varQ->where('bTiene_oferta', 1)
                         ->where(function($dateQuery) {
                             $dateQuery->whereNull('dFecha_fin_oferta')
                                       ->orWhere('dFecha_fin_oferta', '>=', now());
                         });
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

        // Filtro por precio (solo para productos padres, las variaciones se filtran después)
        if ($request->has('precio_min') && !empty($request->precio_min)) {
            $query->where('dPrecio_venta', '>=', $request->precio_min);
        }

        if ($request->has('precio_max') && !empty($request->precio_max)) {
            $query->where('dPrecio_venta', '<=', $request->precio_max);
        }
    }
    
    /**
     * Verifica si una variación cumple con los filtros aplicados
     */
    private function variacionCumpleFiltros($variacion, $request)
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
        
        // Filtro de descuento
        if ($request->has('en_descuento') && $request->en_descuento == '1') {
            if (!$variacion->ofertaVigente()) {
                return false;
            }
        }
        
        // Filtro de categoría (hereda del producto padre)
        if ($request->has('categorias') && !empty($request->categorias)) {
            if (!in_array($variacion->productoPadre->id_categoria, $request->categorias)) {
                return false;
            }
        }
        
        // Filtro de marca (hereda del producto padre)
        if ($request->has('marcas') && !empty($request->marcas)) {
            if (!in_array($variacion->productoPadre->id_marca, $request->marcas)) {
                return false;
            }
        }
        
        // Filtro de precio (para la variación)
        $precioActual = $variacion->ofertaVigente() ? $variacion->dPrecio_oferta : $variacion->dPrecio;
        
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
                        return $item->ofertaVigente() ? $item->dPrecio_oferta : $item->dPrecio;
                    } else {
                        return $item->tieneDescuentoActivo() ? $item->dPrecio_oferta : $item->dPrecio_venta;
                    }
                })->values();
                
            case 'precio_desc':
                return $coleccion->sortByDesc(function($item) {
                    if (isset($item->id_variacion)) {
                        return $item->ofertaVigente() ? $item->dPrecio_oferta : $item->dPrecio;
                    } else {
                        return $item->tieneDescuentoActivo() ? $item->dPrecio_oferta : $item->dPrecio_venta;
                    }
                })->values();
                
            case 'recientes':
                return $coleccion->sortByDesc(function($item) {
                    if (isset($item->id_variacion)) {
                        return $item->created_at ?? ($item->productoPadre ? $item->productoPadre->created_at : now());
                    } else {
                        return $item->created_at ?? now();
                    }
                })->values();
                
            case 'descuento_mayor':
                return $coleccion->sortByDesc(function($item) {
                    if (isset($item->id_variacion)) {
                        if ($item->ofertaVigente() && $item->dPrecio > 0) {
                            return (($item->dPrecio - $item->dPrecio_oferta) / $item->dPrecio) * 100;
                        }
                    } else {
                        if ($item->tieneDescuentoActivo() && $item->dPrecio_venta > 0) {
                            return (($item->dPrecio_venta - $item->dPrecio_oferta) / $item->dPrecio_venta) * 100;
                        }
                    }
                    return 0;
                })->values();
                
            case 'nombre':
            default:
                return $coleccion->sortBy(function($item) {
                    if (isset($item->id_variacion)) {
                        // Verificar que exista el producto padre
                        if ($item->productoPadre) {
                            return $item->productoPadre->vNombre . ' - ' . $item->getAtributosTexto();
                        } else {
                            // Si no hay producto padre, usar el SKU o ID
                            return 'Variación sin padre - ' . ($item->vSKU ?? $item->id_variacion);
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
            $tieneDescuento = $producto->tieneDescuentoActivo();
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
        $variaciones = Variacion::with(['productoPadre', 'atributos.atributo', 'atributos.valor'])
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
            // Verificar que tenga producto padre
            if (!$variacion->productoPadre) {
                continue;
            }
            
            $tieneDescuento = $variacion->ofertaVigente();
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

        // Limitar a 10 resultados totales y ordenar
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