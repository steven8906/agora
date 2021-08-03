<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('telefono', 20);
            $table->string('email', 50)->nullable();
            $table->string('contacto',50);
            $table->string('telefono_contacto',50);
            $table->enum('tipo_documento', ['DNI', 'RUC', 'PASS'])->nullable();
            $table->string('num_documento', 20)->nullable();
            $table->string('direccion', 70)->nullable();
            $table->boolean('condicion')->default(true);
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
        Schema::dropIfExists('proveedores');
    }
}
