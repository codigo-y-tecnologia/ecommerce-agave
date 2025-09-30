<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Models\Producto;

Route::get('/', function () {
    // Trae productos activos (evita traer todo si la tabla es grande)
    $productos = Producto::where('bActivo', 1)->orderBy('tFecha_registro','desc')->get();

    return view('inicio', compact('productos'));
});


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --------------------
// Rutas de Carrito
// --------------------
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index'); // ver carrito
Route::post('/carrito/{producto}', [CarritoController::class, 'store'])->name('carrito.store'); // agregar producto
Route::put('/carrito/{detalle}', [CarritoController::class, 'update'])->name('carrito.update'); // actualizar cantidad
Route::delete('/carrito/{detalle}', [CarritoController::class, 'destroy'])->name('carrito.destroy'); // eliminar producto

// --------------------
// Rutas de Checkout
// --------------------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');