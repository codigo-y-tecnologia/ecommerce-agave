<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Producto;

class CarritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario = Auth::user();

        // Buscar carrito activo del usuario
        $carrito = Carrito::where('id_usuario', $usuario->id_usuario)
            ->where('eEstado', 'activo')
            ->with('detalles.producto') // eager load para no hacer muchas queries
            ->first();

            if (!$carrito || $carrito->detalles->isEmpty()) {
            // Si no tiene carrito o no hay productos
            return view('carrito.index', [
                'detalles' => [],
                'total' => 0
            ])->with('carrito_vacio', 'Tu carrito está vacío.');
        }

        // Calcular total
        $total = $carrito->detalles->sum('subtotal');

        return view('carrito.index', [
            'detalles' => $carrito->detalles,
            'total' => $total
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
