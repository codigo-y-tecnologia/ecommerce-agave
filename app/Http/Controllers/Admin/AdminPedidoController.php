<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Reembolsos;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\PedidoCanceladoCliente;


class AdminPedidoController extends Controller
{
    public function marcarEnviado(Pedido $pedido)
{
    abort_if(!$pedido->envio, 403);

    $pedido->envio->update(['eEstado' => 'enviado']);
    $pedido->update(['eEstado' => 'enviado']);

    return back()->with('success', 'Pedido marcado como enviado');
}

public function marcarEntregado(Pedido $pedido)
{
    abort_if(!$pedido->envio || $pedido->envio->eEstado !== 'enviado', 403);

    $pedido->envio->update(['eEstado' => 'entregado']);
    $pedido->update(['eEstado' => 'entregado']);

    return back()->with('success', 'Pedido marcado como entregado');
}

public function cancelar(Request $request, Pedido $pedido)
{

    $request->validate([
        'motivo' => 'required|string|min:5|max:255',
    ]);

    $pedido->load([
    'venta',
    'envio',
    'pago',
    'usuario',
    'detalles.producto'
]);

        // ❌ Validaciones de seguridad
        abort_if(!$pedido->pago, 403, 'El pedido no tiene pago registrado');
        abort_if(
            optional($pedido->envio)->eEstado === 'entregado',
            403,
            'Pedido ya entregado'
        );

        $pago = $pedido->pago;
        $venta = $pedido->venta;

        DB::beginTransaction();

        try {

        // 1️⃣ Ejecutar reembolso según método de pago
        if ($pago->eMetodo_pago === 'stripe') {
            $this->reembolsarStripe($pago);
        }

        if ($pago->eMetodo_pago === 'paypal') {
            $this->reembolsarPaypal($pago);
        }

        // 2️⃣ Guardar reembolso
        Reembolsos::create([
            'id_venta'     => $venta->id_venta,
            'dMonto'       => $pago->dMonto,
            'vMotivo'      => $request->motivo,
            'eMetodo_pago' => $pago->eMetodo_pago,
            'eEstado'      => 'procesado',
        ]);

        // 3️⃣ Actualizar estados
        $venta->update(['eEstado' => 'reembolsada']);
        $pedido->update(['eEstado' => 'cancelado']);
        $pago->update(['eEstado' => 'reembolsado']);

        Mail::to($pedido->usuario->vEmail)
        ->send(new PedidoCanceladoCliente($pedido, $request->motivo));

    DB::commit();

        return response()->json([
        'success' => true,
        'message' => 'Pedido cancelado y reembolsado correctamente'
    ]);

    } catch (\Throwable $e) {

        DB::rollBack();

        report ($e);

        return response()->json([
            'success' => false,
            'message' => 'El reembolsó se realizó, pero ocurrió un error al notificar al cliente ' . $e->getMessage()
        ], 500);
    }
}

// Métodos privados para reembolsos
    private function reembolsarStripe($pago)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        StripeRefund::create([
            'payment_intent' => $pago->vReferencia,
        ]);
    }

        private function reembolsarPaypal($pago)
    {
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

}
