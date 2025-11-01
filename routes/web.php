<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Carrito\CarritoController;
use App\Http\Controllers\Checkout\CheckoutController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Models\Producto;

// Route::get('/', function () {
//     return view('inicio');
// })->name('home');

Route::get('/', function () {
    // Trae productos activos (evita traer todo si la tabla es grande)
    $productos = Producto::where('bActivo', 1)->orderBy('tFecha_registro','desc')->get();

    return view('inicio', compact('productos'));
})->name('home');

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

    Route::post('/cupon/aplicar', [CheckoutController::class, 'aplicarCupon'])->name('cupon.aplicar');

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
