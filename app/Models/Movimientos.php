<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    use HasFactory;
    protected $fillable = array(
        'id_producto',
        'almacen_entrada',
        'almacen_destino',
        'tipo_movimiento',
        'token',
        'ubicacion',
    );
}
