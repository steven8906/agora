<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Cotizaciones;
use App\Models\PartidasCotizaciones;
use App\Models\Unidades;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

use function Psy\debug;

class CotizacionesController extends Controller
{
      /***CRUD***/
    public function index()
    {
        $cotizaciones = Cotizaciones::with('cliente')->get();
        $usuario  = Auth::user();
        return Inertia::render('Cotizaciones/Index', compact('cotizaciones', 'usuario'));
    }

    public function create()
    {
        $usuario  = Auth::user();
        $clientes = Clientes::All();
        $unidades = Unidades::All();
        return Inertia::render('Cotizaciones/Create', compact('usuario', 'clientes', "unidades"));
    }

    public function store(Request $request)
    {
        // return $request;
        //validacion de seleccion de un cliente
        if ($request->cliente_id == null) {
            return response()->json(array('errors' => ['cliente' => ['Debe seleccionar un cliente.']], 'message' => 'Debe seleccionar un cliente.'), 422);
        }
        //reglas de validacion
        $reglas = array(
            'fecha' => 'required',
            'folio' => 'required',
            'obra' => 'required',
            'tiempo_entrega' => 'required',
            'condiciones' => 'required',
            'ubicacion' => 'required',
            'monto_subtotal' => 'required',
            'monto_iva' => 'required',
            'monto_total' => 'required',
            'ArchivoDeCalculo'   => [
                'required',
                'mimes:cvs,xls,xlsx',
            ]

        );
        //mensajes de validacion
        $mensaje = array(
            'required' => 'El campo :attribute es obligatorio',
            'email' => 'El :attribute debe ser una dirección de correo valida',
            'unique' => 'El :attribute esta repetido',
            'mimes' => 'El :attribute debe ser de tipo :values',
        );
        $request->validate($reglas, $mensaje); //validacion de campos
        //Validar numero de partidas
        $partidas = collect(json_decode($request->partidas));
        if ($partidas->count() == 0)
            return response()->json(array('errors' => ['partidas' => ['Debe ingresar al menos una partida.']], 'message' => 'Debe ingresar al menos una partida.'), 422);

        //Almacenar archivo excel
        $path_excel =  Storage::put('excels', $request->file('ArchivoDeCalculo'));

        //Crear cotizacion
        $cotizacion_new = Cotizaciones::create([
            'fecha' => $request->fechacotizacion,
            'idcliente' => $request->cliente_id,
            'folio' => $request->folio,
            'obra' => $request->obra,
            'ubicacion' => $request->ubicacion,
            'condiciones' => $request->condiciones,
            'tiempo_entrega' => $request->tiempo_entrega,
            'monto_subtotal' => $request->monto_subtotal,
            'monto_iva' => $request->monto_iva,
            'monto_total' => $request->monto_total,
            'archivo_excel' => $path_excel,
            'observaciones' => $request->observaciones,
        ]);


        //Guardar partidas de la cotizacion 
        foreach ($partidas as $partida) {
            PartidasCotizaciones::create([
                'idcotizaciones' => $cotizacion_new->id,
                'numero' => $partida->partida,
                'descripcion' => $partida->descripcion,
                'cantidad' => $partida->cantidad,
                'precio' => $partida->precio,
                'idunidad' => $partida->unidad,
                'total' => $partida->total,
            ]);
        }
        return response()->json(array('success' => true, 'info' => $cotizacion_new));
    }

    public function edit($id)
    {
        $cotizacion = Cotizaciones::find($id);
        $usuarioSeleccionado = Clientes::find($cotizacion->idcliente);
        $partidas = PartidasCotizaciones::where( 'idcotizaciones', $cotizacion->id)->get();
        $usuario  = Auth::user();
        $clientes = Clientes::All();
        $unidades = Unidades::All();
        return Inertia::render('Cotizaciones/Edit', compact('usuario', 'clientes', "unidades", "cotizacion","usuarioSeleccionado", "partidas"));
    }


    public function update( Request $request, $id )
    {
       $cotizacion_old = Cotizaciones::find($id);
        //validacion de seleccion de un cliente
        if ($request->cliente_id == null) {
            return response()->json(array('errors' => ['cliente' => ['Debe seleccionar un cliente.']], 'message' => 'Debe seleccionar un cliente.'), 422);
        }
        //reglas de validacion
        $reglas = array(
            'fecha' => 'required',
            'folio' => 'required',
            'obra' => 'required',
            'tiempo_entrega' => 'required',
            'condiciones' => 'required',
            'ubicacion' => 'required',
            'monto_subtotal' => 'required',
            'monto_iva' => 'required',
            'monto_total' => 'required',

        );
        //mensajes de validacion
        $mensaje = array(
            'required' => 'El campo :attribute es obligatorio',
            'email' => 'El :attribute debe ser una dirección de correo valida',
            'unique' => 'El :attribute esta repetido',
            'mimes' => 'El :attribute debe ser de tipo :values',
        );
        $request->validate($reglas, $mensaje); //validacion de campos
        //Validar numero de partidas
        $partidas = collect(json_decode($request->partidas));
        if ($partidas->count() == 0)
            return response()->json(array('errors' => ['partidas' => ['Debe ingresar al menos una partida.']], 'message' => 'Debe ingresar al menos una partida.'), 422);
       
        //Almacenar archivo excel
        if($request->file('ArchivoDeCalculo') == null){
            $path_excel =  $cotizacion_old->archivo_excel;
        }else{
            $path_excel =  $cotizacion_old->archivo_excel;
            //eliminar excel antiguo
            if(Storage::delete($path_excel))
                $path_excel =  Storage::put('excels', $request->file('ArchivoDeCalculo'));
        }

        //actualizar cotizacion
        $cotizacion_updated = Cotizaciones::where('id', $id )->update([
            'fecha' => $request->fechacotizacion,
            'idcliente' => $request->cliente_id,
            'folio' => $request->folio,
            'obra' => $request->obra,
            'ubicacion' => $request->ubicacion,
            'condiciones' => $request->condiciones,
            'tiempo_entrega' => $request->tiempo_entrega,
            'monto_subtotal' => $request->monto_subtotal,
            'monto_iva' => $request->monto_iva,
            'monto_total' => $request->monto_total,
            'archivo_excel' => $path_excel,
            'observaciones' => $request->observaciones,
        ]);


        if ($cotizacion_updated){
            //eliminar partidas
            $deletedRows = PartidasCotizaciones::where('idcotizaciones', $id)->delete();
                //Guardar partidas nuevas de la cotizacion 
                foreach ($partidas as $partida) {
                    PartidasCotizaciones::create([
                        'idcotizaciones' => $id,
                        'numero' => $partida->numero,
                        'descripcion' => $partida->descripcion,
                        'cantidad' => $partida->cantidad,
                        'precio' => $partida->precio,
                        'idunidad' => $partida->idunidad,
                        'total' => $partida->total,
                    ]);
                }

        }

        return response()->json(array('success' => true, 'info' => 'Actualizado exitosamente'));
    }


    /***EXTRAS***/

    public function guardarFileExcel(Request $request)
    {
        return $request;
        $nameFile = Hash::make(date('Y-m-d H:i:s'));
        $path = $request->file($nameFile)->store('excels');
        // Storage::disk('local')->put('example.txt', 'Contents');
        return $path;
    }


}
