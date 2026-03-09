<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Etiqueta;
use App\Models\Atributo;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        // CATEGORÍAS SIMPLES - SIN JERARQUÍA
        $categorias = Categoria::orderBy('vNombre', 'asc')->get();
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();

        return view('productos.create', compact('categorias', 'marcas', 'etiquetas'));
    }

    public function store(Request $request)
    {
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
            'dPrecio_compra' => ['nullable', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'dPrecio_venta' => ['required', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'iStock' => ['required', 'regex:/^[0-9]+$/'],
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagenes' => 'nullable|array|max:6',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048'
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
            'id_marca.required' => 'La marca es obligatoria',

            'imagenes.max' => 'No puedes subir más de 6 imágenes',
            'imagenes.*.image' => 'Cada archivo debe ser una imagen válida',
            'imagenes.*.mimes' => 'Las imágenes deben ser de tipo: jpg, jpeg, png, gif o webp',
            'imagenes.*.max' => 'Cada imagen no debe pesar más de 2MB'
        ]);

        try {
            DB::beginTransaction();

            $productoData = $request->all();
            $productoData['bActivo'] = $request->has('bActivo');

            // Crear el producto
            $producto = Producto::create($productoData);

            // Guardar imágenes si se enviaron
            if ($request->hasFile('imagenes')) {
                $producto->guardarImagenes($request->file('imagenes'));
            }

            // Sincronizar etiquetas
            if ($request->has('etiquetas')) {
                $producto->etiquetas()->sync($request->etiquetas);
            }

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
    }

    public function showPublic($id)
    {
        $producto = Producto::with(['marca', 'categoria', 'etiquetas'])
            ->where('bActivo', true)
            ->findOrFail($id);

        return view('productos.show-public', compact('producto'));
    }

    public function catalogo()
    {
        $productos = Producto::with(['marca', 'categoria', 'etiquetas'])
            ->where('bActivo', true)
            ->orderBy('vNombre')
            ->get();

        return view('productos.catalogo', compact('productos'));
    }

    public function show(Producto $producto)
    {
        $producto->load(['marca', 'categoria', 'etiquetas']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        // CATEGORÍAS SIMPLES - SIN JERARQUÍA
        $categorias = Categoria::orderBy('vNombre', 'asc')->get();
        $marcas = Marca::all();
        $etiquetas = Etiqueta::all();
        $producto->load('etiquetas');

        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'etiquetas'));
    }

    public function update(Request $request, Producto $producto)
    {
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
            'dPrecio_compra' => ['nullable', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'dPrecio_venta' => ['required', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'],
            'iStock' => ['required', 'regex:/^[0-9]+$/'],
            'id_categoria' => 'required|exists:tbl_categorias,id_categoria',
            'id_marca' => 'required|exists:tbl_marcas,id_marca',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'exists:tbl_etiquetas,id_etiqueta',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048'
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
            'id_marca.required' => 'La marca es obligatoria',

            'imagenes.*.image' => 'Cada archivo debe ser una imagen válida',
            'imagenes.*.mimes' => 'Las imágenes deben ser de tipo: jpg, jpeg, png, gif o webp',
            'imagenes.*.max' => 'Cada imagen no debe pesar más de 2MB'
        ]);

        try {
            DB::beginTransaction();

            // Validar que no se excedan 6 imágenes en total
            $imagenesActuales = $producto->getNumeroImagenes();
            $nuevasImagenes = $request->hasFile('imagenes') ? count($request->file('imagenes')) : 0;

            if (($imagenesActuales + $nuevasImagenes) > 6) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['imagenes' => 'No puedes tener más de 6 imágenes. Actualmente tienes ' . $imagenesActuales . ' imágenes.']);
            }

            $productoData = $request->all();
            $productoData['bActivo'] = $request->has('bActivo') ? true : false;

            $producto->update($productoData);

            // Guardar nuevas imágenes si se enviaron
            if ($request->hasFile('imagenes')) {
                $producto->guardarImagenes($request->file('imagenes'));
            }

            $producto->etiquetas()->sync($request->etiquetas ?? []);

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()]);
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            DB::beginTransaction();

            // Eliminar imágenes del producto
            $producto->eliminarImagenes();

            $producto->etiquetas()->detach();
            $producto->delete();

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Gestionar atributos del producto
     */
    public function atributos(Producto $producto)
    {
        $atributosDisponibles = Atributo::where('bActivo', true)
            ->whereNotIn('id_atributo', function ($query) use ($producto) {
                $query->select('id_atributo')
                    ->from('tbl_producto_atributos')
                    ->where('id_producto', $producto->id_producto);
            })
            ->orderBy('iOrden')
            ->get();

        $producto->load('productoAtributos.atributo', 'productoAtributos.opcion');

        return view('productos.atributos', compact('producto', 'atributosDisponibles'));
    }
}
