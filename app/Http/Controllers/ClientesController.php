<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ClientesController extends Controller
{
    public function index()
    {
        $clientes = Clientes::all();
        $usuario  = Auth::user();
        return Inertia::render('Clientes/Index', compact('clientes', 'usuario'));
    }

    public function store(Request $request)
    {
        $reglas = array(
            'nombre' => 'required',
            'rfc' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => 'required | email | unique:clientes',
        );

        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
            'email' => 'El :attribute debe ser una dirección de correo valida',
            'unique' => 'El :attribute esta repetido',
        );
        $request->validate($reglas, $mensaje);
        Clientes::create($request->only('nombre', 'rfc', 'direccion', 'telefono', 'email'));
        return response()->json(array('success' => true, 'info' => Clientes::all()));
    }

    public function update(Request $request)
    {
        $reglas = array(
            'nombre' => 'required',
            'rfc' => 'required',
            'direccion' => 'required',
            'telefono' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('clientes')->ignore($request->email,'email'),
            ]

        );

        $mensaje = array(
            'required' => 'El campo :attribute, es obligatorio',
            'email' => 'El :attribute debe ser una dirección de correo valida',
            'unique' => 'El :attribute esta repetido',
        );
        $request->validate($reglas, $mensaje);
        Clientes::where('id', $request->only('id'))->update($request->all());
        return response()->json(array('success' => true, 'info' => Clientes::all()));
    }
}
