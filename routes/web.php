<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EtiquetaController; 
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\MarcaController; 
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\AtributoController;


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
Route::get('/productos/{id}/atributos', [ProductoController::class, 'atributos'])->name('productos.atributos');
Route::post('/productos/{id}/guardar-variaciones', [ProductoController::class, 'guardarVariaciones'])->name('productos.guardar-variaciones');
Route::post('/productos/{id}/generar-combinaciones', [ProductoController::class, 'generarCombinaciones'])->name('productos.generar-combinaciones');
// Rutas para atributos
Route::resource('atributos', AtributoController::class);

// Rutas para valores de atributos
Route::prefix('atributos/{atributo}')->name('atributos.')->group(function () {
    Route::get('/valores', [AtributoController::class, 'valores'])->name('valores');
    Route::get('/valores/create', [AtributoController::class, 'createValor'])->name('valores.create');
    Route::post('/valores', [AtributoController::class, 'storeValor'])->name('valores.store');
    Route::get('/valores/{valor}/edit', [AtributoController::class, 'editValor'])->name('valores.edit');
    Route::put('/valores/{valor}', [AtributoController::class, 'updateValor'])->name('valores.update');
    Route::delete('/valores/{valor}', [AtributoController::class, 'destroyValor'])->name('valores.destroy');
});

// Rutas para asignar atributos a productos (más simple)
Route::get('/productos/{id}/asignar-atributos', [ProductoController::class, 'asignarAtributos'])->name('productos.asignar-atributos');
Route::post('/productos/{id}/guardar-atributos', [ProductoController::class, 'guardarAtributos'])->name('productos.guardar-atributos');