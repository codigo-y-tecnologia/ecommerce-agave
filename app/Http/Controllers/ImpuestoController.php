<?php

namespace App\Http\Controllers;

use App\Models\Impuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ImpuestoController extends Controller
{
    /**
     * Mostrar listado de impuestos
     */
    public function index()
    {
        $impuestos = Impuesto::orderBy('vNombre')->get();
        return view('impuestos.index', compact('impuestos'));
    }

    /**
     * Mostrar formulario para crear impuesto
     */
    public function create()
    {
        return view('impuestos.create');
    }

    /**
     * Guardar nuevo impuesto
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_impuestos,vNombre',
            'eTipo' => 'required|in:IVA,ISR,IEPS', // Permitir IEPS también
            'dPorcentaje' => 'required|numeric|min:0|max:100',
            'bActivo' => 'nullable|boolean'
        ], [
            'vNombre.required' => 'El nombre del impuesto es obligatorio',
            'vNombre.unique' => 'Ya existe un impuesto con este nombre',
            'eTipo.required' => 'Debes seleccionar un tipo de impuesto',
            'eTipo.in' => 'El tipo de impuesto debe ser IVA, ISR o IEPS',
            'dPorcentaje.required' => 'El porcentaje es obligatorio',
            'dPorcentaje.numeric' => 'El porcentaje debe ser un número válido',
            'dPorcentaje.min' => 'El porcentaje no puede ser negativo',
            'dPorcentaje.max' => 'El porcentaje no puede ser mayor a 100'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $impuesto = Impuesto::create([
                'vNombre' => $request->vNombre,
                'eTipo' => $request->eTipo,
                'dPorcentaje' => $request->dPorcentaje,
                'bActivo' => $request->has('bActivo') ? true : false
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Impuesto creado exitosamente',
                    'impuesto' => $impuesto
                ]);
            }

            return redirect()->route('impuestos.index')
                ->with('success', 'Impuesto creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear impuesto: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear impuesto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear impuesto: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles de un impuesto
     */
    public function show(Impuesto $impuesto)
    {
        $impuesto->load('productos', 'variaciones');
        return view('impuestos.show', compact('impuesto'));
    }

    /**
     * Mostrar formulario para editar impuesto
     */
    public function edit(Impuesto $impuesto)
    {
        return view('impuestos.edit', compact('impuesto'));
    }

    /**
     * Actualizar impuesto
     */
    public function update(Request $request, Impuesto $impuesto)
    {
        $validator = Validator::make($request->all(), [
            'vNombre' => 'required|max:100|unique:tbl_impuestos,vNombre,' . $impuesto->id_impuesto . ',id_impuesto',
            'eTipo' => 'required|in:IVA,ISR,IEPS',
            'dPorcentaje' => 'required|numeric|min:0|max:100',
            'bActivo' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $impuesto->update([
                'vNombre' => $request->vNombre,
                'eTipo' => $request->eTipo,
                'dPorcentaje' => $request->dPorcentaje,
                'bActivo' => $request->has('bActivo') ? true : false
            ]);

            foreach ($impuesto->productos as $producto) {
                $producto->recalcularPrecioFinal();
            }

            return redirect()->route('impuestos.show', $impuesto)
                ->with('success', 'Impuesto actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar impuesto: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar impuesto: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar impuesto
     */
    public function destroy(Impuesto $impuesto)
    {
        try {
            if ($impuesto->productos()->count() > 0 || $impuesto->variaciones()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el impuesto porque tiene productos o variaciones asociados');
            }

            $impuesto->delete();

            return redirect()->route('impuestos.index')
                ->with('success', 'Impuesto eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar impuesto: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar impuesto: ' . $e->getMessage());
        }
    }

    /**
     * Creación rápida desde AJAX
     */
    public function quickCreate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'vNombre' => 'required|max:100',
        'eTipo' => 'required|in:IVA,IEPS', // SOLO IVA e IEPS
        'dPorcentaje' => 'required|numeric|min:0|max:100',
        'bActivo' => 'nullable|boolean'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $impuesto = Impuesto::create([
            'vNombre' => $request->vNombre,
            'eTipo' => $request->eTipo,
            'dPorcentaje' => $request->dPorcentaje,
            'bActivo' => $request->has('bActivo') ? true : false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Impuesto creado exitosamente',
            'impuesto' => $impuesto
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al crear impuesto: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Obtener impuestos en formato JSON
     */
    public function getJson()
    {
        $impuestos = Impuesto::where('bActivo', true)
            ->orderBy('vNombre')
            ->get(['id_impuesto', 'vNombre', 'eTipo', 'dPorcentaje']);

        return response()->json([
            'success' => true,
            'impuestos' => $impuestos
        ]);
    }
}