<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $table = 'proveedores';
    protected $fillable = [
        'id','nombre', 'telefono','email', 'contacto','telefono_contacto','tipo_documento','num_documento','direccion'
    ];
}
