<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;
use Illuminate\Validation\Rule;

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
            'vCodigo_barras' => [
                'required',
                'max:20',
                'unique:tbl_productos,vCodigo_barras',
                'regex:/^[0-9]+$/'
            ],
            'vNombre' => [
                'required',
                'max:100',
                'unique:tbl_productos,vNombre'
            ],
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',

            // Permitir números con punto decimal
            'dPrecio_compra' => ['nullable', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'dPrecio_venta' => ['required', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'iStock' => ['required', 'regex:/^[0-9]+$/'],

            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta'
        ], [
            'vCodigo_barras.required' => 'El código de barras es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este código de barras',
            'vCodigo_barras.max' => 'El código de barras no puede tener más de 20 caracteres',
            'vCodigo_barras.regex' => 'El código de barras solo puede contener números (0-9)',

            'vNombre.required' => 'El nombre del producto es obligatorio',
            'vNombre.unique' => 'Ya existe un producto con este nombre',

            'dPrecio_compra.regex' => 'El precio de compra debe ser un número válido (ej: 150.50)',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.regex' => 'El precio de venta debe ser un número válido (ej: 200.75)',

            'iStock.required' => 'El stock es obligatorio',
            'iStock.regex' => 'El stock solo puede contener números enteros (0-9)',

            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria'
        ]);

        $productoData = $request->all();
        $productoData['bActivo'] = $request->has('bActivo');

        $producto = Producto::create($productoData);

        if ($request->has('etiquetas')) {
            $producto->etiquetas()->sync($request->etiquetas);
        }

        if ($request->has('atributos')) {
            session()->flash('atributos_seleccionados', $request->atributos);
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
            'vCodigo_barras' => [
                'required',
                'max:20',
                Rule::unique('tbl_productos', 'vCodigo_barras')->ignore($producto->id_producto, 'id_producto'),
                'regex:/^[0-9]+$/'
            ],
            'vNombre' => [
                'required',
                'max:100',
                Rule::unique('tbl_productos', 'vNombre')->ignore($producto->id_producto, 'id_producto')
            ],
            'tDescripcion_corta' => 'nullable|max:255',
            'tDescripcion_larga' => 'nullable',

            // Permitir números con punto decimal
            'dPrecio_compra' => ['nullable', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'dPrecio_venta' => ['required', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'iStock' => ['required', 'regex:/^[0-9]+$/'],

            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta'
        ], [
            'vCodigo_barras.required' => 'El código de barras es obligatorio',
            'vCodigo_barras.unique' => 'Ya existe un producto con este código de barras',
            'vCodigo_barras.max' => 'El código de barras no puede tener más de 20 caracteres',
            'vCodigo_barras.regex' => 'El código de barras solo puede contener números (0-9)',

            'vNombre.required' => 'El nombre del producto es obligatorio',
            'vNombre.unique' => 'Ya existe un producto con este nombre',

            'dPrecio_compra.regex' => 'El precio de compra debe ser un número válido (ej: 150.50)',
            'dPrecio_venta.required' => 'El precio de venta es obligatorio',
            'dPrecio_venta.regex' => 'El precio de venta debe ser un número válido (ej: 200.75)',

            'iStock.required' => 'El stock es obligatorio',
            'iStock.regex' => 'El stock solo puede contener números enteros (0-9)',

            'id_categoria.required' => 'La categoría es obligatoria',
            'id_marca.required' => 'La marca es obligatoria'
        ]);

         $productoData = $request->all();
        $productoData['bActivo'] = $request->has('bActivo') ? true : false;

        $producto->update($productoData);
        
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