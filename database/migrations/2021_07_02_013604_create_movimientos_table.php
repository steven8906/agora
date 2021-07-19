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
            $table->integer('almacen_entrada')->unsigned();
            $table->integer('almacen_destino')->unsigned();
            $table->enum('tipo_movimiento',['ENTRE_ALMACENES','REGULAR']);
            $table->string('token')->nullable(false);
            $table->timestamps();

            $table->foreign('id_producto')
                ->references('id')
                ->on('productos');

            $table->foreign('almacen_entrada')
                ->references('id')
                ->on('almacenes');

            $table->foreign('almacen_destino')
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
