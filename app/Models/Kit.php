<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    use HasFactory;
    protected $table = 'kits';
    protected $fillable = [
        'codigo',
        'id_producto',
        'nombre',
        'precio_venta',
        'precio_compra',
        'precio_minimo',
        'precio_liquidacion',
        'precio_mayorista',
        'descripcion',
        'condicion',
        'ubicacion',
        'token',
    ];
}
