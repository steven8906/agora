<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('idcategoria')->unsigned();
            $table->bigInteger('idproveedor')->unsigned();
            $table->string('codigo', 50)->nullable();
            $table->string('codigo_alterno', 50)->nullable();
            $table->string('nombre', 100)->unique();
            $table->decimal('precio_venta', 11, 2);
            $table->decimal('precio_minimo', 11, 2);
            $table->decimal('precio_liquidacion', 11, 2)->nullable(true);
            $table->decimal('precio_mayorista', 11, 2)->nullable(true);
            $table->decimal('precio_compra', 11, 2)->nullable(true);
            $table->string('descripcion', 256)->nullable();
            $table->boolean('condicion')->default(1);
            $table->enum('tipo',['Producto','Servicio']);
            $table->integer('unidad_entrada')->unsigned();
            $table->integer('unidad_salida')->unsigned();
            //Imagen
            $table->string('nombre_original_imagen');
            $table->string('nombre_unico_imagen');
            $table->string('path_imagen');
            $table->timestamps();
            //relaciones
            $table->foreign('idcategoria')->references('id')->on('categorias');
            $table->foreign('idproveedor')->references('id')->on('proveedores');
            $table->foreign('unidad_entrada')->references('id')->on('unidades');
            $table->foreign('unidad_salida')->references('id')->on('unidades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
