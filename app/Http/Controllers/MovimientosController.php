<?php

namespace App\Http\Controllers;

use App\Models\Carga;
use App\Models\Categorias;
use App\Models\Kit;
use App\Models\Movimientos;
use App\Models\Almacenes;
use App\Models\Multialmacen;
use App\Models\Producto;
use App\Models\TipoMovimiento;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use function GuzzleHttp\Promise\all;

class MovimientosController extends Controller
{
    public function index(){
        $usuario          = Auth::user();
        $productos        = $this->productosRelacionados();
        $unidades         = Unidades::all();
        $almacenes        = Almacenes::all();
        $multialmacenes   = $this->multialmacenesAll();
        //$movimientos    = Movimientos ::all();
        $movimientos      = array();
        $tipoMovimientos  = TipoMovimiento::all();
        $all_movimientos  = $this->getMovimientos();
        return Inertia::render('Movimientos/Index', compact('usuario', 'unidades',
                                        'productos', 'movimientos','almacenes', 'multialmacenes', 'tipoMovimientos',
                                        'all_movimientos'));
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

            if ($request->get('tipoMovimiento') === 'ENTRE_ALMACENES')
            {
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
                    //captura almacen de origen
                    $origen  = $multialmacenes->filter(function($item) use ($movimiento){
                        return $item->id_producto == $movimiento['id_producto'] &&
                            $item->id_almacen == $movimiento['id_almacen_origen'];
                    })->first();
                    //captura almacen de destino
                    $destino = $multialmacenes->filter(function($item) use ($movimiento){
                        return $item->id_producto == $movimiento['id_producto'] &&
                            $item->id_almacen == $movimiento['id_almacen_destino'];
                    })->first();
                    if ($origen != $destino){
                        //comprueba si los almacenes para el movimientos son diferentes y hace el update
                        Multialmacen::where(['id' => $origen->id])->update(['stock' => 0]);
                        Multialmacen::where(['id' => $destino->id])->update(['stock' => (double) ($destino->stock + $origen->stock)]);
                    }
                }
            }
            else{
                foreach ($productos as $producto)
                {
                    Movimientos::create([
                        'id_producto'     => $producto->id,
                        'tipo_movimiento' => $request->get('tipoMovimiento'),
                        'ubicacion'       => $request->get('ubicacion'),
                        'token'           => $token,
                    ]);
                }
            }
            DB::commit();
            return response()->json(array('success' => true, 'info' => 'Solicitud exitosa', 'all_movimientos' => $this->getMovimientos()));
        }catch (\Exception $ex){
            DB::rollBack();
            return response()->json(array('success' => true, 'info' => $ex->getLine()), 400);
        }
    }

    public function storeTipoMovimiento(Request $request)
    {
        TipoMovimiento::create($request->only('descripcion'));
        return response()->json(['success' => true, 'info' => TipoMovimiento::all()], 200, [], JSON_PRETTY_PRINT);
    }

    public function multialmacenesAll(){
        return Multialmacen::from('multialmacen AS a')
                            ->select('b.id AS id_almacen', 'b.almacen', 'c.id AS id_producto', 'c.nombre AS producto', 'a.stock',
                                        'a.stock_minimo', 'a.stock_maximo', 'a.condicion')
                            ->join('almacenes AS b', 'b.id', '=', 'a.id_almacen')
                            ->join('productos AS c', 'c.id', '=', 'a.id_producto')
                            ->get();
    }

    public function getMovimientos()
    {
        return Movimientos::from('movimientos AS a')->select('a.id' ,'b.nombre AS producto', 'c.almacen AS almacen_entrada',
                                'd.almacen AS almacen_destino', 'a.tipo_movimiento', 'a.token', 'a.ubicacion', 'a.created_at')
                            ->join('productos AS b', 'b.id' ,'=', 'a.id_producto')
                            ->join('almacenes AS c', 'c.id', '=', 'a.almacen_entrada')
                            ->join('almacenes AS d', 'd.id', '=', 'a.almacen_destino')
                            ->get();
    }
}
