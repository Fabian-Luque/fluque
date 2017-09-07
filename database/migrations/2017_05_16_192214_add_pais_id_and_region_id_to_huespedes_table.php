<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaisIdAndRegionIdToHuespedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('huespedes', function (Blueprint $table) {
        $table->integer('pais_id')->after('calificacion_promedio')->nullable()->unsigned();
        $table->foreign('pais_id')->references('id')->on('paises');
        $table->integer('region_id')->after('pais_id')->nullable()->unsigned();
        $table->foreign('region_id')->references('id')->on('regiones');
        });
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
