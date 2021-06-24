<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kits', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->nullable();
            $table->bigInteger('id_producto')->unsigned();
            $table->string('nombre', 100)->unique();
            $table->decimal('precio_venta', 11, 2);
            $table->decimal('precio_compra', 11, 2);
            $table->string('descripcion', 256)->nullable();
            $table->boolean('condicion')->default(1);
            $table->string('ubicacion');
            $table->timestamps();

            $table->foreign('id_producto')->on('productos')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kits');
    }
}
