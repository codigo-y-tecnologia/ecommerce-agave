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

        // 1. Productos destacados (los más recientes) - SOLO productos activos
        $productosDestacados = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(12)
            ->get();

        // 2. Productos en descuento - INCLUYE productos y variaciones
        $productosDescuento = $this->obtenerItemsConDescuento(12);

        // 3. Productos Recomendados - SOLO productos
        $productosRecomendados = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(8)
            ->get();

        // 4. TODOS los items (productos + variaciones) para la sección principal
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
                return view('dashboards.cliente', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
            }

            // Fallback de seguridad
            return view('dashboards.cliente', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
        }

        // Si no está autenticado, mostramos la vista pública (cliente como visitante)
        return view('dashboards.cliente', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
    }

    public function cliente()
    {
        // 1. Productos destacados (los más recientes) - SOLO productos activos
        $productosDestacados = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(12)
            ->get();

        // 2. Productos en descuento - INCLUYE productos y variaciones
        $productosDescuento = $this->obtenerItemsConDescuento(12);

        // 3. Productos Recomendados - SOLO productos
        $productosRecomendados = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(8)
            ->get();

        // 4. TODOS los items (productos + variaciones) para la sección principal
        $todosLosItems = $this->obtenerTodosLosItems();

        return view('dashboards.cliente', compact('productosDestacados', 'productosDescuento', 'productosRecomendados', 'todosLosItems'));
    }

    public function admin()
    {
        return view('dashboards.admin');
    }

    public function superadmin()
    {
        return view('dashboards.superadmin');
    }

    /**
     * Obtiene TODOS los items (productos y variaciones) para la página de inicio
     */
    private function obtenerTodosLosItems()
    {
        $items = collect();

        // 1. Obtener todos los productos activos
        $productos = Producto::with([
            'categoria',
            'marca',
            'etiquetas'
        ])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->get();

        foreach ($productos as $producto) {
            // Agregar el producto padre como item independiente
            $productoClone = clone $producto;
            $productoClone->tipo_item = 'producto';
            $productoClone->item_id = $producto->id_producto;
            $items->push($productoClone);

            // Agregar TODAS las variaciones activas de este producto como items independientes
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

        // Ordenar por fecha de registro (más recientes primero)
        $items = $items->sortByDesc(function ($item) {
            if ($item->tipo_item === 'variacion') {
                return $item->tFecha_registro ?? ($item->productoPadre ? $item->productoPadre->tFecha_registro : now());
            }
            return $item->tFecha_registro ?? now();
        })->values();

        // Paginar manualmente
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

    /**
     * Obtiene productos y variaciones con descuento activo
     */
    private function obtenerItemsConDescuento($limite = null)
    {
        $fechaActual = now()->toDateString();
        $itemsConDescuento = collect();

        // 1. PRODUCTOS CON DESCUENTO
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

        // 2. VARIACIONES CON DESCUENTO
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
            ->where('bTiene_oferta', 1)
            ->where('dPrecio_oferta', '>', 0)
            ->where(function ($query) use ($fechaActual) {
                $query->where(function ($q) use ($fechaActual) {
                    $q->whereNull('dFecha_fin_oferta')
                        ->orWhere('dFecha_fin_oferta', '>=', $fechaActual);
                });
            })
            ->where(function ($query) use ($fechaActual) {
                $query->where(function ($q) use ($fechaActual) {
                    $q->whereNull('dFecha_inicio_oferta')
                        ->orWhere('dFecha_inicio_oferta', '<=', $fechaActual);
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

        // Ordenar por porcentaje de descuento
        $itemsConDescuento = $itemsConDescuento->sortByDesc(function ($item) {
            return $item->porcentaje_descuento_calculado ?? 0;
        })->values();

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

    /**
     * Verifica si una variación tiene descuento activo
     */
    private function tieneDescuentoActivoVariacion($variacion)
    {
        if (!$variacion->bTiene_oferta || $variacion->dPrecio_oferta === null || $variacion->dPrecio_oferta <= 0) {
            return false;
        }

        $fechaActual = now()->toDateString();

        if ($variacion->dFecha_inicio_oferta && $variacion->dFecha_fin_oferta) {
            return $fechaActual >= $variacion->dFecha_inicio_oferta &&
                $fechaActual <= $variacion->dFecha_fin_oferta;
        }

        if ($variacion->dFecha_inicio_oferta && !$variacion->dFecha_fin_oferta) {
            return $fechaActual >= $variacion->dFecha_inicio_oferta;
        }

        if (!$variacion->dFecha_inicio_oferta && $variacion->dFecha_inicio_oferta) {
            return $fechaActual <= $variacion->dFecha_fin_oferta;
        }

        return true;
    }

    /**
     * Calcula el porcentaje de descuento de un producto
     */
    private function calcularPorcentajeDescuentoProducto($producto)
    {
        if (!$this->tieneDescuentoActivoProducto($producto) || $producto->dPrecio_venta <= 0) {
            return 0;
        }

        $descuento = (($producto->dPrecio_venta - $producto->dPrecio_descuento) / $producto->dPrecio_venta) * 100;
        return round($descuento);
    }

    /**
     * Calcula el porcentaje de descuento de una variación
     */
    private function calcularPorcentajeDescuentoVariacion($variacion)
    {
        if (!$this->tieneDescuentoActivoVariacion($variacion) || $variacion->dPrecio <= 0) {
            return 0;
        }

        $descuento = (($variacion->dPrecio - $variacion->dPrecio_oferta) / $variacion->dPrecio) * 100;
        return round($descuento);
    }
}
