<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use App\Models\AtributoOpcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtributoController extends Controller
{
    public function index()
    {
        $atributos = Atributo::with('opciones')->orderBy('iOrden')->get();
        return view('atributos.index', compact('atributos'));
    }

    public function create()
    {
        $tipos = [
            'texto' => 'texto',
            'textarea' => 'textarea',
            'select' => 'select',
            'radio' => 'radio button',
            'checkbox' => 'checkbox',
            'archivo' => 'Archivo'
        ];
        
        return view('atributos.create', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre',
            'tDescripcion' => 'nullable',
            'eTipo' => 'required|in:texto,textarea,select,radio,checkbox,archivo',
            'vLabel' => 'nullable|max:100',
            'vPlaceholder' => 'nullable|max:100',
            'bRequerido' => 'boolean',
            'iOrden' => 'integer|min:0',
            'bActivo' => 'boolean',
            'opciones' => 'required_if:eTipo,select,radio,checkbox|array|min:1',
            'opciones.*.vValor' => 'required_if:eTipo,select,radio,checkbox|max:100',
            'opciones.*.vEtiqueta' => 'required_if:eTipo,select,radio,checkbox|max:100',
        ], [
            'vNombre.required' => 'El nombre del atributo es obligatorio',
            'vNombre.unique' => 'Ya existe un atributo con este nombre',
            'eTipo.required' => 'Debe seleccionar un tipo de campo',
            'opciones.required_if' => 'Debe agregar al menos una opción para este tipo de campo',
            'opciones.min' => 'Debe agregar al menos una opción',
            'opciones.*.vValor.required_if' => 'El valor de la opción es obligatorio',
            'opciones.*.vEtiqueta.required_if' => 'La etiqueta de la opción es obligatoria',
        ]);

        try {
            DB::beginTransaction();

            $atributo = Atributo::create([
                'vNombre' => $request->vNombre,
                'tDescripcion' => $request->tDescripcion,
                'eTipo' => $request->eTipo,
                'vLabel' => $request->vLabel,
                'vPlaceholder' => $request->vPlaceholder,
                'bRequerido' => $request->boolean('bRequerido'),
                'iOrden' => $request->iOrden ?? 0,
                'bActivo' => $request->boolean('bActivo')
            ]);

            // Crear opciones si el tipo lo requiere
            if (in_array($request->eTipo, ['select', 'radio', 'checkbox']) && $request->has('opciones')) {
                foreach ($request->opciones as $index => $opcionData) {
                    // Validar que ambos campos estén llenos
                    if (!empty($opcionData['vValor']) && !empty($opcionData['vEtiqueta'])) {
                        AtributoOpcion::create([
                            'id_atributo' => $atributo->id_atributo,
                            'vValor' => $opcionData['vValor'],
                            'vEtiqueta' => $opcionData['vEtiqueta'],
                            'bPredeterminado' => $opcionData['bPredeterminado'] ?? false,
                            'iOrden' => $index
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('atributos.index')
                ->with('success', 'Atributo creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el atributo: ' . $e->getMessage()]);
        }
    }

    public function show(Atributo $atributo)
    {
        $atributo->load('opciones');
        return view('atributos.show', compact('atributo'));
    }

    public function edit(Atributo $atributo)
    {
        $tipos = [
            'texto' => 'Campo de Texto',
            'textarea' => 'Área de Texto',
            'select' => 'Lista Desplegable',
            'radio' => 'Botones de Radio',
            'checkbox' => 'Casilla de Verificación',
            'archivo' => 'Archivo'
        ];
        
        $atributo->load('opciones');
        return view('atributos.edit', compact('atributo', 'tipos'));
    }

    public function update(Request $request, Atributo $atributo)
    {
        $request->validate([
            'vNombre' => 'required|max:100|unique:tbl_atributos,vNombre,' . $atributo->id_atributo . ',id_atributo',
            'tDescripcion' => 'nullable',
            'eTipo' => 'required|in:texto,textarea,select,radio,checkbox,archivo',
            'vLabel' => 'nullable|max:100',
            'vPlaceholder' => 'nullable|max:100',
            'bRequerido' => 'boolean',
            'iOrden' => 'integer|min:0',
            'bActivo' => 'boolean',
            'opciones' => 'required_if:eTipo,select,radio,checkbox|array',
            'opciones.*.vValor' => 'required_if:eTipo,select,radio,checkbox|max:100',
            'opciones.*.vEtiqueta' => 'required_if:eTipo,select,radio,checkbox|max:100',
        ]);

        try {
            DB::beginTransaction();

            $atributo->update([
                'vNombre' => $request->vNombre,
                'tDescripcion' => $request->tDescripcion,
                'eTipo' => $request->eTipo,
                'vLabel' => $request->vLabel,
                'vPlaceholder' => $request->vPlaceholder,
                'bRequerido' => $request->boolean('bRequerido'),
                'iOrden' => $request->iOrden ?? 0,
                'bActivo' => $request->boolean('bActivo')
            ]);

            // Actualizar opciones si el tipo lo requiere
            if (in_array($request->eTipo, ['select', 'radio', 'checkbox'])) {
                // Eliminar opciones existentes
                $atributo->opciones()->delete();
                
                // Crear nuevas opciones
                if ($request->has('opciones')) {
                    foreach ($request->opciones as $index => $opcionData) {
                        AtributoOpcion::create([
                            'id_atributo' => $atributo->id_atributo,
                            'vValor' => $opcionData['vValor'],
                            'vEtiqueta' => $opcionData['vEtiqueta'],
                            'bPredeterminado' => $opcionData['bPredeterminado'] ?? false,
                            'iOrden' => $index
                        ]);
                    }
                }
            } else {
                // Si cambió de tipo a uno que no requiere opciones, eliminarlas
                $atributo->opciones()->delete();
            }

            DB::commit();

            return redirect()->route('atributos.index')
                ->with('success', 'Atributo actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el atributo: ' . $e->getMessage()]);
        }
    }

    public function destroy(Atributo $atributo)
    {
        try {
            DB::beginTransaction();

            // Eliminar relaciones con productos primero
            DB::table('tbl_producto_atributos')->where('id_atributo', $atributo->id_atributo)->delete();
            $atributo->opciones()->delete();
            $atributo->delete();

            DB::commit();

            return redirect()->route('atributos.index')
                ->with('success', 'Atributo eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('atributos.index')
                ->with('error', 'Error al eliminar el atributo: ' . $e->getMessage());
        }
    }
}