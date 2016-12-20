<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCalificacionPromedioToHuespedesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('huespedes', function (Blueprint $table) {
        $table->float('calificacion_promedio',10,1)->after('pais')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('huespedes', function (Blueprint $table) {
        $table->dropColumn('calificacion_promedio');
        });
    }
}
