<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/', function () {
    return view('inicio');
})->name('home');

// Login y registro solo para invitados
Route::middleware('guest')->group(function () {
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
    Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

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

// Rutas para clientes
// --------------------
// Route::middleware(['auth', 'role:cliente'])->group(function () {
//     Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
//     Route::post('/carrito/{producto}', [CarritoController::class, 'store'])->name('carrito.store');
//     Route::put('/carrito/{detalle}', [CarritoController::class, 'update'])->name('carrito.update');
//     Route::delete('/carrito/{detalle}', [CarritoController::class, 'destroy'])->name('carrito.destroy');

//     Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
// });

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
