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
        $kits          = Kit::all();
        return Inertia::render('Kits/Index', compact('usuario', 'unidades', 'codigo_barras', 'productos', 'kits'));
    }

    public function store(Request $request)
    {
        $reglas = array(
            'nombre' => 'required',
        );
        try {
            $productos = $request->get('productos');
            DB::beginTransaction();
            $uniqueid = uniqid();
            foreach ($productos as $producto){
                Kit::create([
                    'codigo'        => $request->get('codigo'),
                    'id_producto'   => $producto,
                    'nombre'        => $request->get('nombre'),
                    'precio_venta'  => $request->get('precio_venta'),
                    'precio_compra' => $request->get('precio_compra'),
                    'descripcion'   => $request->get('descripcion'),
                    'condicion'     => true,
                    'ubicacion'     => $request->get('ubicacion'),
                    'token'         => $uniqueid
                ]);
            }
            DB::commit();
            return response()->json(array('success' => true, 'info' => Kit::all()));
        }catch (\Exception $ex){
            return response()->json(array('success' => false, 'info' => $ex->getMessage()));
        }
        dd($productos);

        $mensaje = array('required' => 'El campo :attribute, es obligatorio');
        $request->validate($reglas, $mensaje);
        Kit::create($request->only('nombre', 'descripcion'));
        return response()->json(array('success' => true, 'info' => Kit::all()));
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

    private function generarBarras(){
        return Str::limit(preg_replace('/[^0-9]/', '', md5(time()) . rand(1000, 10000)), 8, '');
    }
}
