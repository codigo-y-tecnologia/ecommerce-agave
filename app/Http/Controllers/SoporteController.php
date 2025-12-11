<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SoporteController extends Controller
{
    public function form()
    {
        return view('soporte.form');
    }

    public function send(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|min:10',
        ]);

        try {
            Mail::raw(
                "Soporte solicitud:\n\nUsuario: " . Auth::user()->vEmail .
                "\nNombre: " . Auth::user()->vNombre . "\n\nMensaje:\n" . $request->mensaje,
                function ($msg) {
                    $msg->to(config('mail.support_email'))
                        ->subject('Cliente necesita ayuda con su pedido');
                }
            );

            return back()->with('success', 'Tu mensaje ha sido enviado. Nuestro equipo te contactará pronto.');

        } catch (\Throwable $e) {
            Log::error("Error enviando soporte: " . $e->getMessage());
            return back()->with('error', 'Hubo un problema enviando tu mensaje. Intenta más tarde.');
        }
    }
}
