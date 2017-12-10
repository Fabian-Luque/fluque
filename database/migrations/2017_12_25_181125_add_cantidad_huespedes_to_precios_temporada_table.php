<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCantidadHuespedesToPreciosTemporadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasTable('precios_temporada')) {
        Schema::table('precios_temporada', function (Blueprint $table) {
            $table->integer('cantidad_huespedes')->after('id')->nullable()->unsigned();
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
