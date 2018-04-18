<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRazonSocialRutToPropiedadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('propiedades')) {
            Schema::table('propiedades', function (Blueprint $table) {
                $table->string('razon_social')->after('iva');
                $table->string('rut')->after('razon_social');
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
