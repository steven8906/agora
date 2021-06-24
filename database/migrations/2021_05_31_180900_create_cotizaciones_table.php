<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idcliente')->unsigned();
            $table->string('folio');
            $table->string('condiciones')->nullable();;
            $table->string('tiempo_entrega')->nullable();;
            $table->string('obra')->nullable();;
            $table->string('ubicacion')->nullable();;
            $table->string('observaciones')->nullable();;
            $table->decimal('monto_subtotal', 12, 2);
            $table->decimal('monto_iva', 12, 2);
            $table->decimal('monto_total', 12, 2);
            $table->date('fecha', "d/m/Y");
            $table->string('archivo_excel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizaciones');
    }
}
