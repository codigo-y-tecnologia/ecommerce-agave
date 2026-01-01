<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SolicitudPostventa;
use App\Models\Reembolsos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;

class AdminPostventaController extends Controller
{
    public function index(Request $request)
    {
        $query = SolicitudPostventa::with([
            'pedido.usuario',
            'pedido.venta',
            'pedido.pago',
        ]);

        if ($request->estado) {
            $query->where('eEstado', $request->estado);
        }

        if ($request->tipo) {
            $query->where('eTipo', $request->tipo);
        }

        if ($request->pedido) {
            $query->where('id_pedido', $request->pedido);
        }

        $solicitudes = $query->latest()->paginate(15);

        return view('admin.postventa.index', compact('solicitudes'));
    }

    public function show(SolicitudPostventa $solicitud)
    {
        $solicitud->load([
            'pedido.usuario',
            'pedido.venta',
            'pedido.pago',
            'pedido.detalles.producto'
        ]);

        return view('admin.postventa.show', compact('solicitud'));
    }

    public function aprobar(SolicitudPostventa $solicitud)
    {
        abort_if($solicitud->eEstado !== 'pendiente', 403);

        $pedido = $solicitud->pedido;
        $pago   = $pedido->pago;
        $venta  = $pedido->venta;

        DB::beginTransaction();

        try {

            // 🔁 Stripe
            if ($pago->eMetodo_pago === 'stripe') {
                Stripe::setApiKey(config('services.stripe.secret'));

                StripeRefund::create([
                    'payment_intent' => $pago->vReferencia,
                ]);
            }

            // 🔁 PayPal
            if ($pago->eMetodo_pago === 'paypal') {
                $environment = new SandboxEnvironment(
                    config('services.paypal.client_id'),
                    config('services.paypal.secret')
                );

                $client = new PayPalHttpClient($environment);

                $request = new CapturesRefundRequest($pago->vReferencia);
                $request->body = [
                    'amount' => [
                        'value' => number_format($pago->dMonto, 2, '.', ''),
                        'currency_code' => 'MXN'
                    ]
                ];

                $client->execute($request);
            }

            // 💾 Guardar reembolso
            Reembolsos::create([
                'id_venta'     => $venta->id_venta,
                'dMonto'       => $pago->dMonto,
                'vMotivo'      => $solicitud->vMotivo,
                'eMetodo_pago' => $pago->eMetodo_pago,
                'eEstado'      => 'procesado',
            ]);

            // 🔄 Estados

            if ($solicitud->eTipo === 'cancelacion') {
    $pedido->update(['eEstado' => 'cancelado']);

    $solicitud->update(['tRespuesta_admin' => 'Cancelación aprobada y reembolso realizado']);
}

if ($solicitud->eTipo === 'devolucion') {
    $pedido->update(['eEstado' => 'devuelto']);

    $solicitud->update(['tRespuesta_admin' => 'Devolución aprobada y reembolso realizado']);
}

            $solicitud->update(['eEstado' => 'reembolsada']);
            $venta->update(['eEstado' => 'reembolsada']);
            $pago->update(['eEstado' => 'reembolsado']);

            // 📧 Email
            Mail::to($pedido->usuario->vEmail)
                ->send(new \App\Mail\PostventaAprobadaCliente($solicitud));

            DB::commit();

            return redirect()
                ->route('admin.postventa.index')
                ->with('success', 'Solicitud aprobada y reembolso realizado');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Error al procesar el reembolso');
        }
    }

public function rechazar(Request $request, SolicitudPostventa $solicitud)
    {
        abort_if($solicitud->eEstado !== 'pendiente', 403);

        $request->validate([
        'respuesta' => 'required|string|min:5|max:255',
    ]);

        $solicitud->update([
            'eEstado' => 'rechazada',
            'tRespuesta_admin' => $request->respuesta
        ]);

        Mail::to($solicitud->pedido->usuario->vEmail)
            ->send(new \App\Mail\PostventaRechazadaCliente($solicitud));

        return redirect()
            ->route('admin.postventa.index')
            ->with('success', 'Solicitud rechazada');
    }
}
