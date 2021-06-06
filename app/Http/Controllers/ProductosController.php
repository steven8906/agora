<?php

namespace App\Http\Controllers;

use App\Models\Almacenes;
use App\Models\Categorias;
use App\Models\Multialmacen;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductosController extends Controller
{
    public function index(){
        $usuario       = Auth::user();
        $proveedores   = Proveedor::all();
        $categorias    = Categorias::all();
        $unidades      = Unidades::all();
        $productos     = $this->productosRelacionados();
        $codigo_barras = $this->generarBarras();
        $almacenes     = Almacenes::all();
        return Inertia::render('Productos/Index', compact('usuario', 'proveedores', 'categorias',
                                            'unidades', 'codigo_barras', 'productos', 'almacenes'));
    }

    public function store(Request $request)
    {
        $reglas = array(
            'idcategoria'        => 'required',
            'idproveedor'        => 'required',
            'nombre'             => 'required',
            'codigo'             => 'required',
            'tipo'               => 'required',
            'unidad_entrada'     => 'required',
            'unidad_salida'      => 'required',
            'precio_compra'      => 'numeric',
            'precio_venta'       => 'numeric',
            'precio_minimo'      => 'numeric',
            'precio_liquidacion' => 'numeric',
            'precio_mayorista'   => 'numeric',
            'imagen'             => 'mimes:jpeg,jpg,png,gif',
        );
        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
            'numeric'    => 'El campo :attribute, debe ser en formato moneda',
        );
        $request->validate($reglas, $mensaje);
        $nombre_archivo_md5      = md5(time()) . '_' . $request->file('imagen')->getClientOriginalName();
        $nombre_archivo_original = $request->file('imagen')->getClientOriginalName();
        $path_imagen             = asset("uploads/{$nombre_archivo_md5}");
        $model                   = $request->only('idcategoria', 'idproveedor', 'nombre', 'codigo', 'tipo', 'unidad_entrada',
                                                        'unidad_salida', 'precio_compra', 'precio_venta', 'precio_minimo', 'precio_liquidacion',
                                                        'precio_mayorista');
        $model['nombre_original_imagen']  = $nombre_archivo_original;
        $model['nombre_unico_imagen']     = $nombre_archivo_md5;
        $model['path_imagen']             = $path_imagen;
        $request->file('imagen')->move(public_path() . '/uploads', $nombre_archivo_md5);
        Producto::create($model);
        return response()->json(array(
            'success' => true,
            'info' => array(
                'productos'     => $this->productosRelacionados(),
                'codigo_barras' => $this->generarBarras()
            )
        ));
    }

    public function update(Request $request){
        $reglas = array(
            'idcategoria'        => 'required',
            'idproveedor'        => 'required',
            'nombre'             => 'required',
            'codigo'             => 'required',
            'tipo'               => 'required',
            'unidad_entrada'     => 'required',
            'unidad_salida'      => 'required',
            'precio_compra'      => 'numeric',
            'precio_venta'       => 'numeric',
            'precio_minimo'      => 'numeric',
            'precio_liquidacion' => 'numeric',
            'precio_mayorista'   => 'numeric',
            'imagen'             => 'mimes:jpeg,jpg,png,gif',
        );

        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
            'numeric'    => 'El campo :attribute, debe ser en formato moneda',
        );
        $request->offsetUnset('categoria');
        $request->offsetUnset('proveedor');
        $request->offsetSet('unidad_entrada', $request->id_unidad);
        $request->offsetSet('unidad_salida', $request->id_unidad);
        $request->offsetUnset('id_unidad');
        $request->offsetUnset('unidad');
        $request->validate($reglas, $mensaje);
        Producto::where('id', $request->only('id'))->update($request->all());
        return response()->json(array('success' => true, 'info' => $this->productosRelacionados()));
    }

    public function multialmacenProducto(Request $request, $id_producto){
        return response()->json($this->verMultialmacenProducto($id_producto));
    }

    public function storeMultialmacen(Request $request){
        $reglas = array(
            'id_producto'  => 'numeric|required',
            'almacenes'    => 'array|required',
            'stock'        => 'numeric|required',
            'stock_minimo' => 'numeric|required',
            'stock_maximo' => 'numeric|required',
        );
        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
            'numeric'    => 'El campo :attribute, debe ser en formato moneda',
        );
        $request->validate($reglas, $mensaje);
        try {
            DB::beginTransaction();
            Multialmacen::where('id_producto', $request->only('id_producto')['id_producto'])->delete();
            $almacenes = $request->only('almacenes')['almacenes'];
            foreach ($almacenes as $almacen){
                Multialmacen::create([
                    'id_almacen'   => $almacen,
                    'id_producto'  => $request->only('id_producto')['id_producto'],
                    'stock'        => $request->only('stock')['stock'],
                    'stock_maximo' => $request->only('stock_maximo')['stock_maximo'],
                    'stock_minimo' => $request->only('stock_minimo')['stock_minimo'],
                ]);
            }
            DB::commit();
            return response()->json(array(
                'success' => true,
                'info' => array(
                    'productos'      => $this->productosRelacionados(),
                    'codigo_barras'  => $this->generarBarras(),
                    'multialmacenes' => $this->verMultialmacenProducto($request->only('id_producto')['id_producto']),
                )
            ));
        }catch (\Exception $ex){
            return response()->json(['success' => false, 'info' => $ex->getMessage()], 422);
        }
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

    private function verMultialmacenProducto($id_producto){
        $multialmacenes = Multialmacen::where('id_producto', $id_producto)->get();
        if (count($multialmacenes) > 0){
            $model_almacenes = array();
            foreach ($multialmacenes as $multialmacen){
                array_push($model_almacenes, $multialmacen->id_almacen);
            }
            $model = array(
                'almacenes'   => $model_almacenes,
                "id_producto" => $multialmacenes[0]->id_producto,
                "stock"       => $multialmacenes[0]->stock,
                "stock_minimo"=> $multialmacenes[0]->stock_minimo,
                "stock_maximo"=> $multialmacenes[0]->stock_maximo,
            );
            return $model;
        }
        return array();
    }
}
