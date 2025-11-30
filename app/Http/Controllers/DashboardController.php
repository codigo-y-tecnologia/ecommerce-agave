<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use Illuminate\Support\Facades\File;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;

class DashboardController extends Controller
{

    public function index()
    {
        // // Traer productos activos
        // $productos = Producto::with(['marca', 'categoria', 'etiquetas'])
        //                 ->where('bActivo', 1)
        //                 ->get();
        
        // // Obtener imágenes para cada producto - CORREGIDO
        // foreach ($productos as $producto) {
        //     $carpetaProducto = public_path('images/productos/' . $producto->vCodigo_barras);
        //     $imagenes = [];
            
        //     if (File::exists($carpetaProducto)) {
        //         $archivos = File::files($carpetaProducto);
        //         foreach ($archivos as $archivo) {
        //             $extension = strtolower($archivo->getExtension());
        //             if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        //                 $imagenes[] = $archivo->getFilename();
        //             }
        //         }
        //         // Ordenar imágenes por nombre numéricamente
        //         natsort($imagenes);
        //         $imagenes = array_values($imagenes);
        //     }
            
        //     // Agregar imágenes al producto
        //     $producto->imagenes = $imagenes;
        // }

        $categorias = Categoria::with('childrenRecursive')
                              ->padres()
                              ->activas()
                              ->ordenadas()
                              ->get();
                              
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        
        // Obtener productos para mostrar en la página de inicio
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])
                            ->where('bActivo', true)
                            ->orderBy('vNombre')
                            ->limit(12)
                            ->get();

        // Si el usuario está autenticado, lo redirigimos a su panel correspondiente
        if (Auth::check()) {
            $rol = Auth::user()->eRol;

            switch ($rol) {
                case 'cliente':
                    return view('dashboards.cliente', compact('categorias', 'marcas', 'etiquetas', 'productos'));
                case 'admin':
                    return view('dashboards.admin');
                case 'superadmin':
                    return view('dashboards.superadmin');
                default:
                    return view('dashboards.cliente', compact('categorias', 'marcas', 'etiquetas', 'productos'));
            }
        }

        // Si no está autenticado, mostramos la vista pública (cliente como visitante)
        return view('dashboards.cliente', compact('categorias', 'marcas', 'etiquetas', 'productos'));
    }

    public function cliente()
    {
        // // Traer productos activos
        // $productos = Producto::with(['marca', 'categoria', 'etiquetas'])
        //                 ->where('bActivo', 1)
        //                 ->get();
        
        // // Obtener imágenes para cada producto - CORREGIDO
        // foreach ($productos as $producto) {
        //     $carpetaProducto = public_path('images/productos/' . $producto->vCodigo_barras);
        //     $imagenes = [];
            
        //     if (File::exists($carpetaProducto)) {
        //         $archivos = File::files($carpetaProducto);
        //         foreach ($archivos as $archivo) {
        //             $extension = strtolower($archivo->getExtension());
        //             if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        //                 $imagenes[] = $archivo->getFilename();
        //             }
        //         }
        //         // Ordenar imágenes por nombre numéricamente
        //         natsort($imagenes);
        //         $imagenes = array_values($imagenes);
        //     }
            
        //     // Agregar imágenes al producto
        //     $producto->imagenes = $imagenes;
        // }

        $categorias = Categoria::with('childrenRecursive')
                              ->padres()
                              ->activas()
                              ->ordenadas()
                              ->get();
                              
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        
        // Obtener productos para mostrar en la página de inicio
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])
                            ->where('bActivo', true)
                            ->orderBy('vNombre')
                            ->limit(12)
                            ->get();

        return view('dashboards.cliente', compact('categorias', 'marcas', 'etiquetas', 'productos'));
    }

    public function admin()
    {
        return view('dashboards.admin');
    }

    public function superadmin()
    {
        return view('dashboards.superadmin');
    }
}
