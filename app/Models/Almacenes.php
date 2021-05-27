<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacenes extends Model
{
    use HasFactory;
    protected $table    = 'almacenes';
    protected $fillable = [
        'almacen',
        'descripcion'
    ];
}
