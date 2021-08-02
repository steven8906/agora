<?php

use App\Http\Controllers\AlmacenesController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\MovimientosController;
use App\Http\Controllers\MultialmacenesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UnidadesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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

Route::group(['prefix' => '/almacenes'], function (){
    Route::get('/', [AlmacenesController::class, 'index'])->name('almacenes.index');
    Route::post('almacenes/guardar', [AlmacenesController::class, 'store'])->name('almacenes.store');
    Route::post('almacenes/actualizar', [AlmacenesController::class, 'update'])->name('almacenes.update');
    Route::post('desactivar', [AlmacenesController::class, 'disable'])->name('almacenes.disable');
});

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
Route::get('/productos/{producto}', [ProductosController::class, 'indexID'])->name('productos.producto');
Route::post('/productos/guardar', [ProductosController::class, 'store'])->name('productos.store');
Route::post('/productos/actualizar', [ProductosController::class, 'update'])->name('productos.update');

Route::group(['prefix' => '/proveedores'], function (){
    Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::post('guardar', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::post('actualizar', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::post('desactivar', [ProveedorController::class, 'disable'])->name('proveedores.disable');
});

Route::group(['prefix' => 'multialmacen'] , function (){
    Route::get('/producto/{id_producto}', [ProductosController::class, 'multialmacenProducto'])->name('multialmacen.producto');
    Route::post('/guardar', [ProductosController::class, 'storeMultialmacen'])->name('multialmacen.store');
});

Route::group(['prefix' => 'multialmacenes'] , function (){
    Route::get('/', [MultialmacenesController::class, 'index'])->name('multialmacenes.index');
    Route::post('/guardar', [MultialmacenesController::class, 'store'])->name('multialmacenes.store');
    Route::post('/actualizar', [MultialmacenesController::class, 'update'])->name('multialmacenes.update');
    Route::get('/consulta/{id_producto}', [MultialmacenesController::class, 'get'])->name('multialmacenes.get');
});

Route::group(['prefix' => 'kits'] , function (){
    Route::get('/', [KitController::class, 'index'])->name('kit.index');
    Route::post('/guardar', [KitController::class, 'store'])->name('kit.store');
    Route::post('/actualizar', [KitController::class, 'update'])->name('kit.update');
});

Route::group(['prefix' => 'movimientos'] , function (){
    Route::get('/', [MovimientosController::class, 'index'])->name('movimientos.index');
    Route::post('/guardar', [MovimientosController::class, 'store'])->name('movimientos.store');
    Route::post('/guardartipomovimiento', [MovimientosController::class, 'storeTipoMovimiento'])->name('movimientos.storeTipoMovimiento');
    Route::post('/actualizar', [MovimientosController::class, 'update'])->name('movimientos.update');
});

Route::get('/cotizaciones', [CotizacionesController::class, 'index'])->name('cotizaciones.index');
Route::get('/cotizaciones/create', [CotizacionesController::class, 'create'])->name('cotizaciones.create');
Route::post('/cotizaciones/guardar', [CotizacionesController::class, 'store'])->name('cotizaciones.store');
Route::get('/cotizaciones/edit/{id}', [CotizacionesController::class, 'edit'])->name('cotizaciones.edit');
Route::post('/cotizaciones/actualizar/{id}', [CotizacionesController::class, 'update'])->name('cotizaciones.update');
Route::post('/cotizaciones/guardarFileExcel', [CotizacionesController::class, 'guardarFileExcel'])->name('cotizaciones.guardarFileExcel');

Route::group(['prefix' => 'helpers'], function (){
   Route::get('guid', function (){
       return Str::limit(preg_replace('/[^0-9]/', '', md5(time()) . rand(1000, 10000)), 8, '');
   })->name('helpers.guid');
});
