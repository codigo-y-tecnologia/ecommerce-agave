<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductoVariacion;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Support\Facades\DB;

class InicioController extends Controller
{
    /**
     * Mostrar la página de inicio con productos destacados
     */
    public function index()
    {
        try {
            // Obtener productos con descuento activo (productos y variaciones como entidades separadas)
            $productosDescuento = $this->getProductosConDescuento(8);
            
            // Obtener productos destacados (recientes) - SOLO productos sin variaciones
            $productosDestacados = $this->getProductosDestacados(8);
            
            // Obtener productos recomendados - SOLO productos sin variaciones
            $productosRecomendados = $this->getProductosRecomendados(8);
            
            // Obtener todos los productos con paginación (productos sin variaciones + variaciones)
            $todosLosItems = $this->getTodosLosProductos(12);
            
            return view('inicio', compact(
                'productosDescuento', 
                'productosDestacados', 
                'productosRecomendados',
                'todosLosItems'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error en InicioController: ' . $e->getMessage());
            
            // En caso de error, devolver colecciones vacías
            $productosDescuento = collect([]);
            $productosDestacados = collect([]);
            $productosRecomendados = collect([]);
            $todosLosItems = collect([]);
            
            return view('inicio', compact(
                'productosDescuento', 
                'productosDestacados', 
                'productosRecomendados',
                'todosLosItems'
            ))->with('error', 'Error al cargar los productos. Intenta más tarde.');
        }
    }
    
    /**
     * Obtener productos con descuento activo (productos + variaciones)
     */
    private function getProductosConDescuento($limit = 8)
    {
        $items = collect();
        
        // Productos con descuento (que NO tengan variaciones activas)
        $productos = Producto::where('bActivo', true)
            ->whereDoesntHave('variaciones', function ($query) {
                $query->where('bActivo', true);
            })
            ->where('bTiene_descuento', true)
            ->where(function($query) {
                $query->whereNull('dFecha_fin_descuento')
                    ->orWhere('dFecha_fin_descuento', '>=', now()->toDateString());
            })
            ->where(function($query) {
                $query->whereNull('dFecha_inicio_descuento')
                    ->orWhere('dFecha_inicio_descuento', '<=', now()->toDateString());
            })
            ->where('dPrecio_descuento', '>', 0)
            ->where('dPrecio_descuento', '<', DB::raw('dPrecio_venta'))
            ->with(['categoria', 'marca', 'etiquetas', 'impuestos'])
            ->get();
            
        foreach ($productos as $producto) {
            if ($producto->tieneDescuentoActivo()) {
                $producto->tipo_item = 'producto';
                $producto->item_id = $producto->id_producto;
                $items->push($producto);
            }
        }
        
        // Variaciones con descuento
        $variaciones = ProductoVariacion::whereHas('producto', function($query) {
                $query->where('bActivo', true);
            })
            ->where('bActivo', true)
            ->where('bTiene_descuento', true)
            ->where(function($query) {
                $query->whereNull('dFecha_fin_descuento')
                    ->orWhere('dFecha_fin_descuento', '>=', now()->toDateString());
            })
            ->where(function($query) {
                $query->whereNull('dFecha_inicio_descuento')
                    ->orWhere('dFecha_inicio_descuento', '<=', now()->toDateString());
            })
            ->where('dPrecio_descuento', '>', 0)
            ->where('dPrecio_descuento', '<', DB::raw('dPrecio'))
            ->with(['producto.categoria', 'producto.marca', 'producto.etiquetas', 'impuesto', 'atributos.valor'])
            ->get();
            
        foreach ($variaciones as $variacion) {
            if ($variacion->tieneDescuentoActivo()) {
                $variacion->tipo_item = 'variacion';
                $variacion->item_id = $variacion->id_variacion;
                $items->push($variacion);
            }
        }
        
        // Ordenar por mayor porcentaje de descuento
        $items = $items->sortByDesc(function($item) {
            if ($item->tipo_item === 'variacion') {
                return $item->porcentaje_descuento;
            }
            return $item->porcentajeDescuento;
        });
        
        return $items->take($limit);
    }
    
    /**
     * Obtener productos destacados (recientes) - SOLO productos sin variaciones
     */
    private function getProductosDestacados($limit = 8)
    {
        return Producto::where('bActivo', true)
            ->whereDoesntHave('variaciones', function ($query) {
                $query->where('bActivo', true);
            })
            ->with(['categoria', 'marca', 'etiquetas', 'impuestos'])
            ->orderBy('tFecha_registro', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($producto) {
                $producto->tipo_item = 'producto';
                return $producto;
            });
    }
    
    /**
     * Obtener productos recomendados (aleatorios) - SOLO productos sin variaciones
     */
    private function getProductosRecomendados($limit = 8)
    {
        return Producto::where('bActivo', true)
            ->whereDoesntHave('variaciones', function ($query) {
                $query->where('bActivo', true);
            })
            ->with(['categoria', 'marca', 'etiquetas', 'impuestos'])
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function($producto) {
                $producto->tipo_item = 'producto';
                return $producto;
            });
    }
    
    /**
     * Obtener todos los productos con paginación (productos sin variaciones + variaciones)
     */
    private function getTodosLosProductos($perPage = 12)
    {
        $items = collect();
        
        // Productos que NO tienen variaciones activas
        $productosSinVariaciones = Producto::where('bActivo', true)
            ->whereDoesntHave('variaciones', function ($query) {
                $query->where('bActivo', true);
            })
            ->with(['categoria', 'marca', 'etiquetas', 'impuestos'])
            ->orderBy('id_producto', 'desc')
            ->get();
            
        foreach ($productosSinVariaciones as $producto) {
            $producto->tipo_item = 'producto';
            $producto->item_id = $producto->id_producto;
            $items->push($producto);
        }
        
        // Todas las variaciones activas
        $variaciones = ProductoVariacion::whereHas('producto', function($query) {
                $query->where('bActivo', true);
            })
            ->where('bActivo', true)
            ->with(['producto.categoria', 'producto.marca', 'producto.etiquetas', 'impuesto', 'atributos.valor'])
            ->orderBy('id_variacion', 'desc')
            ->get();
            
        foreach ($variaciones as $variacion) {
            $variacion->tipo_item = 'variacion';
            $variacion->item_id = $variacion->id_variacion;
            $items->push($variacion);
        }
        
        // Mezclar (aleatorio)
        $items = $items->shuffle();
        
        // Paginación manual
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice($offset, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return $paginated;
    }
}