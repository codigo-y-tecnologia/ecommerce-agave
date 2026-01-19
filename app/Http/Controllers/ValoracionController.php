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
            'dPrecio' => 'required|numeric|min:0|max:9999999.99|regex:/^\d{1,7}(\.\d{1,2})?$/',
            'dPrecio_oferta' => 'nullable|numeric|min:0|max:9999999.99|regex:/^\d{1,7}(\.\d{1,2})?$/',
            'iStock' => 'required|integer|min:0|max:999999',
            'dPeso' => 'nullable|numeric|min:0|max:1000|regex:/^\d{1,4}(\.\d{1,2})?$/',
            'dLargo_cm' => 'nullable|numeric|min:0|max:500|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'dAncho_cm' => 'nullable|numeric|min:0|max:500|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'dAlto_cm' => 'nullable|numeric|min:0|max:500|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,gif,webp,bmp,svg',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor'
        ], [
            'vSKU.required' => 'El SKU es obligatorio',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'dPrecio.required' => 'El precio es obligatorio',
            'dPrecio.regex' => 'El precio debe tener máximo 7 enteros y 2 decimales',
            'dPrecio.max' => 'El precio no puede exceder $9,999,999.99',
            'dPrecio_oferta.regex' => 'El precio de oferta debe tener máximo 7 enteros y 2 decimales',
            'dPrecio_oferta.max' => 'El precio de oferta no puede exceder $9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.max' => 'El stock no puede exceder 999,999 unidades',
            'dPeso.regex' => 'El peso debe tener máximo 4 enteros y 2 decimales',
            'dPeso.max' => 'El peso no puede exceder 1000 kg',
            'dLargo_cm.regex' => 'El largo debe tener máximo 3 enteros y 2 decimales',
            'dLargo_cm.max' => 'El largo no puede exceder 500 cm',
            'dAncho_cm.regex' => 'El ancho debe tener máximo 3 enteros y 2 decimales',
            'dAncho_cm.max' => 'El ancho no puede exceder 500 cm',
            'dAlto_cm.regex' => 'El alto debe tener máximo 3 enteros y 2 decimales',
            'dAlto_cm.max' => 'El alto no puede exceder 500 cm',
            'imagen.image' => 'El archivo debe ser una imagen válida',
            'imagen.max' => 'La imagen no debe pesar más de 5MB',
            'imagen.mimes' => 'Formatos aceptados: JPG, JPEG, PNG, GIF, WebP, BMP, SVG',
            'atributos.required' => 'Debes seleccionar valores para todos los atributos',
            'atributos.*.required' => 'Debes seleccionar un valor para cada atributo',
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
                'dLargo_cm' => $request->dLargo_cm,
                'dAncho_cm' => $request->dAncho_cm,
                'dAlto_cm' => $request->dAlto_cm,
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
            'dPrecio' => 'required|numeric|min:0|max:9999999.99|regex:/^\d{1,7}(\.\d{1,2})?$/',
            'dPrecio_oferta' => 'nullable|numeric|min:0|max:9999999.99|regex:/^\d{1,7}(\.\d{1,2})?$/',
            'iStock' => 'required|integer|min:0|max:999999',
            'dPeso' => 'nullable|numeric|min:0|max:1000|regex:/^\d{1,4}(\.\d{1,2})?$/',
            'dLargo_cm' => 'nullable|numeric|min:0|max:500|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'dAncho_cm' => 'nullable|numeric|min:0|max:500|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'dAlto_cm' => 'nullable|numeric|min:0|max:500|regex:/^\d{1,3}(\.\d{1,2})?$/',
            'vClase_envio' => 'nullable|max:50',
            'tDescripcion' => 'nullable',
            'bActivo' => 'boolean',
            'imagen' => 'nullable|image|max:5120|mimes:jpg,jpeg,png,gif,webp,bmp,svg',
            'mantener_imagen' => 'sometimes|boolean',
            'atributos' => 'required|array',
            'atributos.*' => 'required|exists:tbl_atributo_valores,id_atributo_valor'
        ], [
            'vSKU.required' => 'El SKU es obligatorio',
            'vSKU.unique' => 'Este SKU ya está registrado',
            'dPrecio.required' => 'El precio es obligatorio',
            'dPrecio.regex' => 'El precio debe tener máximo 7 enteros y 2 decimales',
            'dPrecio.max' => 'El precio no puede exceder $9,999,999.99',
            'dPrecio_oferta.regex' => 'El precio de oferta debe tener máximo 7 enteros y 2 decimales',
            'dPrecio_oferta.max' => 'El precio de oferta no puede exceder $9,999,999.99',
            'iStock.required' => 'El stock es obligatorio',
            'iStock.max' => 'El stock no puede exceder 999,999 unidades',
            'dPeso.regex' => 'El peso debe tener máximo 4 enteros y 2 decimales',
            'dPeso.max' => 'El peso no puede exceder 1000 kg',
            'dLargo_cm.regex' => 'El largo debe tener máximo 3 enteros y 2 decimales',
            'dLargo_cm.max' => 'El largo no puede exceder 500 cm',
            'dAncho_cm.regex' => 'El ancho debe tener máximo 3 enteros y 2 decimales',
            'dAncho_cm.max' => 'El ancho no puede exceder 500 cm',
            'dAlto_cm.regex' => 'El alto debe tener máximo 3 enteros y 2 decimales',
            'dAlto_cm.max' => 'El alto no puede exceder 500 cm',
            'imagen.image' => 'El archivo debe ser una imagen válida',
            'imagen.max' => 'La imagen no debe pesar más de 5MB',
            'imagen.mimes' => 'Formatos aceptados: JPG, JPEG, PNG, GIF, WebP, BMP, SVG',
            'atributos.required' => 'Debes seleccionar valores para todos los atributos',
            'atributos.*.required' => 'Debes seleccionar un valor para cada atributo',
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
                'dLargo_cm' => $request->dLargo_cm,
                'dAncho_cm' => $request->dAncho_cm,
                'dAlto_cm' => $request->dAlto_cm,
                'tDescripcion' => $request->tDescripcion,
                'bActivo' => $request->has('bActivo')
            ];

            if ($request->filled('vClase_envio')) {
                $updateData['vClase_envio'] = $request->vClase_envio;
            } else {
                $updateData['vClase_envio'] = $variacion->vClase_envio ?: 
                    ($productoPadre->vClase_envio ?: 'Estándar');
            }

            // CORRECCIÓN IMPORTANTE: Manejo de imagen
            if ($request->hasFile('imagen')) {
                // Subir nueva imagen
                $this->eliminarImagenVariacion($variacion);
                $this->guardarImagenVariacion($variacion, $request->file('imagen'));
            } else {
                // Si no se sube nueva imagen
                if ($request->has('mantener_imagen') && $request->mantener_imagen == '1') {
                    // Mantener imagen actual - no hacer nada
                } else {
                    // Eliminar imagen actual
                    $this->eliminarImagenVariacion($variacion);
                }
            }

            $variacion->update($updateData);

            // Actualizar atributos
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
        
        $extension = strtolower($imagen->getClientOriginalExtension());
        
        $extensionesValidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'tiff', 'ico', 'heic', 'heif'];
        
        if (!$extension || !in_array($extension, $extensionesValidas)) {
            $extension = 'jpg';
        }
        
        $nombreArchivo = 'imagen_' . time() . '_' . uniqid() . '.' . $extension;
        $ruta = $imagen->storeAs($carpeta, $nombreArchivo, 'public');
        
        $variacion->vImagen = Storage::url($ruta);
        $variacion->save();
    }

    private function eliminarImagenVariacion($variacion)
    {
        if ($variacion->vImagen) {
            $urlBase = Storage::url('');
            $rutaRelativa = str_replace($urlBase, '', $variacion->vImagen);
            
            if (Storage::disk('public')->exists($rutaRelativa)) {
                Storage::disk('public')->delete($rutaRelativa);
            }
            
            $carpeta = 'variaciones/' . $variacion->id_variacion;
            if (Storage::disk('public')->exists($carpeta)) {
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