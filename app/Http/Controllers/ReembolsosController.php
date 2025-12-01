<?php

namespace App\Http\Controllers;

use App\Models\reembolsos;
use Illuminate\Http\Request;

class ReembolsosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Crear consulta
        $query = reembolsos::query();
        
        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_reembolso', 'like', "%{$search}%")
                  ->orWhere('id_venta', 'like', "%{$search}%")
                  ->orWhere('vMotivo', 'like', "%{$search}%");
            });
        }
        
        // Paginar resultados
        $reembolsos = $query->orderBy('id_reembolso', 'desc')->paginate(10);
        
        return view('reembolsos.index', compact('reembolsos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reembolsos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_venta' => 'required|integer',
            'tFecha_reembolso' => 'required|date',
            'dMonto' => 'required|numeric|min:0',
            'vMotivo' => 'nullable|string|max:255',
            'eMetodo_pago' => 'required|in:paypal,stripe,tarjeta,transferencia',
            'eEstado' => 'required|in:pendiente,procesado,completado,fallido'
        ]);
        
        reembolsos::create($request->all());
        
        return redirect()->route('reembolsos.index')
                         ->with('success', 'Reembolso creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reembolso = reembolsos::findOrFail($id);
        return view('reembolsos.show', compact('reembolso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $reembolso = reembolsos::findOrFail($id);
        return view('reembolsos.edit', compact('reembolso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $reembolso = reembolsos::findOrFail($id);
        
        $request->validate([
            'id_venta' => 'required|integer',
            'tFecha_reembolso' => 'required|date',
            'dMonto' => 'required|numeric|min:0',
            'vMotivo' => 'nullable|string|max:255',
            'eMetodo_pago' => 'required|in:paypal,stripe,tarjeta,transferencia',
            'eEstado' => 'required|in:pendiente,procesado,completado,fallido'
        ]);
        
        $reembolso->update($request->all());
        
        return redirect()->route('reembolsos.show', $reembolso->id_reembolso)
                         ->with('success', 'Reembolso actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reembolso = reembolsos::findOrFail($id);
        $reembolso->delete();
        
        return redirect()->route('reembolsos.index')
                         ->with('success', 'Reembolso eliminado correctamente');
    }
}