<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\SolicitudPostventa;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Notifications\SolicitudPostventaCreada;
use App\Notifications\SolicitudPostventaCliente;

class PostventaController extends Controller
{
    /**
     * Cancelar compra (envío pendiente)
     */
    public function cancelar(Request $request, Pedido $pedido)
    {
        $request->validate([
            'motivo' => 'required|string|min:5|max:255',
        ]);

        abort_if($pedido->id_usuario !== Auth::id(), 403);
        abort_if(!$pedido->envio || $pedido->envio->eEstado !== 'pendiente', 403);
        abort_if($pedido->eEstado !== 'pagado', 403);

        // Evitar duplicados
        if (
            SolicitudPostventa::where('id_pedido', $pedido->id_pedido)
            ->where('eTipo', 'cancelacion')
            ->exists()
        ) {
            return response()->json([
                'message' => 'Ya existe una solicitud para este pedido'
            ], 409);
        }

        $solicitud = SolicitudPostventa::create([
            'id_pedido' => $pedido->id_pedido,
            'id_usuario' => Auth::id(),
            'eTipo' => 'cancelacion',
            'vMotivo' => $request->motivo,
        ]);

        // 🔔 Notificar admins
        try {
            $admins = Usuario::where('eRol', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new SolicitudPostventaCreada($solicitud));
            }

            Auth::user()->notify(new SolicitudPostventaCliente($solicitud));
        } catch (\Throwable $e) {
            report($e); // se guarda en storage/logs
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de cancelación enviada. Te notificaremos.'
        ]);
    }

    /**
     * Solicitar devolución (pedido entregado)
     */
    public function devolver(Request $request, Pedido $pedido)
    {
        $request->validate([
            'motivo' => 'required|string|min:5|max:255',
        ]);

        abort_if($pedido->id_usuario !== Auth::id(), 403);
        abort_if($pedido->eEstado !== 'entregado', 403);

        // Evitar duplicados
        if (
            SolicitudPostventa::where('id_pedido', $pedido->id_pedido)
            ->where('eTipo', 'devolucion')
            ->exists()
        ) {
            return response()->json([
                'message' => 'Ya existe una solicitud para este pedido'
            ], 409);
        }

        $solicitud = SolicitudPostventa::create([
            'id_pedido' => $pedido->id_pedido,
            'id_usuario' => Auth::id(),
            'eTipo' => 'devolucion',
            'vMotivo' => $request->motivo,
        ]);

        // 🔔 Notificar admins
        try {
            $admins = Usuario::whereIn('eRol', ['admin', 'superadmin'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new SolicitudPostventaCreada($solicitud));
            }

            Auth::user()->notify(new SolicitudPostventaCliente($solicitud));
        } catch (\Throwable $e) {
            report($e); // se guarda en storage/logs
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitud de devolución enviada. Te contactaremos.'
        ]);
    }
}
