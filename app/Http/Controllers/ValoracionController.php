<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoVariacion;
use App\Models\VariacionAtributo;
use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ValoracionController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['variaciones.atributos.valor', 'variaciones.atributos.atributo', 'marca', 'categoria'])
            ->whereHas('variaciones')
            ->orderBy('vNombre')
            ->get();
            
        return view('valoraciones.index', compact('productos'));
    }

    public function show($id)
    {
        $producto = Producto::with(['variaciones.atributos.valor', 'variaciones.atributos.atributo', 'marca', 'categoria'])
            ->findOrFail($id);
            
        return view('valoraciones.show', compact('producto'));
    }

    public function create($producto_id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($producto_id);
        
        // Verificar si el producto tiene atributos asignados
        if ($producto->valoresAtributos->count() === 0) {
            return redirect()->route('valoraciones.show', $producto->id_producto)
                ->with('warning', 'Primero debes asignar atributos al producto desde la página de edición.');
        }
        
        // Agrupar atributos por nombre
        $atributos = [];
        foreach ($producto->valoresAtributos as $valor) {
            $nombreAtributo = $valor->atributo->vNombre;
            if (!isset($atributos[$nombreAtributo])) {
                $atributos[$nombreAtributo] = [];
            }
            $atributos[$nombreAtributo][] = $valor;
        }
        
        return view('valoraciones.create', compact('producto', 'atributos'));
    }

    public function store(Request $request, $producto_id)
    {
        $request->validate([
            'vSKU' => 'required|unique:tbl_producto_variaciones,vSKU',
            'vCodigo_barras' => 'nullable|max:50',
            'dPrecio' => 'required|numeric|min:0',
            'dPrecio_oferta' => 'nullable|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'dPeso' => 'nullable|numeric|min:0',
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor'
        ]);

        try {
            DB::beginTransaction();

            $variacion = ProductoVariacion::create([
                'id_producto' => $producto_id,
                'vSKU' => $request->vSKU,
                'vCodigo_barras' => $request->vCodigo_barras,
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso,
                'vClase_envio' => $request->vClase_envio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo')
            ]);

            // Guardar imagen si se envió
            if ($request->hasFile('imagen')) {
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            }

            // Guardar atributos de la valoración
            foreach ($request->atributos as $atributo_id => $valor_id) {
                VariacionAtributo::create([
                    'id_variacion' => $variacion->id_variacion,
                    'id_atributo' => $atributo_id,
                    'id_atributo_valor' => $valor_id
                ]);
            }

            DB::commit();

            return redirect()->route('valoraciones.show', $producto_id)
                ->with('success', 'Valoración creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear valoración: ' . $e->getMessage()]);
        }
    }

    public function edit($producto_id, $variacion_id)
    {
        $producto = Producto::with(['valoresAtributos.atributo'])->findOrFail($producto_id);
        $variacion = ProductoVariacion::with('atributos.valor', 'atributos.atributo')
            ->findOrFail($variacion_id);
        
        // Agrupar atributos por nombre
        $atributos = [];
        foreach ($producto->valoresAtributos as $valor) {
            $nombreAtributo = $valor->atributo->vNombre;
            if (!isset($atributos[$nombreAtributo])) {
                $atributos[$nombreAtributo] = [];
            }
            $atributos[$nombreAtributo][] = $valor;
        }
        
        // Marcar los valores seleccionados
        $valoresSeleccionados = [];
        foreach ($variacion->atributos as $atributoVariacion) {
            $valoresSeleccionados[$atributoVariacion->id_atributo] = $atributoVariacion->id_atributo_valor;
        }
        
        return view('valoraciones.edit', compact('producto', 'variacion', 'atributos', 'valoresSeleccionados'));
    }

    public function update(Request $request, $producto_id, $variacion_id)
    {
        $request->validate([
            'vSKU' => 'required|unique:tbl_producto_variaciones,vSKU,' . $variacion_id . ',id_variacion',
            'vCodigo_barras' => 'nullable|max:50',
            'dPrecio' => 'required|numeric|min:0',
            'dPrecio_oferta' => 'nullable|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'dPeso' => 'nullable|numeric|min:0',
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor'
        ]);

        try {
            DB::beginTransaction();

            $variacion = ProductoVariacion::findOrFail($variacion_id);
            
            $variacion->update([
                'vSKU' => $request->vSKU,
                'vCodigo_barras' => $request->vCodigo_barras,
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso,
                'vClase_envio' => $request->vClase_envio,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo')
            ]);

            // Guardar imagen si se envió
            if ($request->hasFile('imagen')) {
                $this->eliminarImagenVariacion($variacion);
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            }

            // Eliminar atributos existentes y guardar nuevos
            $variacion->atributos()->delete();
            
            foreach ($request->atributos as $atributo_id => $valor_id) {
                VariacionAtributo::create([
                    'id_variacion' => $variacion->id_variacion,
                    'id_atributo' => $atributo_id,
                    'id_atributo_valor' => $valor_id
                ]);
            }

            DB::commit();

            return redirect()->route('valoraciones.show', $producto_id)
                ->with('success', 'Valoración actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar valoración: ' . $e->getMessage()]);
        }
    }

    public function destroy($producto_id, $variacion_id)
    {
        try {
            DB::beginTransaction();

            $variacion = ProductoVariacion::findOrFail($variacion_id);
            
            // Eliminar imagen
            $this->eliminarImagenVariacion($variacion);
            
            // Eliminar atributos
            $variacion->atributos()->delete();
            
            // Eliminar valoración
            $variacion->delete();

            DB::commit();

            return redirect()->route('valoraciones.show', $producto_id)
                ->with('success', 'Valoración eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('valoraciones.show', $producto_id)
                ->with('error', 'Error al eliminar valoración: ' . $e->getMessage());
        }
    }

    private function guardarImagenVariacion($variacion, $imagen)
    {
        $carpeta = 'variaciones/' . $variacion->id_variacion;
        
        if (!Storage::disk('public')->exists($carpeta)) {
            Storage::disk('public')->makeDirectory($carpeta);
        }
        
        $nombreArchivo = 'imagen.' . $imagen->getClientOriginalExtension();
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        $variacion->vImagen = Storage::url($ruta);
        $variacion->save();
    }

    private function eliminarImagenVariacion($variacion)
    {
        if ($variacion->vImagen) {
            $carpeta = 'variaciones/' . $variacion->id_variacion;
            if (Storage::disk('public')->exists($carpeta)) {
                Storage::disk('public')->deleteDirectory($carpeta);
            }
            $variacion->vImagen = null;
            $variacion->save();
        }
    }
}