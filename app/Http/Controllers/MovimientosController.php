<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Kit;
use App\Models\Movimientos;
use App\Models\Almacenes;
use App\Models\Producto;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MovimientosController extends Controller
{
    public function index(){
        $usuario       = Auth::user();
        $productos     = $this->productosRelacionados();
        $unidades      = Unidades::all();
        $almacenes      = Almacenes::all();
        //$movimientos   = Movimientos::all();
        $movimientos   = array();
        return Inertia::render('Movimientos/Index', compact('usuario', 'unidades', 'productos', 'movimientos','almacenes'));
//        $categorias = Categorias::all();
//        $usuario    = Auth::user();
//        return Inertia::render('/Index', compact('categorias', 'usuario'));
    }

    private function productosRelacionados(){
        return Producto::from('productos AS a')
            ->select('a.*', 'b.id AS idcategoria', 'b.nombre AS categoria', 'c.id AS idproveedor', 'c.nombre AS proveedor', 'd.id AS id_unidad', 'd.unidad', 'e.id AS id_unidad', 'e.unidad')
            ->join('categorias AS b', 'b.id', '=', 'a.idcategoria')
            ->join('proveedores AS c', 'c.id', '=', 'a.idproveedor')
            ->join('unidades AS d', 'd.id', '=', 'a.unidad_salida')
            ->join('unidades AS e', 'e.id', '=', 'a.unidad_entrada')
            ->get();
    }

    public function store(Request $request){
       try {
            $productos = $request->productoSeleccion;
            foreach($productos as $index => $producto){
                Movimientos::create([
                    'id_producto' => $producto['id'],
                    'almacen_entrada' => $request->almacenEntrada,
                    'almacen_destino' => $request->almacenDestino,
                    'tipo_movimiento' => $request->tipoMovimiento
                ]);
            }
            return response()->json(array('success' => true, 'info' => Movimientos::all()));
        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json(array('success' => false, 'info' => $ex->getMessage()));
        }
    }
}
