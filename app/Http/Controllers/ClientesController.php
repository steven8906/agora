<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientesController extends Controller
{
    public function index(){
        /*        $categorias = Categorias::all();
                $usuario    = Auth::user();
                return Inertia::render('Categorias/Index', compact('categorias', 'usuario'));*/
        return "Hola soy index de ClientesController";
    }

    public function store(Request $request)
    {
        /*        $reglas = array(
                    'nombre' => 'required',
                );

                $mensaje = array('required' => 'El campo :attribute, es obligatorio');
                $request->validate($reglas, $mensaje);
                Categorias::create($request->only('nombre', 'descripcion'));
                return response()->json(array('success' => true, 'info' => Categorias::all()));*/
    }

    public function update(Request $request){
        /*        $reglas = array(
                    'id'      => 'required',
                    'nombre'  => 'required',
                );

                $mensaje = array('required' => 'El campo :attribute, es obligatorio');
                $request->validate($reglas, $mensaje);
                Categorias::where('id', $request->only('id'))->update($request->all());
                return response()->json(array('success' => true, 'info' => Categorias::all()));*/
    }
}
