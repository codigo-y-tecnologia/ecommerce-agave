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
    public function inicio()
    {
        $productosDestacados = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(12)
            ->get();

        $productosDescuento = $this->obtenerItemsConDescuento(12);

        $productosRecomendados = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(8)
            ->get();

        $todosLosItems = $this->obtenerTodosLosItems();

        return view('inicio', compact(
            'productosDestacados',
            'productosDescuento',
            'productosRecomendados',
            'todosLosItems'
        ));
    }

    private function obtenerTodosLosItems()
    {
        $items = collect();

        $productos = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->get();

        foreach ($productos as $producto) {
            $productoClone = clone $producto;
            $productoClone->tipo_item = 'producto';
            $productoClone->item_id = $producto->id_producto;
            $items->push($productoClone);

            $variaciones = ProductoVariacion::with([
                'productoPadre',
                'productoPadre.categoria',
                'productoPadre.marca',
                'productoPadre.etiquetas',
                'atributos.atributo',
                'atributos.valor',
                'imagenesRegistradas'
            ])
                ->where('id_producto', $producto->id_producto)
                ->where('bActivo', true)
                ->get();

            foreach ($variaciones as $variacion) {
                $variacion->tipo_item = 'variacion';
                $variacion->item_id = $variacion->id_variacion;
                $items->push($variacion);
            }
        }

        $items = $items->sortByDesc(function ($item) {
            if ($item->tipo_item === 'variacion') {
                return $item->tFecha_registro ?? ($item->productoPadre ? $item->productoPadre->tFecha_registro : now());
            }
            return $item->tFecha_registro ?? now();
        })->values();

        $perPage = 12;
        $currentPage = request()->get('page', 1);
        $pagedData = $items->forPage($currentPage, $perPage);

        return new LengthAwarePaginator(
            $pagedData->values(),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    private function obtenerItemsConDescuento($limite = null)
    {
        $fechaActual = now()->toDateString();
        $itemsConDescuento = collect();

        $productosConDescuento = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->where('bTiene_descuento', 1)
            ->where('dPrecio_descuento', '>', 0)
            ->where(function ($query) use ($fechaActual) {
                $query->where(function ($q) use ($fechaActual) {
                    $q->whereNull('dFecha_fin_descuento')
                        ->orWhere('dFecha_fin_descuento', '>=', $fechaActual);
                });
            })
            ->where(function ($query) use ($fechaActual) {
                $query->where(function ($q) use ($fechaActual) {
                    $q->whereNull('dFecha_inicio_descuento')
                        ->orWhere('dFecha_inicio_descuento', '<=', $fechaActual);
                });
            })
            ->get();

        foreach ($productosConDescuento as $producto) {
            if ($this->tieneDescuentoActivoProducto($producto)) {
                $productoClone = clone $producto;
                $productoClone->tipo_item = 'producto';
                $productoClone->item_id = $producto->id_producto;
                $productoClone->porcentaje_descuento_calculado = $this->calcularPorcentajeDescuentoProducto($producto);
                $itemsConDescuento->push($productoClone);
            }
        }

        $variacionesConDescuento = ProductoVariacion::with([
            'productoPadre',
            'productoPadre.categoria',
            'productoPadre.marca',
            'productoPadre.etiquetas',
            'atributos.atributo',
            'atributos.valor',
            'imagenesRegistradas'
        ])
            ->whereHas('productoPadre', function ($q) {
                $q->where('bActivo', true);
            })
            ->where('bActivo', true)
            ->where('bTiene_descuento', 1)
            ->where('dPrecio_descuento', '>', 0)
            ->where(function ($query) use ($fechaActual) {
                $query->where(function ($q) use ($fechaActual) {
                    $q->whereNull('dFecha_fin_descuento')
                        ->orWhere('dFecha_fin_descuento', '>=', $fechaActual);
                });
            })
            ->where(function ($query) use ($fechaActual) {
                $query->where(function ($q) use ($fechaActual) {
                    $q->whereNull('dFecha_inicio_descuento')
                        ->orWhere('dFecha_inicio_descuento', '<=', $fechaActual);
                });
            })
            ->get();

        foreach ($variacionesConDescuento as $variacion) {
            if ($this->tieneDescuentoActivoVariacion($variacion)) {
                $variacion->tipo_item = 'variacion';
                $variacion->item_id = $variacion->id_variacion;
                $variacion->porcentaje_descuento_calculado = $this->calcularPorcentajeDescuentoVariacion($variacion);
                $itemsConDescuento->push($variacion);
            }
        }

        $itemsConDescuento = $itemsConDescuento->sortByDesc(function ($item) {
            return $item->porcentaje_descuento_calculado ?? 0;
        })->values();

        if ($limite) {
            $itemsConDescuento = $itemsConDescuento->take($limite);
        }

        return $itemsConDescuento;
    }

    private function tieneDescuentoActivoProducto($producto)
    {
        if (!$producto->bTiene_descuento || $producto->dPrecio_descuento === null || $producto->dPrecio_descuento <= 0) {
            return false;
        }

        $fechaActual = now()->toDateString();

        if ($producto->dFecha_inicio_descuento && $producto->dFecha_fin_descuento) {
            return $fechaActual >= $producto->dFecha_inicio_descuento &&
                $fechaActual <= $producto->dFecha_fin_descuento;
        }

        if ($producto->dFecha_inicio_descuento && !$producto->dFecha_fin_descuento) {
            return $fechaActual >= $producto->dFecha_inicio_descuento;
        }

        if (!$producto->dFecha_inicio_descuento && $producto->dFecha_fin_descuento) {
            return $fechaActual <= $producto->dFecha_fin_descuento;
        }

        return true;
    }

    private function tieneDescuentoActivoVariacion($variacion)
    {
        if (!$variacion->bTiene_descuento || $variacion->dPrecio_descuento === null || $variacion->dPrecio_descuento <= 0) {
            return false;
        }

        $fechaActual = now()->toDateString();

        if ($variacion->dFecha_inicio_descuento && $variacion->dFecha_fin_descuento) {
            return $fechaActual >= $variacion->dFecha_inicio_descuento &&
                $fechaActual <= $variacion->dFecha_fin_descuento;
        }

        if ($variacion->dFecha_inicio_descuento && !$variacion->dFecha_fin_descuento) {
            return $fechaActual >= $variacion->dFecha_inicio_descuento;
        }

        if (!$variacion->dFecha_inicio_descuento && $variacion->dFecha_fin_descuento) {
            return $fechaActual <= $variacion->dFecha_fin_descuento;
        }

        return true;
    }

    private function calcularPorcentajeDescuentoProducto($producto)
    {
        if (!$this->tieneDescuentoActivoProducto($producto) || $producto->dPrecio_venta <= 0) {
            return 0;
        }

        $descuento = (($producto->dPrecio_venta - $producto->dPrecio_descuento) / $producto->dPrecio_venta) * 100;
        return round($descuento);
    }

    private function calcularPorcentajeDescuentoVariacion($variacion)
    {
        if (!$this->tieneDescuentoActivoVariacion($variacion) || $variacion->dPrecio <= 0) {
            return 0;
        }

        $descuento = (($variacion->dPrecio - $variacion->dPrecio_descuento) / $variacion->dPrecio) * 100;
        return round($descuento);
    }

    public function buscar(Request $request)
    {
        $filtroDescuento = $request->has('en_descuento') && $request->en_descuento == '1';

        $resultados = collect();

        if ($filtroDescuento) {
            $productosConDescuento = Producto::with([
                'categoria',
                'marca',
                'etiquetas'
            ])
                ->where('bActivo', true)
                ->where('bTiene_descuento', 1)
                ->where('dPrecio_descuento', '>', 0)
                ->get();

            foreach ($productosConDescuento as $producto) {
                if ($this->tieneDescuentoActivoProducto($producto)) {
                    if ($this->productoCumpleFiltrosBasicos($producto, $request)) {
                        if ($request->con_stock != '1' || $producto->iStock > 0) {
                            $productoClone = clone $producto;
                            $productoClone->tipo_item = 'producto';
                            $productoClone->item_id = $producto->id_producto;
                            $productoClone->porcentaje_descuento_calculado = $this->calcularPorcentajeDescuentoProducto($producto);
                            $resultados->push($productoClone);
                        }
                    }
                }
            }

            $variacionesConDescuento = ProductoVariacion::with([
                'productoPadre',
                'productoPadre.categoria',
                'productoPadre.marca',
                'productoPadre.etiquetas',
                'atributos.atributo',
                'atributos.valor',
                'imagenesRegistradas'
            ])
                ->whereHas('productoPadre', function ($q) use ($request) {
                    $q->where('bActivo', true);
                    $this->aplicarFiltrosAProductoPadre($q, $request);
                })
                ->where('bActivo', true)
                ->where('bTiene_descuento', 1)
                ->where('dPrecio_descuento', '>', 0)
                ->get();

            foreach ($variacionesConDescuento as $variacion) {
                if ($this->tieneDescuentoActivoVariacion($variacion)) {
                    if ($this->variacionCumpleFiltrosBasicos($variacion, $request)) {
                        if ($request->con_stock != '1' || $variacion->iStock > 0) {
                            $variacion->tipo_item = 'variacion';
                            $variacion->item_id = $variacion->id_variacion;
                            $variacion->porcentaje_descuento_calculado = $this->calcularPorcentajeDescuentoVariacion($variacion);
                            $resultados->push($variacion);
                        }
                    }
                }
            }
        } else {
            $productosQuery = Producto::with(['categoria', 'marca', 'etiquetas'])
                ->where('bActivo', true);

            $this->aplicarFiltros($productosQuery, $request);
            $productosIds = $productosQuery->pluck('id_producto')->toArray();

            $productosPadre = Producto::with([
                'categoria',
                'marca',
                'etiquetas'
            ])
                ->whereIn('id_producto', $productosIds)
                ->get();

            foreach ($productosPadre as $producto) {
                if ($this->productoCumpleFiltrosBasicos($producto, $request)) {
                    if ($request->con_stock != '1' || $producto->iStock > 0) {
                        $productoClone = clone $producto;
                        $productoClone->tipo_item = 'producto';
                        $productoClone->item_id = $producto->id_producto;
                        $resultados->push($productoClone);
                    }
                }

                $variaciones = ProductoVariacion::with([
                    'productoPadre',
                    'atributos.atributo',
                    'atributos.valor',
                    'imagenesRegistradas'
                ])
                    ->where('id_producto', $producto->id_producto)
                    ->where('bActivo', true)
                    ->get();

                foreach ($variaciones as $variacion) {
                    if ($this->variacionCumpleFiltrosBasicos($variacion, $request)) {
                        if ($request->con_stock != '1' || $variacion->iStock > 0) {
                            $variacion->tipo_item = 'variacion';
                            $variacion->item_id = $variacion->id_variacion;
                            $resultados->push($variacion);
                        }
                    }
                }
            }
        }

        $resultados = $resultados->unique(function ($item) {
            if ($item->tipo_item === 'variacion') {
                return 'var_' . $item->id_variacion;
            }
            return 'prod_' . $item->id_producto;
        });

        $resultados = $this->aplicarOrdenamiento($resultados, $request->get('orden', 'nombre'));

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

    private function aplicarFiltrosAProductoPadre($query, $request)
    {
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('vNombre', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('tDescripcion_corta', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('tDescripcion_larga', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->has('categorias') && !empty($request->categorias)) {
            $query->whereIn('id_categoria', $request->categorias);
        }

        if ($request->has('marcas') && !empty($request->marcas)) {
            $query->whereIn('id_marca', $request->marcas);
        }

        if ($request->has('etiquetas') && !empty($request->etiquetas)) {
            $query->whereHas('etiquetas', function ($q) use ($request) {
                $q->whereIn('tbl_etiquetas.id_etiqueta', $request->etiquetas);
            });
        }
    }

    private function aplicarFiltros($query, $request)
    {
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('vNombre', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('tDescripcion_corta', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('tDescripcion_larga', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('categoria', function ($catQuery) use ($searchTerm) {
                        $catQuery->where('tbl_categorias.vNombre', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('marca', function ($brandQuery) use ($searchTerm) {
                        $brandQuery->where('tbl_marcas.vNombre', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('etiquetas', function ($tagQuery) use ($searchTerm) {
                        $tagQuery->where('tbl_etiquetas.vNombre', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        if ($request->has('categorias') && !empty($request->categorias)) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->whereIn('tbl_categorias.id_categoria', $request->categorias);
            });
        }

        if ($request->has('marcas') && !empty($request->marcas)) {
            $query->whereHas('marca', function ($q) use ($request) {
                $q->whereIn('tbl_marcas.id_marca', $request->marcas);
            });
        }

        if ($request->has('etiquetas') && !empty($request->etiquetas)) {
            $query->whereHas('etiquetas', function ($q) use ($request) {
                $q->whereIn('tbl_etiquetas.id_etiqueta', $request->etiquetas);
            });
        }

        if ($request->has('precio_min') && !empty($request->precio_min)) {
            $query->where('dPrecio_venta', '>=', $request->precio_min);
        }

        if ($request->has('precio_max') && !empty($request->precio_max)) {
            $query->where('dPrecio_venta', '<=', $request->precio_max);
        }
    }

    private function productoCumpleFiltrosBasicos($producto, $request)
    {
        if ($request->has('precio_min') && !empty($request->precio_min)) {
            $precioMin = floatval($request->precio_min);
            $precioActual = $this->tieneDescuentoActivoProducto($producto) ? $producto->dPrecio_descuento : $producto->dPrecio_venta;
            if ($precioActual < $precioMin) {
                return false;
            }
        }

        if ($request->has('precio_max') && !empty($request->precio_max)) {
            $precioMax = floatval($request->precio_max);
            $precioActual = $this->tieneDescuentoActivoProducto($producto) ? $producto->dPrecio_descuento : $producto->dPrecio_venta;
            if ($precioActual > $precioMax) {
                return false;
            }
        }

        return true;
    }

    private function variacionCumpleFiltrosBasicos($variacion, $request)
    {
        if (!$variacion->productoPadre) {
            return false;
        }

        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = strtolower($request->q);
            $nombreProducto = strtolower($variacion->productoPadre->vNombre ?? '');
            $atributosTexto = strtolower($variacion->getAtributosTexto() ?? '');

            if (
                strpos($nombreProducto, $searchTerm) === false &&
                strpos($atributosTexto, $searchTerm) === false
            ) {
                return false;
            }
        }

        $precioActual = $this->tieneDescuentoActivoVariacion($variacion) ? $variacion->dPrecio_descuento : $variacion->dPrecio;

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

    private function aplicarOrdenamiento($coleccion, $orden)
    {
        switch ($orden) {
            case 'precio_asc':
                return $coleccion->sortBy(function ($item) {
                    if ($item->tipo_item === 'variacion') {
                        return $this->tieneDescuentoActivoVariacion($item) ? $item->dPrecio_descuento : $item->dPrecio;
                    } else {
                        return $this->tieneDescuentoActivoProducto($item) ? $item->dPrecio_descuento : $item->dPrecio_venta;
                    }
                })->values();

            case 'precio_desc':
                return $coleccion->sortByDesc(function ($item) {
                    if ($item->tipo_item === 'variacion') {
                        return $this->tieneDescuentoActivoVariacion($item) ? $item->dPrecio_descuento : $item->dPrecio;
                    } else {
                        return $this->tieneDescuentoActivoProducto($item) ? $item->dPrecio_descuento : $item->dPrecio_venta;
                    }
                })->values();

            case 'recientes':
                return $coleccion->sortByDesc(function ($item) {
                    if ($item->tipo_item === 'variacion') {
                        return $item->tFecha_registro ?? ($item->productoPadre ? $item->productoPadre->tFecha_registro : now());
                    } else {
                        return $item->tFecha_registro ?? now();
                    }
                })->values();

            case 'descuento_mayor':
                return $coleccion->sortByDesc(function ($item) {
                    return $item->porcentaje_descuento_calculado ?? 0;
                })->values();

            case 'nombre':
            default:
                return $coleccion->sortBy(function ($item) {
                    if ($item->tipo_item === 'variacion') {
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

        $productos = Producto::with(['categoria', 'marca'])
            ->where('bActivo', true)
            ->where(function ($q) use ($term) {
                $q->where('vNombre', 'LIKE', "%{$term}%")
                    ->orWhere('tDescripcion_corta', 'LIKE', "%{$term}%")
                    ->orWhereHas('categoria', function ($catQuery) use ($term) {
                        $catQuery->where('tbl_categorias.vNombre', 'LIKE', "%{$term}%");
                    })
                    ->orWhereHas('marca', function ($brandQuery) use ($term) {
                        $brandQuery->where('tbl_marcas.vNombre', 'LIKE', "%{$term}%");
                    });
            })
            ->take(5)
            ->get();

        foreach ($productos as $producto) {
            $tieneDescuento = $this->tieneDescuentoActivoProducto($producto);
            $precioMostrar = $tieneDescuento ? $producto->dPrecio_descuento : $producto->dPrecio_venta;
            $texto = $producto->vNombre . ' - $' . number_format($precioMostrar, 2);

            if ($tieneDescuento) {
                $descuento = $this->calcularPorcentajeDescuentoProducto($producto);
                $texto .= " (¡{$descuento}% OFF!)";
            }

            $sugerencias[] = [
                'id' => $producto->id_producto,
                'text' => $texto,
                'url' => route('productos.show.public', $producto->id_producto),
                'en_descuento' => $tieneDescuento,
                'porcentaje' => $tieneDescuento ? $this->calcularPorcentajeDescuentoProducto($producto) : 0,
                'tipo' => 'producto'
            ];
        }

        $variaciones = ProductoVariacion::with([
            'productoPadre',
            'atributos.atributo',
            'atributos.valor'
        ])
            ->whereHas('productoPadre', function ($q) use ($term) {
                $q->where('bActivo', true);
            })
            ->where(function ($q) use ($term) {
                $q->whereHas('productoPadre', function ($subQ) use ($term) {
                    $subQ->where('vNombre', 'LIKE', "%{$term}%");
                })->orWhereHas('atributos.valor', function ($attrQ) use ($term) {
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
            $precioMostrar = $tieneDescuento ? $variacion->dPrecio_descuento : $variacion->dPrecio;
            $texto = $variacion->productoPadre->vNombre . ' - ' . $variacion->getAtributosTexto() . ' - $' . number_format($precioMostrar, 2);

            if ($tieneDescuento) {
                $descuento = $this->calcularPorcentajeDescuentoVariacion($variacion);
                $texto .= " (¡{$descuento}% OFF!)";
            }

            $sugerencias[] = [
                'id' => $variacion->id_variacion,
                'text' => $texto,
                'url' => route('productos.show.public', [$variacion->productoPadre->id_producto, 'variacion' => $variacion->id_variacion]),
                'en_descuento' => $tieneDescuento,
                'porcentaje' => $tieneDescuento ? $this->calcularPorcentajeDescuentoVariacion($variacion) : 0,
                'tipo' => 'variacion'
            ];
        }

        $sugerencias = collect($sugerencias)->sortByDesc('en_descuento')->sortByDesc('porcentaje')->take(10)->values()->toArray();

        return response()->json($sugerencias);
    }
}
