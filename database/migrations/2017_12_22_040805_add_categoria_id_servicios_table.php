<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoriaIdServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servicios', function (Blueprint $table) {
        $table->dropColumn('categoria');
        $table->integer('cantidad_disponible')->after('precio')->default(0);
        $table->integer('categoria_id')->after('cantidad_disponible')->default(1)->unsigned();
        $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicios', function (Blueprint $table) {
        $table->dropColumn('cantidad_disponible');
        $table->dropColumn('categoria_id');
    
        });
    }
}
