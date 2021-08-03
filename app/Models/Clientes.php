<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;
    protected $table    = 'clientes';
    protected $fillable = [
        'cliente',
        'contacto',
        'rfc',
        'direccion',
        'telefono_oficina',
        'telefono_movil',
        'email'
    ];
}
