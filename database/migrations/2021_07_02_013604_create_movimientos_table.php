<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_producto')->unsigned();
            $table->bigInteger('almacen_entrada')->unsigned();
            $table->bigInteger('almacen_destino')->unsigned();
            $table->bigInteger('id_almacen_final')->unsigned();
            $table->enum('tipo_movimiento',['ENTRE_ALMACENES','REGULAR']);
            $table->string('token')->nullable(false);
            $table->string('ubicacion')->nullable(true);
            $table->float('cantidad')->nullable(true)->default(null);
            $table->date('fecha')->nullable(true);
            $table->string('folio_documento')->nullable(true);
            $table->timestamps();
            //relaciones entre tablas
            $table->foreign('id_producto')
                ->references('id')
                ->on('productos');

            $table->foreign('almacen_entrada')
                ->references('id')
                ->on('almacenes');

            $table->foreign('almacen_destino')
                ->references('id')
                ->on('almacenes');

            $table->foreign('id_almacen_final')
                ->references('id')
                ->on('almacenes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos');
    }
}
