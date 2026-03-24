<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{Producto, ProductoVariacion, Categoria, Marca, Etiqueta};
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{

    public function index()
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

        // Si el usuario está autenticado, lo redirigimos a su panel correspondiente
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('superadmin')) {
                return view('dashboards.superadmin');
            }

            if ($user->hasRole('admin')) {
                return view('dashboards.admin');
            }

            if ($user->hasRole('cliente')) {
                return view('inicio', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
            }

            // Fallback de seguridad
            return view('inicio', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
        }

        // Si no está autenticado, mostramos la vista pública (cliente como visitante)
        return view('inicio', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
    }

    public function cliente()
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

        return view('inicio', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
    }

    public function admin()
    {
        return view('dashboards.admin');
    }

    public function superadmin()
    {
        return view('dashboards.superadmin');
    }

    private function obtenerTodosLosItems()
    {
        $items = collect();

        $productos = Producto::with([
            'categoria',
            'marca',
            'etiquetas',
            'impuestos'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->get();

        foreach ($productos as $producto) {
            $productoClone = clone $producto;
            $productoClone->tipo_item = 'producto';
            $productoClone->item_id = $producto->id_producto;

            // Calcular precio final con impuestos para producto
            $precioBase = $productoClone->tieneDescuentoActivo() ? $productoClone->dPrecio_descuento : $productoClone->dPrecio_venta;
            $totalImpuestos = 0;
            foreach ($productoClone->impuestos as $impuesto) {
                if ($impuesto->bActivo) {
                    $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                }
            }
            $productoClone->precio_final_con_impuesto = $precioBase + $totalImpuestos;
            $productoClone->precio_original_con_impuesto = $productoClone->dPrecio_venta;
            foreach ($productoClone->impuestos as $impuesto) {
                if ($impuesto->bActivo) {
                    $productoClone->precio_original_con_impuesto += $productoClone->dPrecio_venta * ($impuesto->dPorcentaje / 100);
                }
            }

            $items->push($productoClone);

            $variaciones = ProductoVariacion::with([
                'productoPadre',
                'productoPadre.categoria',
                'productoPadre.marca',
                'productoPadre.etiquetas',
                'atributos.atributo',
                'atributos.valor',
                'imagenesRegistradas',
                'impuesto'
            ])
                ->where('id_producto', $producto->id_producto)
                ->where('bActivo', true)
                ->get();

            foreach ($variaciones as $variacion) {
                $variacion->tipo_item = 'variacion';
                $variacion->item_id = $variacion->id_variacion;

                // Calcular precio final con impuestos para variación
                $precioBase = $variacion->tieneDescuentoActivo() ? $variacion->dPrecio_descuento : $variacion->dPrecio;
                $totalImpuestos = 0;

                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $totalImpuestos = $precioBase * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($producto->impuestos->count() > 0) {
                    foreach ($producto->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }

                $variacion->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $variacion->precio_original_con_impuesto = $variacion->dPrecio;

                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($producto->impuestos->count() > 0) {
                    foreach ($producto->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }

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
        $itemsConDescuento = collect();

        // Productos con descuento
        $productosConDescuento = Producto::with([
            'categoria',
            'marca',
            'etiquetas',
            'impuestos'
        ])
            ->where('bActivo', true)
            ->where('bTiene_descuento', 1)
            ->where('dPrecio_descuento', '>', 0)
            ->get();

        foreach ($productosConDescuento as $producto) {
            if ($this->tieneDescuentoActivoProducto($producto)) {
                $productoClone = clone $producto;
                $productoClone->tipo_item = 'producto';
                $productoClone->item_id = $producto->id_producto;
                $productoClone->porcentaje_descuento_calculado = $this->calcularPorcentajeDescuentoProducto($producto);

                // Calcular precio final con impuestos
                $precioBase = $productoClone->tieneDescuentoActivo() ? $productoClone->dPrecio_descuento : $productoClone->dPrecio_venta;
                $totalImpuestos = 0;
                foreach ($productoClone->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                    }
                }
                $productoClone->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $productoClone->precio_original_con_impuesto = $productoClone->dPrecio_venta;
                foreach ($productoClone->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $productoClone->precio_original_con_impuesto += $productoClone->dPrecio_venta * ($impuesto->dPorcentaje / 100);
                    }
                }

                $itemsConDescuento->push($productoClone);
            }
        }

        // Variaciones con descuento
        $variacionesConDescuento = ProductoVariacion::with([
            'productoPadre',
            'productoPadre.categoria',
            'productoPadre.marca',
            'productoPadre.etiquetas',
            'atributos.atributo',
            'atributos.valor',
            'imagenesRegistradas',
            'impuesto'
        ])
            ->whereHas('productoPadre', function ($q) {
                $q->where('bActivo', true);
            })
            ->where('bActivo', true)
            ->where('bTiene_descuento', 1)
            ->where('dPrecio_descuento', '>', 0)
            ->get();

        foreach ($variacionesConDescuento as $variacion) {
            if ($this->tieneDescuentoActivoVariacion($variacion)) {
                $variacion->tipo_item = 'variacion';
                $variacion->item_id = $variacion->id_variacion;
                $variacion->porcentaje_descuento_calculado = $this->calcularPorcentajeDescuentoVariacion($variacion);

                // Calcular precio final con impuestos
                $precioBase = $variacion->tieneDescuentoActivo() ? $variacion->dPrecio_descuento : $variacion->dPrecio;
                $totalImpuestos = 0;

                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $totalImpuestos = $precioBase * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($variacion->productoPadre && $variacion->productoPadre->impuestos->count() > 0) {
                    foreach ($variacion->productoPadre->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }

                $variacion->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $variacion->precio_original_con_impuesto = $variacion->dPrecio;

                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($variacion->productoPadre && $variacion->productoPadre->impuestos->count() > 0) {
                    foreach ($variacion->productoPadre->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }

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
            $inicio = date('Y-m-d', strtotime($producto->dFecha_inicio_descuento));
            $fin = date('Y-m-d', strtotime($producto->dFecha_fin_descuento));
            return $fechaActual >= $inicio && $fechaActual <= $fin;
        }

        if ($producto->dFecha_inicio_descuento && !$producto->dFecha_fin_descuento) {
            $inicio = date('Y-m-d', strtotime($producto->dFecha_inicio_descuento));
            return $fechaActual >= $inicio;
        }

        if (!$producto->dFecha_inicio_descuento && $producto->dFecha_fin_descuento) {
            $fin = date('Y-m-d', strtotime($producto->dFecha_fin_descuento));
            return $fechaActual <= $fin;
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
            $inicio = date('Y-m-d', strtotime($variacion->dFecha_inicio_descuento));
            $fin = date('Y-m-d', strtotime($variacion->dFecha_fin_descuento));
            return $fechaActual >= $inicio && $fechaActual <= $fin;
        }

        if ($variacion->dFecha_inicio_descuento && !$variacion->dFecha_fin_descuento) {
            $inicio = date('Y-m-d', strtotime($variacion->dFecha_inicio_descuento));
            return $fechaActual >= $inicio;
        }

        if (!$variacion->dFecha_inicio_descuento && $variacion->dFecha_fin_descuento) {
            $fin = date('Y-m-d', strtotime($variacion->dFecha_fin_descuento));
            return $fechaActual <= $fin;
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
}
