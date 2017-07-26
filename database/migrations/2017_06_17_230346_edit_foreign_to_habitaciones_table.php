<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditForeignToHabitacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('habitaciones', function(Blueprint $table){
        $table->dropForeign('habitaciones_tipo_habitacion_id_foreign');
        $table->foreign('tipo_habitacion_id')->references('id')->on('tipo_habitacion')->onDelete('set null');
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
