<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameColumnUniqueCodeToKitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->string('nombre', 100)->nullable(false);
            $table->string('token')->nullable(false);
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
            //
        });
    }
}
