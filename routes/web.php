<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Carrito\CarritoController;
use App\Http\Controllers\Checkout\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Direccion;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Models\Producto;
use App\Http\Controllers\Checkout\PaymentController;
use App\Http\Controllers\Checkout\OrderReceivedController;
use App\Http\Controllers\Checkout\CheckoutSuccessController;
use App\Http\Controllers\Checkout\CheckoutErrorController;
use App\Http\Controllers\SoporteController;

// Route::get('/', function () {
//     return view('inicio');
// })->name('home');

Route::get('/', function () {
    // Trae productos activos (evita traer todo si la tabla es grande)
    $productos = Producto::where('bActivo', 1)->orderBy('tFecha_registro','desc')->get();

    return view('inicio', compact('productos'));
})->name('home');

// Ruta púlica para el Webhook de Stripe 
Route::post('/stripe/webhook', [PaymentController::class, 'stripeWebhook'])->name('webhook.stripe'); 

// Login y registro solo para invitados
Route::middleware('guest')->group(function () {
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
    Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

    // Rutas para verificación de email
    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::get('/resend-verification', function () {
        return view('auth.resend-verification');
    })->name('verification.resend-form');
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail'])->name('verification.resend');

    // Rutas para restablecimiento de contraseña
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
});

// --------------------
// Rutas para clientes
// --------------------
Route::middleware(['auth', \App\Http\Middleware\CheckRole::class . ':cliente'])->group(function () {

// --------------------
// Rutas de Carrito
// --------------------
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/{producto}', [CarritoController::class, 'store'])->name('carrito.store');
    Route::put('/carrito/{detalle}', [CarritoController::class, 'update'])->name('carrito.update');
    Route::delete('/carrito/{detalle}', [CarritoController::class, 'destroy'])->name('carrito.destroy');

// --------------------
// Rutas de Checkout
// --------------------
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::post('/checkout/crear-direccion', [CheckoutController::class, 'crearDireccion'])
        ->name('checkout.crearDireccion');
    
    Route::put('/checkout/actualizar-direccion/{id}', [CheckoutController::class, 'actualizarDireccion'])
        ->name('checkout.actualizarDireccion');


    Route::get('/api/direccion/{id}', function ($id) {
    $direccion = Direccion::where('id_direccion', $id)
        ->where('id_usuario', Auth::user()->id_usuario)
        ->first();

    return response()->json([
        'success' => (bool) $direccion,
        'direccion' => $direccion
    ]);
});

    Route::post('/cupon/aplicar', [CheckoutController::class, 'aplicarCupon'])->name('cupon.aplicar');


    // Rutas de Soporte
    Route::get('/soporte', [SoporteController::class, 'form'])
    ->name('soporte.form');

    Route::post('/soporte/enviar', [SoporteController::class, 'send'])
    ->name('soporte.send');

    // Rutas de pago

    // Stripe
    Route::post('/payment/stripe-session', [PaymentController::class, 'createStripeSession'])->name('payment.stripe.session');

    // Order Received
    Route::get('/order-received/{id}', [OrderReceivedController::class, 'show'])
    ->name('order.received');

    Route::get('/checkout/success', [CheckoutSuccessController::class, 'index'])
    ->name('checkout.success');

    Route::get('/checkout/error', [CheckoutErrorController::class, 'index'])->name('checkout.error');

    // PayPal
    Route::post('/payment/paypal-create', [PaymentController::class, 'createPaypalOrder'])->name('payment.paypal.create');
    Route::post('/payment/paypal-capture', [PaymentController::class, 'capturePaypalOrder'])->name('payment.paypal.capture');

    Route::get('/pago-error', function () {
    return view('checkout.session-error');
})->name('session.error');

});

// --------------------
// Rutas para admin
// --------------------
// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/admin/dashboard', function () {
//         return "Bienvenido al panel de administración 👨‍💻";
//     })->name('admin.dashboard');

//     Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios');
// });

// --------------------
// Rutas para superadmin
// --------------------
// Route::middleware(['auth', 'role:superadmin'])->group(function () {
//     Route::get('/superadmin/panel', function () {
//         return "Bienvenido al panel del superadmin 👑";
//     })->name('superadmin.panel');
// });
