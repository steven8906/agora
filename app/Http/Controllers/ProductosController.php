<?php

namespace App\Http\Controllers;

use App\Models\Categorias;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use function Sodium\randombytes_buf;

class ProductosController extends Controller
{
    public function index(){
        $usuario       = Auth::user();
        $proveedores   = Proveedor::all();
        $categorias    = Categorias::all();
        $unidades      = Unidades::all();
        $productos     = $this->productosRelacionados();
        $codigo_barras = $this->generarBarras();
        return Inertia::render('Productos/Index', compact('usuario', 'proveedores', 'categorias', 'unidades', 'codigo_barras', 'productos'));
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
            'precio_compra'      => 'required',
            'precio_venta'       => 'required',
            'precio_minimo'      => 'required',
            'precio_liquidacion' => 'required',
            'precio_mayorista'   => 'required',
            'imagen'             => 'mimes:jpeg,jpg,png,gif',
        );
        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
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
        $request->file('imagen')->move(public_path() . "/uploads", $nombre_archivo_md5);
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
            'nombre'            => 'required',
            'telefono'          => 'required|int',
            'contacto'          => 'required',
            'telefono_contacto' => 'required|int',
            'num_documento'     => 'int',
            'email'             => 'email|nullable',
        );

        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
            'integer'  => 'El campo :attribute, debe ser numérico',
            'email'    => 'El campo :attribute, debe ser un email válido',
        );

        $mensaje = array('required' => 'El campo :attribute, es obligatorio');
        $request->validate($reglas, $mensaje);
        Proveedor::where('id', $request->only('id'))->update($request->all());
        return response()->json(array('success' => true, 'info' => $this->productosRelacionados()));
    }

    private function productosRelacionados(){
        return Producto::from('productos AS a')
                        ->select('a.*', 'b.id', 'b.nombre AS categoria', 'c.id', 'c.nombre AS proveedor', 'd.id', 'd.unidad', 'd.id', 'd.unidad', 'e.id', 'e.unidad')
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
