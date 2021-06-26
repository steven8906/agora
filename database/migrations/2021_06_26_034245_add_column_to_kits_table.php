<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToKitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->decimal('precio_minimo', 11, 2)->default(0.00)->nullable(true);
            $table->decimal('precio_liquidacion', 11, 2)->default(0.00)->nullable(true);
            $table->decimal('precio_mayorista', 11, 2)->default(0.00)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kits', function (Blueprint $table) {
        });
    }
}
