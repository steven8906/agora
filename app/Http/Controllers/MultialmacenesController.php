<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Multialmacen;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MultialmacenesController extends Controller
{
    public function index(){
        $usuario            = Auth::user();
        $productos          = $this->productosRelacionados();
        $multialmacenes     = $this->productosMultialmacen();
        $multialmacenes_all = Multialmacen::all();
        return Inertia::render('Multialmacenes/Index', compact('usuario', 'productos', 'multialmacenes', 'multialmacenes_all'));
    }

    public function store(Request $request)
    {
        $multialmacenes = $request->only('multialmacen')['multialmacen'];
        Multialmacen::upsert($multialmacenes, ['stock', 'stock_minimo', 'stock_maximo']);
        return response()->json(array('success' => true, 'info' => $this->productosMultialmacen()));
    }

    public function get(Request $request, $id_producto){
        return response()->json(['success' => true, 'info' => $this->consultaProductoMultialmacen($id_producto)]);
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

    private function productosMultialmacen(){
        return Multialmacen::from('multialmacen AS a')
                ->selectRaw('a.id_producto, ANY_VALUE(b.nombre) AS producto, ANY_VALUE(b.condicion) AS condicion,
                            GROUP_CONCAT(c.almacen SEPARATOR "-") AS almacenes, ANY_VALUE(b.codigo) AS codigo,
                            ANY_VALUE(b.codigo_alterno) AS codigo_alterno, ANY_VALUE(b.path_imagen) AS path_imagen,
                            LPAD(ANY_VALUE(a.stock),6,0) AS stock, LPAD(ANY_VALUE(a.stock_minimo),6,0) AS stock_minimo,
                            LPAD(ANY_VALUE(a.stock_maximo),6,0) AS stock_maximo')
                ->join('productos AS b', 'b.id', '=', 'a.id_producto')
                ->join('almacenes AS c', 'c.id', '=', 'a.id_almacen')
                ->groupBy('a.id_producto')->get();
    }

    private function consultaProductoMultialmacen($id_producto){
        return Multialmacen::from('multialmacen AS a')
            ->select('a.id', 'b.id AS id_almacen', 'a.id_producto', 'b.almacen', 'a.stock', 'a.stock_maximo', 'a.stock_minimo', 'a.condicion')
            ->join('almacenes AS b', 'b.id', '=', 'a.id_almacen')
            ->where(['a.id_producto' => $id_producto])
            ->get();
    }
}
