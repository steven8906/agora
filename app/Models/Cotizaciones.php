<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    use HasFactory;
    protected $fillable = [
        'idcliente',
        'folio',
        'condiciones',
        'tiempo_entrega',
        'obra',
        'ubicacion',
        'monto_subtotal',
        'monto_iva',
        'monto_total',
        'fecha',
        'archivo_excel',
        'observaciones',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'idcliente', 'id');
    }
}
