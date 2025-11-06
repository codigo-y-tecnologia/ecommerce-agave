<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EtiquetaController; 
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MarcaController; 
use App\Http\Controllers\AtributoController;
use App\Http\Controllers\ProductoAtributoController;
use App\Http\Controllers\BusquedaController;

// Cambiar esta ruta para usar AuthController en lugar de la función anónima
Route::get('/', [AuthController::class, 'index'])->name('inicio');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/usuarios/crear', [AuthController::class, 'showRegister'])->name('usuarios.create');
Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('/categorias', CategoriaController::class);
Route::resource('productos', ProductoController::class);
Route::resource('marcas', MarcaController::class);
Route::resource('etiquetas', EtiquetaController::class);
Route::resource('atributos', AtributoController::class);

Route::get('/producto/{id}', [ProductoController::class, 'showPublic'])->name('productos.show.public');
Route::get('/catalogo', [ProductoController::class, 'catalogo'])->name('productos.catalogo');

// RUTAS PARA ATRIBUTOS DE PRODUCTOS
Route::get('/productos/{producto}/atributos', [ProductoController::class, 'atributos'])
     ->name('productos.atributos');

Route::post('/productos/{producto}/atributos', [ProductoAtributoController::class, 'store'])
     ->name('productos.atributos.store');

// Cambia estas rutas para usar parámetros explícitos
Route::put('/productos/{producto}/atributos/{atributo}', [ProductoAtributoController::class, 'update'])
     ->name('productos.atributos.update');

Route::delete('/productos/{producto}/atributos/{atributo}', [ProductoAtributoController::class, 'destroy'])
     ->name('productos.atributos.destroy');

// API para obtener opciones de atributos
Route::get('/atributos/{atributo}/opciones', [ProductoAtributoController::class, 'getOpciones'])
     ->name('atributos.opciones');

// Rutas de búsqueda
Route::get('/buscar', [BusquedaController::class, 'buscar'])->name('busqueda.resultados');
Route::get('/busqueda-rapida', [BusquedaController::class, 'busquedaRapida'])->name('busqueda.rapida');
Route::get('/buscar-productos', [BusquedaController::class, 'buscarProductos'])->name('busqueda.productos');