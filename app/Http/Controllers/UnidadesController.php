<?php

namespace App\Http\Controllers;

use App\Models\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UnidadesController extends Controller
{
    public function index(){
        $usuario  = Auth::user();
        $unidades = Unidades::all();
        return Inertia::render('Unidades/Index', compact('usuario', 'unidades'));
    }

    public function store(Request $request)
    {
        $reglas = array(
            'unidad' => 'required',
            'clave'  => 'required',
            'tipo'   => 'required',
        );

        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
        );

        $request->validate($reglas, $mensaje);
        Unidades::create($request->all());
        return response()->json(array('success' => true, 'info' => Unidades::all()));
    }

    public function update(Request $request){
        $reglas = array(
            'unidad' => 'required',
            'clave'  => 'required',
            'tipo'   => 'required',
        );

        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
        );

        $mensaje = array('required' => 'El campo :attribute, es obligatorio');
        $request->validate($reglas, $mensaje);
        Unidades::where('id', $request->only('id'))->update($request->only('unidad', 'clave', 'tipo'));
        return response()->json(array('success' => true, 'info' => Unidades::all()));
    }
}
