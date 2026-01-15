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
        $productos = Producto::with(['marca', 'categoria', 'variaciones.atributos.valor'])
            ->whereHas('valoresAtributos')
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
        
        if ($producto->valoresAtributos->count() === 0) {
            return redirect()->route('valoraciones.show', $producto->id_producto)
                ->with('warning', 'Primero debes asignar atributos al producto desde la página de edición.');
        }
        
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
            'dPrecio' => 'required|numeric|min:0',
            'dPrecio_oferta' => 'nullable|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'dPeso' => 'nullable|numeric|min:0',
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,gif,webp,bmp,svg',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor'
        ], [
            'imagen.image' => 'El archivo debe ser una imagen válida',
            'imagen.max' => 'La imagen no debe pesar más de 5MB',
            'imagen.mimes' => 'Formatos aceptados: JPG, JPEG, PNG, GIF, WebP, BMP, SVG',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'dPrecio.required' => 'El precio es obligatorio',
            'iStock.required' => 'El stock es obligatorio',
        ]);

        try {
            DB::beginTransaction();

            $productoPadre = Producto::find($producto_id);
            
            $variacionData = [
                'id_producto' => $producto_id,
                'vSKU' => $request->vSKU,
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo')
            ];

            if ($request->filled('vClase_envio')) {
                $variacionData['vClase_envio'] = $request->vClase_envio;
            } elseif ($productoPadre && $productoPadre->vClase_envio) {
                $variacionData['vClase_envio'] = $productoPadre->vClase_envio;
            } else {
                $variacionData['vClase_envio'] = 'Estándar';
            }

            $variacion = ProductoVariacion::create($variacionData);

            // Guardar imagen si se envió
            if ($request->hasFile('imagen')) {
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            }

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
        
        $atributos = [];
        foreach ($producto->valoresAtributos as $valor) {
            $nombreAtributo = $valor->atributo->vNombre;
            if (!isset($atributos[$nombreAtributo])) {
                $atributos[$nombreAtributo] = [];
            }
            $atributos[$nombreAtributo][] = $valor;
        }
        
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
            'dPrecio' => 'required|numeric|min:0',
            'dPrecio_oferta' => 'nullable|numeric|min:0',
            'iStock' => 'required|integer|min:0',
            'dPeso' => 'nullable|numeric|min:0',
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,gif,webp,bmp,svg',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor'
        ], [
            'imagen.image' => 'El archivo debe ser una imagen válida',
            'imagen.max' => 'La imagen no debe pesar más de 5MB',
            'imagen.mimes' => 'Formatos aceptados: JPG, JPEG, PNG, GIF, WebP, BMP, SVG',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'dPrecio.required' => 'El precio es obligatorio',
            'iStock.required' => 'El stock es obligatorio',
        ]);

        try {
            DB::beginTransaction();

            $variacion = ProductoVariacion::findOrFail($variacion_id);
            $productoPadre = Producto::find($producto_id);
            
            $updateData = [
                'vSKU' => $request->vSKU,
                'dPrecio' => $request->dPrecio,
                'dPrecio_oferta' => $request->dPrecio_oferta,
                'iStock' => $request->iStock,
                'dPeso' => $request->dPeso,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo')
            ];

            if ($request->filled('vClase_envio')) {
                $updateData['vClase_envio'] = $request->vClase_envio;
            } else {
                $updateData['vClase_envio'] = $variacion->vClase_envio ?: 
                    ($productoPadre->vClase_envio ?: 'Estándar');
            }

            $variacion->update($updateData);

            // Manejo de imagen mejorado
            if ($request->hasFile('imagen')) {
                // Si se sube nueva imagen, reemplazar
                $this->eliminarImagenVariacion($variacion);
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            } elseif ($request->has('mantener_imagen') && $request->mantener_imagen == '1') {
                // Si se marca "mantener imagen", no hacer nada (mantener la existente)
                // No se elimina ni se cambia la imagen
            } else {
                // Si no se especifica nada, mantener la imagen existente
                // No hacer nada
            }

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
            
            $this->eliminarImagenVariacion($variacion);
            $variacion->atributos()->delete();
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
        
        // Obtener la extensión original del archivo
        $extension = strtolower($imagen->getClientOriginalExtension());
        
        // Lista de extensiones de imagen válidas
        $extensionesValidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'tiff', 'ico', 'heic', 'heif'];
        
        // Si no hay extensión o no es válida, usar jpg como predeterminado
        if (!$extension || !in_array($extension, $extensionesValidas)) {
            $extension = 'jpg';
        }
        
        // Generar nombre único para evitar colisiones
        $nombreArchivo = 'imagen_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        $variacion->vImagen = Storage::url($ruta);
        $variacion->save();
    }

    private function eliminarImagenVariacion($variacion)
    {
        if ($variacion->vImagen) {
            // Extraer la ruta relativa del URL completo
            $urlBase = Storage::url('');
            $rutaRelativa = str_replace($urlBase, '', $variacion->vImagen);
            
            // Eliminar el archivo específico
            if (Storage::disk('public')->exists($rutaRelativa)) {
                Storage::disk('public')->delete($rutaRelativa);
            }
            
            // Intentar eliminar la carpeta si está vacía
            $carpeta = 'variaciones/' . $variacion->id_variacion;
            if (Storage::disk('public')->exists($carpeta)) {
                // Verificar si la carpeta está vacía
                $archivos = Storage::disk('public')->files($carpeta);
                if (empty($archivos)) {
                    Storage::disk('public')->deleteDirectory($carpeta);
                }
            }
            
            $variacion->vImagen = null;
            $variacion->save();
        }
    }
}