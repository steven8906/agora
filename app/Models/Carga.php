<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carga extends Model
{
    use HasFactory;
    protected $fillable = [
        'tipo_carga',
        'id_producto',
        'token',
        'tipo_archivo',
        'nombre_original',
        'nombre_cod',
        'ubicacion',
    ];
}
