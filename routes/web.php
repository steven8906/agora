<?php

use App\Http\Controllers\AlmacenesController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UnidadesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::get('/almacenes', [AlmacenesController::class, 'index'])->name('almacenes.index');
Route::post('/almacenes/guardar', [AlmacenesController::class, 'store'])->name('almacenes.store');
Route::post('/almacenes/actualizar', [AlmacenesController::class, 'update'])->name('almacenes.update');

Route::get('/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
Route::post('/categorias/guardar', [CategoriasController::class, 'store'])->name('categorias.store');
Route::post('/categorias/actualizar', [CategoriasController::class, 'update'])->name('categorias.update');

Route::get('/movimientos', [MovimientosController::class, 'index'])->name('movimientos.index');
Route::post('/movimientos/guardar', [MovimientosController::class, 'store'])->name('movimientos.store');
Route::post('/movimientos/actualizar', [MovimientosController::class, 'update'])->name('movimientos.update');

Route::get('/unidades', [UnidadesController::class, 'index'])->name('unidades.index');
Route::post('/unidades/guardar', [UnidadesController::class, 'store'])->name('unidades.store');
Route::post('/unidades/actualizar', [UnidadesController::class, 'update'])->name('unidades.update');

Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
Route::post('/clientes/guardar', [ClientesController::class, 'store'])->name('clientes.store');
Route::post('/clientes/actualizar', [ClientesController::class, 'update'])->name('clientes.update');

Route::get('/productos', [ProductosController::class, 'index'])->name('productos.index');
Route::post('/productos/guardar', [ProductosController::class, 'store'])->name('productos.store');
Route::post('/productos/actualizar', [ProductosController::class, 'update'])->name('productos.update');

Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::post('/proveedores/guardar', [ProveedorController::class, 'store'])->name('proveedores.store');
Route::post('/proveedores/actualizar', [ProveedorController::class, 'update'])->name('proveedores.update');

Route::group(['prefix' => 'multialmacen'] , function (){
    Route::get('/producto/{id_producto}', [ProductosController::class, 'multialmacenProducto'])->name('multialmacen.producto');
    Route::post('/guardar', [ProductosController::class, 'store'])->name('multialmacen.store');
});

