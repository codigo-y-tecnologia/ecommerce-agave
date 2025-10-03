<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('inicio');
})->name('home');

// Login y registro solo para invitados
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
    Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');
});

// Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
// Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

// Logout solo para usuarios autenticados
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
//Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
