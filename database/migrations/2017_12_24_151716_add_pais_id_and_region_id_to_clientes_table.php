<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaisIdAndRegionIdToClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
        $table->integer('pais_id')->after('giro')->nullable()->unsigned();
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
/*        Schema::table('clientes', function (Blueprint $table) {
        $table->dropForeign('clientes_pais_id_foreign');
        $table->dropColumn('pais_id');
        $table->dropForeign('clientes_region_id_foreign');
        $table->dropColumn('region_id');
    
        });*/

    }
}
