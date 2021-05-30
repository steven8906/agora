<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table = 'productos';
    protected $fillable =[
        'idcategoria',
        'idproveedor',
        'codigo',
        'nombre',
        'precio_compra',
        'descripcion',
        'condicion',
        'tipo',
        'ubicacion',
        'unidad_entrada',
        'unidad_salida',
        'nombre_original_imagen',
        'nombre_unico_imagen',
        'path_imagen',
        'precio_venta',
        'precio_minimo',
        'precio_liquidacion',
        'precio_mayorista',
    ];
}
