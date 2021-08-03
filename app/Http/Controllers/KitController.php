<?php

namespace App\Http\Controllers;

use App\Models\Almacenes;
use App\Models\Categorias;
use App\Models\Kit;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class KitController extends Controller
{
    public function index()
    {
        $usuario       = Auth::user();
        $productos     = $this->productosRelacionados();
        $codigo_barras = $this->generarBarras();
        $unidades      = Unidades::all();
        $kits          = $this->getKits();
        return Inertia::render('Kits/Index', compact('usuario', 'unidades', 'codigo_barras', 'productos', 'kits'));
    }

    public function store(Request $request)
    {
        try {
            $productos = $request->get('productos');
            DB::beginTransaction();
            $uniqueid = uniqid();
            Kit::where('codigo', $request->get('codigo'))->delete();
            foreach ($productos as $producto){
                if (isset($producto['id'])){
                    Kit::create([
                        'codigo'             => $request->get('codigo'),
                        'id_producto'        => $producto['id'],
                        'nombre'             => $request->get('nombre'),
                        'precio_venta'       => $request->get('precio_venta'),
                        'precio_compra'      => $request->get('precio_compra'),
                        'precio_minimo'      => $request->get('precio_minimo'),
                        'precio_liquidacion' => $request->get('precio_liquidacion'),
                        'precio_mayorista'   => $request->get('precio_mayorista'),
                        'descripcion'        => $request->get('descripcion'),
                        'condicion'          => true,
                        'ubicacion'          => $request->get('ubicacion'),
                        'token'              => $uniqueid
                    ]);
                }
            }
            DB::commit();
            return response()->json(array('success' => true, 'info' => $this->getKits()));
        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json(array('success' => false, 'info' => $ex->getMessage()));
        }
    }

    public function show(Kit $kit)
    {
        //
    }

    public function update(Request $request, Kit $kit)
    {
        //
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

    private function getKits(){
        return Kit::from('kits AS a')
                    ->selectRaw('( a.condicion ) AS condicion,
                                ( a.codigo ) AS codigo,
                                ( a.token ) AS token,
                                ( a.nombre ) AS nombre,
                                ( a.descripcion ) AS descripcion,
                                ( a.ubicacion ) AS ubicacion,
                                GROUP_CONCAT( b.nombre SEPARATOR "|") AS producto,
	                            GROUP_CONCAT( b.id SEPARATOR "|" ) AS id_producto,
                                GROUP_CONCAT( b.path_imagen SEPARATOR "|" ) AS path_imagen,
                                ( a.precio_compra ) AS precio_compra,
                                ( a.precio_venta ) AS precio_venta,
                                ( a.precio_minimo ) AS precio_minimo,
                                ( a.precio_mayorista ) AS precio_mayorista,
                                ( a.precio_liquidacion ) AS precio_liquidacion')
                    ->join('productos AS b', 'b.id', '=', 'a.id_producto')
                    ->groupBy('a.codigo')
                    ->get();
    }

    private function generarBarras(){
        return Str::limit(preg_replace('/[^0-9]/', '', md5(time()) . rand(1000, 10000)), 8, '');
    }
}
