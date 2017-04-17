<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoMonedaIdToReservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservas', function (Blueprint $table) {
        $table->integer('tipo_moneda_id')->after('noches')->nullable()->unsigned();
        $table->foreign('tipo_moneda_id')->references('id')->on('tipo_moneda');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservas', function (Blueprint $table) {
            /*$table->dropForeign('reservas_tipo_moneda_id_foreign');*/
            $table->dropColumn('tipo_moneda_id');
    

        });
    }
}
