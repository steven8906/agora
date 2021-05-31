<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DefaultValueoPreciosToProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_minimo', 11, 2)->default(0.00)->change();
            $table->decimal('precio_liquidacion', 11, 2)->nullable(true)->default(0.00)->change();
            $table->decimal('precio_mayorista', 11, 2)->nullable(true)->default(0.00)->change();
            $table->decimal('precio_compra', 11, 2)->nullable(true)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            //
        });
    }
}
