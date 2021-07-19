<?php

namespace App\Http\Controllers;

use App\Models\Carga;
use App\Models\Categorias;
use App\Models\Kit;
use App\Models\Movimientos;
use App\Models\Almacenes;
use App\Models\Multialmacen;
use App\Models\Producto;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MovimientosController extends Controller
{
    public function index(){
        $usuario         = Auth::user();
        $productos       = $this->productosRelacionados();
        $unidades        = Unidades::all();
        $almacenes       = Almacenes::all();
        $multialmacenes  = $this->multialmacenesAll();
        //$movimientos   = Movimientos ::all();
        $movimientos     = array();
        return Inertia::render('Movimientos/Index', compact('usuario', 'unidades', 'productos', 'movimientos','almacenes', 'multialmacenes'));
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
        DB::beginTransaction();
        try {
            $token     = uniqid('MOV_');
            $productos = json_decode($request->get('productos'));
            //se cargan los archivos y se hace registro en dd antes de hacer el movimiento
            if ($request->hasFile('archivo')) {
                $col_archivos = $request->file('archivo');

                foreach ($col_archivos as $archivo) {
                    $tipo_archivo            = $archivo->getMimeType();
                    $nombre_archivo_md5      = md5(time()) . '_' . $archivo->getClientOriginalName();
                    $nombre_archivo_original = $archivo->getClientOriginalName();
                    $path_imagen             = asset("uploads/{$nombre_archivo_md5}");
                    $archivo->move(public_path() . '/uploads', $nombre_archivo_md5);
                    Carga::create([
                        'tipo_carga'      => 'MOVIMIENTOS',
                        'token'           => $token,
                        'tipo_archivo'    => $tipo_archivo,
                        'nombre_original' => $nombre_archivo_original,
                        'nombre_cod'      => $nombre_archivo_md5,
                        'ubicacion'       => $path_imagen,
                    ]);
                }
            }

            $col_movimientos = [];
            foreach ($productos as $producto) {
                array_push($col_movimientos, [
                    'id_producto'        => $producto->id,
                    'id_almacen_origen'  => $request ->almacenEntrada,
                    'id_almacen_destino' => $request ->almacenDestino
                ]);
                Movimientos::create([
                    'id_producto'     => $producto->id,
                    'almacen_entrada' => $request->almacenEntrada,
                    'almacen_destino' => $request->almacenDestino,
                    'tipo_movimiento' => $request->tipoMovimiento,
                    'token'           => $token,
                ]);
            }
            //recorrer lmultialmacenes que conicidan con el almacen de salida y trasladarlos al de destino
            foreach ($col_movimientos as $movimiento){
                $multialmacenes = Multialmacen::all();
                Multialmacen::where('id_producto', $movimiento['id_producto'])->where('id_almacen',
                    $movimiento['id_almacen_origen'])->delete();
                foreach ($multialmacenes as $multialmacen){
                    if($multialmacen->id_producto == $movimiento['id_producto'] && $multialmacen->id_almacen == $request->almacenEntrada){
                        $model = [
                            'id_almacen'   => (int) $movimiento['id_almacen_destino'],
                            'id_producto'  => $movimiento['id_producto'],
                            'stock'        => $multialmacen->stock,
                            'stock_minimo' => $multialmacen->stock_minimo,
                            'stock_maximo' => $multialmacen->stock_maximo,
                        ];
                        Multialmacen::create($model);
                    }
                }
            }
            DB::commit();
            return response()->json(array('success' => true, 'info' => 'Solicitud exitosa'));
        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json(array('success' => true, 'info' => $ex->getMessage()), 400);
        }
    }

    public function multialmacenesAll(){
        return Multialmacen::from('multialmacen AS a')
                            ->select('b.id AS id_almacen', 'b.almacen', 'c.id AS id_producto', 'c.nombre AS producto', 'a.stock',
                                        'a.stock_minimo', 'a.stock_maximo', 'a.condicion')
                            ->join('almacenes AS b', 'b.id', '=', 'a.id_almacen')
                            ->join('productos AS c', 'c.id', '=', 'a.id_producto')
                            ->get();
    }
}
