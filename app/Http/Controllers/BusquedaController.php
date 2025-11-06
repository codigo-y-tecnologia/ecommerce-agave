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
                      $catQuery->where('vNombre', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('marca', function($brandQuery) use ($searchTerm) {
                      $brandQuery->where('vNombre', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('etiquetas', function($tagQuery) use ($searchTerm) {
                      $tagQuery->where('vNombre', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Filtros
        if ($request->has('categoria') && !empty($request->categoria)) {
            $query->whereHas('categoria', function($q) use ($request) {
                $q->where('id_categoria', $request->categoria);
            });
        }

        if ($request->has('marca') && !empty($request->marca)) {
            $query->whereHas('marca', function($q) use ($request) {
                $q->where('id_marca', $request->marca);
            });
        }

        if ($request->has('etiqueta') && !empty($request->etiqueta)) {
            $query->whereHas('etiquetas', function($q) use ($request) {
                $q->where('id_etiqueta', $request->etiqueta);
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
                $query->orderBy('tFecha_registro', 'desc');
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
                      $catQuery->where('vNombre', 'LIKE', "%{$term}%");
                  });
            })
            ->take(5)
            ->get(['id_producto', 'vNombre', 'dPrecio_venta']);

        $sugerencias = [];
        foreach ($productos as $producto) {
            $sugerencias[] = [
                'id' => $producto->id_producto,
                'text' => $producto->vNombre . ' - $' . number_format($producto->dPrecio_venta, 2),
                'url' => route('productos.show.public', $producto->id_producto)
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
            ->get(['id_producto', 'vNombre', 'dPrecio_venta']);

        return response()->json($productos);
    }
}