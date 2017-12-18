<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('habitaciones')) {
        Schema::table('habitaciones', function (Blueprint $table) {
            $table->dropColumn('precio_base');
            $table->dropColumn('disponibilidad_base');
        });
    }
if (Schema::hasTable('reservas')) {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign('reservas_metodo_pago_id_foreign');
            $table->dropColumn('metodo_pago_id');
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
