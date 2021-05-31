<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultialmacenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multialmacen', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_almacen')->unsigned();
            $table->bigInteger('id_producto')->unsigned();
            $table->double('stock')->default(0.0)->nullable(false);
            $table->double('stock_minimo')->default(0.0)->nullable(false);
            $table->double('stock_maximo')->default(0.0)->nullable(false);
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
        Schema::dropIfExists('multialmacen');
    }
}
