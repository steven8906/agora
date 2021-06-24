<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartidasCotizaciones extends Model
{
    use HasFactory;
    protected $fillable = [
        'idcotizaciones',
        'numero',
        'descripcion',
        'cantidad',
        'precio',
        'idunidad',
        'total',
    ];
}
