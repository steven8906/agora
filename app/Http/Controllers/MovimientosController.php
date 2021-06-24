<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Kit;
use App\Models\Movimientos;
use App\Models\Producto;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MovimientosController extends Controller
{
    public function index(){
        $usuario       = Auth::user();
        $productos     = $this->productosRelacionados();
        $unidades      = Unidades::all();
        //$movimientos   = Movimientos::all();
        $movimientos   = array();
        return Inertia::render('Movimientos/Index', compact('usuario', 'unidades', 'productos', 'movimientos'));
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
}
