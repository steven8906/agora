<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_carga', ['MOVIMIENTOS', 'CARGA_PRODUCTO'])->nullable(false);
            $table->string('token')->nullable(false);
            $table->string('tipo_archivo')->nullable(false);
            $table->string('nombre_original')->nullable(false);
            $table->string('nombre_cod')->nullable(false);
            $table->string('ubicacion')->nullable(false);
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
        Schema::dropIfExists('cargas');
    }
}
