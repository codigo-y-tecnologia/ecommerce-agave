<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])->get();
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categorias = Categoria::all();
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        return view('productos.create', compact('categorias', 'marcas', 'etiquetas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'vCodigo_barras' => 'required|max:20|unique:tbl_productos,vCodigo_barras',
            'vNombre' => 'required|max:100',
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',
            'dPrecio_compra' => 'nullable|numeric|min:0',
            'dPrecio_venta' => 'required|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta'
        ], [
            'vCodigo_barras.required' => 'El código de barras es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este código de barras',
            'vNombre.required' => 'El nombre del producto es obligatorio',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.min' => 'El precio de venta debe ser mayor o igual a 0',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.min' => 'El stock debe ser mayor o igual a 0',
            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria'
        ]);

        $producto = Producto::create($request->all());
        
        if ($request->has('etiquetas')) {
            $producto->etiquetas()->sync($request->etiquetas);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');

    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
         $producto->load(['marca', 'categoria', 'etiquetas']);
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
        $categorias = Categoria::all();
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        $producto->load('etiquetas');
        
        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'etiquetas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        //
         $request->validate([
            'vCodigo_barras' => 'required|max:20|unique:tbl_productos,vCodigo_barras,' . $producto->id_producto . ',id_producto',
            'vNombre' => 'required|max:100',
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',
            'dPrecio_compra' => 'nullable|numeric|min:0',
            'dPrecio_venta' => 'required|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta'
        ]);

        $producto->update($request->all());
        
        $producto->etiquetas()->sync($request->etiquetas ?? []);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        //
        $producto->etiquetas()->detach();
        $producto->delete();
        
        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
            
    }
}
