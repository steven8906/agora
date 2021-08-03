<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multialmacen extends Model
{
    use HasFactory;
    protected $table    = 'multialmacen';
    protected $fillable = ['id_almacen', 'id_producto', 'stock', 'stock_minimo', 'stock_maximo'];
}
