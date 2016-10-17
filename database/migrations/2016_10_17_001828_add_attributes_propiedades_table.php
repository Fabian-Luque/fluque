<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributesPropiedadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
             Schema::table('propiedades', function (Blueprint $table) {

            $table->string('pais')->after('numero_habitaciones');
            $table->string('estado')->after('ciudad');
            $table->string('telefono')->after('direccion');
            $table->string('email')->after('telefono');
            $table->string('nombre_responsable')->after('email');
            $table->string('descripcion')->after('nombre_responsable');
            $table->string('moneda')->after('descripcion');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

             Schema::table('propiedades', function (Blueprint $table) {
            
          
            $table->dropColumn('pais');
            $table->dropColumn('estado');
            $table->dropColumn('telefono');
            $table->dropColumn('email');
            $table->dropColumn('nombre_responsable');
            $table->dropColumn('descripcion');
            $table->dropColumn('moneda');

        });


    }
}
