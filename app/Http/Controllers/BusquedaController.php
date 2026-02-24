<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusquedaController extends Controller
{
    // Método para la página de inicio
    public function inicio()
    {
        // Productos destacados (los más recientes)
        $productos = Producto::with(['categoria', 'marca', 'etiquetas'])
            ->where('bActivo', true)
            ->orderBy('id_producto', 'desc')
            ->take(12)
            ->get();
        
        // Productos en descuento (CORREGIDO: usando bTiene_oferta, dPrecio_oferta, dFecha_fin_oferta)
        $productosDescuento = Producto::with(['categoria', 'marca', 'etiquetas'])
            ->where('bActivo', true)
            ->where('bTiene_oferta', 1)
            ->where(function($dateQuery) {
                $dateQuery->whereNull('dFecha_fin_oferta')
                          ->orWhere('dFecha_fin_oferta', '>=', now());
            })
            ->orderByRaw('((dPrecio_venta - dPrecio_oferta) / dPrecio_venta) DESC')
            ->take(8)
            ->get();
        
        // Productos más vendidos (simulado)
        $productosMasVendidos = Producto::with(['categoria', 'marca', 'etiquetas'])
            ->where('bActivo', true)
            ->where('iStock', '>', 0)
            ->inRandomOrder()
            ->take(8)
            ->get();
        
        return view('inicio', compact('productos', 'productosDescuento', 'productosMasVendidos'));
    }

    public function buscar(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'etiquetas'])
            ->where('bActivo', true);

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

        // FILTRO DE BÚSQUEDA POR DESCUENTO (CORREGIDO - usando bTiene_oferta)
        if ($request->has('en_descuento') && $request->en_descuento == '1') {
            $query->where(function($q) {
                $q->where('bTiene_oferta', 1)
                  ->where(function($dateQuery) {
                      $dateQuery->whereNull('dFecha_fin_oferta')
                                ->orWhere('dFecha_fin_oferta', '>=', now());
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

        // Filtro por stock
        if ($request->has('con_stock') && $request->con_stock == '1') {
            $query->where('iStock', '>', 0);
        }

        // Filtro por rango de descuento (CORREGIDO - usando dPrecio_oferta)
        if ($request->has('descuento_min') && !empty($request->descuento_min)) {
            $descuentoMin = floatval($request->descuento_min);
            $query->where('bTiene_oferta', 1)
                  ->whereRaw('((dPrecio_venta - dPrecio_oferta) / dPrecio_venta * 100) >= ?', [$descuentoMin]);
        }

        // Ordenamiento
        $orden = $request->get('orden', 'nombre');
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('dPrecio_venta', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('dPrecio_venta', 'desc');
                break;
            case 'recientes':
                $query->orderBy('id_producto', 'desc');
                break;
            case 'descuento_mayor':
                $query->where('bTiene_oferta', 1)
                      ->orderByRaw('((dPrecio_venta - dPrecio_oferta) / dPrecio_venta) DESC');
                break;
            case 'nombre':
            default:
                $query->orderBy('vNombre', 'asc');
                break;
        }

        $productos = $query->paginate(12)->appends($request->all());

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

    public function busquedaRapida(Request $request)
    {
        $term = $request->get('q');
        
        if (!$term) {
            return response()->json([]);
        }

        $productos = Producto::where('bActivo', true)
            ->where(function($q) use ($term) {
                $q->where('vNombre', 'LIKE', "%{$term}%")
                  ->orWhere('tDescripcion_corta', 'LIKE', "%{$term}%")
                  ->orWhereHas('categoria', function($catQuery) use ($term) {
                      $catQuery->where('tbl_categorias.vNombre', 'LIKE', "%{$term}%");
                  });
            })
            ->take(5)
            ->get(['id_producto', 'vNombre', 'dPrecio_venta', 'dPrecio_oferta', 'bTiene_oferta']);

        $sugerencias = [];
        foreach ($productos as $producto) {
            $tieneDescuento = $producto->tieneDescuentoActivo(); // Este método debe existir en el modelo
            $precioMostrar = $tieneDescuento ? $producto->dPrecio_oferta : $producto->dPrecio_venta;
            $texto = $producto->vNombre . ' - $' . number_format($precioMostrar, 2);
            
            if ($tieneDescuento) {
                $texto .= ' (¡DESCUENTO!)';
            }
            
            $sugerencias[] = [
                'id' => $producto->id_producto,
                'text' => $texto,
                'url' => route('productos.show.public', $producto->id_producto),
                'en_descuento' => $tieneDescuento
            ];
        }

        return response()->json($sugerencias);
    }

    public function buscarProductos(Request $request)
    {
        $term = $request->get('term');
        
        if (!$term) {
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