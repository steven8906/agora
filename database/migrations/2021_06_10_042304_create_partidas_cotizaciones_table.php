<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partidas_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->Integer('numero')->unsigned();
            $table->text('descripcion')->nullable();;
            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio', 12, 2);
            $table->decimal('total', 12, 2);
            $table->bigInteger('idunidad')->unsigned();
            $table->bigInteger('idcotizaciones')->unsigned();
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
        Schema::dropIfExists('partidas_cotizaciones');
    }
}
