<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;

class ConsultaPedidoController extends Controller
{
    public function form()
    {
        return view('pedidos.consulta');
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'id_pedido' => 'required|integer',
            'email'     => 'required|email',
        ]);

        $pedido = Pedido::where('id_pedido', $request->id_pedido)
            ->where('vEmail', $request->email)
            ->with([
                'detalles.producto',
                'venta',
                'envio',
                'ultimaSolicitudPostventa'
            ])
            ->first();

        if (!$pedido) {
            return back()
                ->withErrors([
                    'general' => 'No fue posible validar la información proporcionada.'
                ])
                ->withInput();
        }

        return view('pedidos.show_public', compact('pedido'));
    }
}
