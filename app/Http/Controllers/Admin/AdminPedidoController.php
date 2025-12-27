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

public function cancelar(Pedido $pedido)
{
    $pedido->load(['venta', 'envio']);

        // ❌ Validaciones de seguridad
        abort_if(!$pedido->venta, 403, 'Pedido no pagado');
        abort_if(
            optional($pedido->envio)->eEstado === 'entregado',
            403,
            'Pedido ya entregado'
        );

        $venta = $pedido->venta;

        // 1️⃣ Ejecutar reembolso según método de pago
        if ($venta->eMetodo_pago === 'stripe') {
            $this->reembolsarStripe($venta);
        }

        if ($venta->eMetodo_pago === 'paypal') {
            $this->reembolsarPaypal($venta);
        }

        // 2️⃣ Guardar reembolso
        Reembolsos::create([
            'id_venta'     => $venta->id_venta,
            'dMonto'       => $venta->dTotal,
            'eMetodo_pago' => $venta->eMetodo_pago,
            'eEstado'      => 'procesado',
        ]);

        // 3️⃣ Actualizar estados
        $venta->update(['eEstado' => 'reembolsada']);
        $pedido->update(['eEstado' => 'cancelado']);

        return back()->with('success', 'Pedido cancelado y reembolsado correctamente');
}

// Métodos privados para reembolsos
    private function reembolsarStripe($venta)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        StripeRefund::create([
            'payment_intent' => $venta->vReferencia,
        ]);
    }

        private function reembolsarPaypal($venta)
    {
        $environment = new SandboxEnvironment(
            config('services.paypal.client_id'),
            config('services.paypal.secret')
        );

        $client = new PayPalHttpClient($environment);

        $request = new CapturesRefundRequest($venta->vReferencia);
        $request->body = [
            'amount' => [
                'value' => number_format($venta->dTotal, 2, '.', ''),
                'currency_code' => 'MXN'
            ]
        ];

        $client->execute($request);
    }

}
