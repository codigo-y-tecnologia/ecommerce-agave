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
use App\Http\Controllers\FavoritoController;

// RUTA PRINCIPAL - redirige a la página de inicio real
Route::get('/', function() {
    return redirect()->route('inicio.real');
})->name('inicio');

// Ruta para la página de inicio que muestra productos destacados
Route::get('/inicio-real', [BusquedaController::class, 'inicio'])->name('inicio.real');

// RUTAS DE AUTENTICACIÓN - Usando AuthController
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de registro
Route::get('/registro', [AuthController::class, 'showRegister'])->name('usuarios.create');
Route::post('/registro', [AuthController::class, 'register'])->name('usuarios.store');

// Rutas públicas
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

// RUTAS PÚBLICAS PARA FAVORITOS
Route::get('/favoritos', [FavoritoController::class, 'index'])
     ->name('favoritos.index');

// RUTAS PROTEGIDAS PARA ACCIONES DE FAVORITOS
Route::middleware('auth')->group(function () {
    Route::post('/favoritos/toggle/{producto}', [FavoritoController::class, 'toggle'])
         ->name('favoritos.toggle');
    Route::delete('/favoritos/{producto}', [FavoritoController::class, 'destroy'])
         ->name('favoritos.destroy');
    Route::get('/favoritos/sync', [FavoritoController::class, 'sync'])
         ->name('favoritos.sync');
});