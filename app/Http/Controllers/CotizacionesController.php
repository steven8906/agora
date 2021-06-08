<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Cotizaciones;
use App\Models\Unidades;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CotizacionesController extends Controller
{
    public function index()
    {
        $Cotizaciones = Cotizaciones::all();
        $usuario  = Auth::user();
        return Inertia::render('Cotizaciones/Index', compact('Cotizaciones','usuario') );
    }

    public function create(){
        $usuario  = Auth::user();
        $clientes = Clientes::All();
        $unidades = Unidades::All();
        return Inertia::render('Cotizaciones/Create', compact('usuario', 'clientes', "unidades") );
    }

    public function store(Request $request){
        return $request;
        $reglas = array(
            'usuarioSeleccionado.cliente'=> 'required',
            'usuarioSeleccionado.contacto'=> 'required',
            'usuarioSeleccionado.email'=> 'required',
            'usuarioSeleccionado.telefono_oficina'=> 'required',
            'fecha' => 'required',
            'folio' => 'required',
            'monto_subtotal' => 'required',
            'monto_iva' => 'required',
            'monto_total' => 'required',
            'obra' => 'required',
            'ubicacion' => 'required',
            'tiempo_entrega' => 'required',
            
        );
        $mensaje = array(
            'required' => 'El campo :attribute es obligatorio',
            'email' => 'El :attribute debe ser una dirección de correo valida',
            'unique' => 'El :attribute esta repetido',
        );
        $request->validate($reglas, $mensaje);
        //validar que existen partidas requistradas
        if(count($request->dataPartidas) == 0 ){

            return response()->json(array('errors' => [ 'partidas'=> ['Debe ingresar al menos una partida.']], 'message' => 'Debe ingresar al menos una partida.'),422);
        } 
         //validar que existen archivos de calculo subidos
         if(count($request->fileCalculo) == '' ){
            return response()->json(array('errors' => [ 'filecalculo'=> ['Debe ingresar el archivo de calculo de la cotizacion.']], 'message' => 'Debe ingresar el archivo de calculo de la cotizacion.'),422);
        } 


        return $request;
    }

    public function guardarFileExcel(Request $request){
        return $request;
        $nameFile = Hash::make(date('Y-m-d H:i:s'));
        $path = $request->file($nameFile)->store('excels');
        // Storage::disk('local')->put('example.txt', 'Contents');
        return $path;
    }
}
