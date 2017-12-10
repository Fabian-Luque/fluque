<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCantidadDisponibleVentaToTipoHabitacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tipo_habitacion')) {
        Schema::table('tipo_habitacion', function (Blueprint $table) {
            $table->integer('cantidad')->after('capacidad')->default(0);
            $table->integer('disponible_venta')->after('cantidad')->default(0);
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
