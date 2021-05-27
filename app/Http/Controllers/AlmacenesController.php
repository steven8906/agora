<?php

namespace App\Http\Controllers;

use App\Models\Almacenes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AlmacenesController extends Controller
{

    public function index()
    {
        $almacenes = Almacenes::all();
        return Inertia::render('Almacenes/Index', ['almacenes' => $almacenes, 'usuario' => Auth::user()]);
    }

    public function store(Request $request)
    {
        $reglas = array(
            'almacen' => 'required',
        );

        $mensaje = array('required' => 'El campo :attribute, es obligatorio');
        $request->validate($reglas, $mensaje);
        Almacenes::create($request->only('almacen', 'descripcion'));
        return response()->json(array('success' => true, 'info' => Almacenes::all()));
    }

    public function update(Request $request){
        $reglas = array(
            'id'      => 'required',
            'almacen' => 'required',
        );

        $mensaje = array('required' => 'El campo :attribute, es obligatorio');
        $request->validate($reglas, $mensaje);
        Almacenes::where('id', $request->only('id'))->update($request->all());
        return response()->json(array('success' => true, 'info' => Almacenes::all()));
    }
}
