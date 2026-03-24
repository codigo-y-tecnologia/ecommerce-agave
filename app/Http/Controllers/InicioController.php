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
            // Obtener productos con descuento activo - 6 productos máximo
            $productosDescuento = $this->getProductosConDescuento(6);
            
            // Obtener productos destacados (recientes) - 6 productos máximo
            $productosDestacados = $this->getProductosDestacados(6);
            
            // Obtener productos recomendados - 6 productos máximo
            $productosRecomendados = $this->getProductosRecomendados(6);
            
            // Obtener TODOS los productos SIN PAGINACIÓN (solo los primeros 12)
            $todosLosItems = $this->getTodosLosProductos(12);
            
            return view('inicio', compact(
                'productosDescuento', 
                'productosDestacados', 
                'productosRecomendados',
                'todosLosItems'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error en InicioController: ' . $e->getMessage());
            
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
     * Obtener productos con descuento activo (productos + variaciones) - LIMITADO
     */
    private function getProductosConDescuento($limit = 6)
    {
        $items = collect();
        
        // Productos con descuento
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
                
                $precioBase = $producto->tieneDescuentoActivo() ? $producto->dPrecio_descuento : $producto->dPrecio_venta;
                $totalImpuestos = 0;
                foreach ($producto->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                    }
                }
                $producto->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $producto->precio_original_con_impuesto = $producto->dPrecio_venta;
                foreach ($producto->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $producto->precio_original_con_impuesto += $producto->dPrecio_venta * ($impuesto->dPorcentaje / 100);
                    }
                }
                
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
                
                $precioBase = $variacion->tieneDescuentoActivo() ? $variacion->dPrecio_descuento : $variacion->dPrecio;
                $totalImpuestos = 0;
                
                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $totalImpuestos = $precioBase * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($variacion->producto && $variacion->producto->impuestos->count() > 0) {
                    foreach ($variacion->producto->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }
                
                $variacion->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $variacion->precio_original_con_impuesto = $variacion->dPrecio;
                
                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($variacion->producto && $variacion->producto->impuestos->count() > 0) {
                    foreach ($variacion->producto->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }
                
                $items->push($variacion);
            }
        }
        
        $items = $items->sortByDesc(function($item) {
            return $item->porcentaje_descuento;
        });
        
        return $items->take($limit);
    }
    
    /**
     * Obtener productos destacados - LIMITADO
     */
    private function getProductosDestacados($limit = 6)
    {
        $productos = Producto::where('bActivo', true)
            ->whereDoesntHave('variaciones', function ($query) {
                $query->where('bActivo', true);
            })
            ->with(['categoria', 'marca', 'etiquetas', 'impuestos'])
            ->orderBy('tFecha_registro', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($producto) {
                $producto->tipo_item = 'producto';
                
                $precioBase = $producto->tieneDescuentoActivo() ? $producto->dPrecio_descuento : $producto->dPrecio_venta;
                $totalImpuestos = 0;
                foreach ($producto->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                    }
                }
                $producto->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $producto->precio_original_con_impuesto = $producto->dPrecio_venta;
                foreach ($producto->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $producto->precio_original_con_impuesto += $producto->dPrecio_venta * ($impuesto->dPorcentaje / 100);
                    }
                }
                
                return $producto;
            });
            
        return $productos;
    }
    
    /**
     * Obtener productos recomendados - LIMITADO
     */
    private function getProductosRecomendados($limit = 6)
    {
        $productos = Producto::where('bActivo', true)
            ->whereDoesntHave('variaciones', function ($query) {
                $query->where('bActivo', true);
            })
            ->with(['categoria', 'marca', 'etiquetas', 'impuestos'])
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(function($producto) {
                $producto->tipo_item = 'producto';
                
                $precioBase = $producto->tieneDescuentoActivo() ? $producto->dPrecio_descuento : $producto->dPrecio_venta;
                $totalImpuestos = 0;
                foreach ($producto->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                    }
                }
                $producto->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $producto->precio_original_con_impuesto = $producto->dPrecio_venta;
                foreach ($producto->impuestos as $impuesto) {
                    if ($impuesto->bActivo) {
                        $producto->precio_original_con_impuesto += $producto->dPrecio_venta * ($impuesto->dPorcentaje / 100);
                    }
                }
                
                return $producto;
            });
            
        return $productos;
    }
    
    /**
     * Obtener productos SIN PAGINACIÓN (solo los primeros 12)
     */
    private function getTodosLosProductos($limit = 12)
    {
        $items = collect();
        
        // Productos sin variaciones
        $productosSinVariaciones = Producto::where('bActivo', true)
            ->whereDoesntHave('variaciones', function ($query) {
                $query->where('bActivo', true);
            })
            ->with(['categoria', 'marca', 'etiquetas', 'impuestos'])
            ->orderBy('id_producto', 'desc')
            ->limit($limit)
            ->get();
            
        foreach ($productosSinVariaciones as $producto) {
            $producto->tipo_item = 'producto';
            $producto->item_id = $producto->id_producto;
            
            $precioBase = $producto->tieneDescuentoActivo() ? $producto->dPrecio_descuento : $producto->dPrecio_venta;
            $totalImpuestos = 0;
            foreach ($producto->impuestos as $impuesto) {
                if ($impuesto->bActivo) {
                    $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                }
            }
            $producto->precio_final_con_impuesto = $precioBase + $totalImpuestos;
            $producto->precio_original_con_impuesto = $producto->dPrecio_venta;
            foreach ($producto->impuestos as $impuesto) {
                if ($impuesto->bActivo) {
                    $producto->precio_original_con_impuesto += $producto->dPrecio_venta * ($impuesto->dPorcentaje / 100);
                }
            }
            
            $items->push($producto);
        }
        
        // Contar cuántos productos ya tenemos
        $countProductos = $items->count();
        $remaining = $limit - $countProductos;
        
        // Si hay espacio, agregar variaciones
        if ($remaining > 0) {
            $variaciones = ProductoVariacion::whereHas('producto', function($query) {
                    $query->where('bActivo', true);
                })
                ->where('bActivo', true)
                ->with(['producto.categoria', 'producto.marca', 'producto.etiquetas', 'impuesto', 'atributos.valor'])
                ->orderBy('id_variacion', 'desc')
                ->limit($remaining)
                ->get();
                
            foreach ($variaciones as $variacion) {
                $variacion->tipo_item = 'variacion';
                $variacion->item_id = $variacion->id_variacion;
                
                $precioBase = $variacion->tieneDescuentoActivo() ? $variacion->dPrecio_descuento : $variacion->dPrecio;
                $totalImpuestos = 0;
                
                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $totalImpuestos = $precioBase * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($variacion->producto && $variacion->producto->impuestos->count() > 0) {
                    foreach ($variacion->producto->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $totalImpuestos += $precioBase * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }
                
                $variacion->precio_final_con_impuesto = $precioBase + $totalImpuestos;
                $variacion->precio_original_con_impuesto = $variacion->dPrecio;
                
                if ($variacion->impuesto && $variacion->impuesto->bActivo) {
                    $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($variacion->impuesto->dPorcentaje / 100);
                } elseif ($variacion->producto && $variacion->producto->impuestos->count() > 0) {
                    foreach ($variacion->producto->impuestos as $impuesto) {
                        if ($impuesto->bActivo) {
                            $variacion->precio_original_con_impuesto += $variacion->dPrecio * ($impuesto->dPorcentaje / 100);
                        }
                    }
                }
                
                $items->push($variacion);
            }
        }
        
        // Mezclar aleatoriamente
        $items = $items->shuffle();
        
        return $items;
    }
}