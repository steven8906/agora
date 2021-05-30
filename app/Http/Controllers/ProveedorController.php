<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProveedorController extends Controller
{
    public function index(){
        $usuario     = Auth::user();
        $proveedores = Proveedor ::all();
        return Inertia::render('Proveedores/Index', compact('usuario', 'proveedores'));
    }

    public function store(Request $request)
    {
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

        $request->validate($reglas, $mensaje);
        Proveedor::create($request->all());
        return response()->json(array('success' => true, 'info' => Proveedor::all()));
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
        return response()->json(array('success' => true, 'info' => Proveedor::all()));
    }
}
