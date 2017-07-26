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
            $table->string('telefono')->after('direccion');
            $table->string('email')->after('telefono');
            $table->string('nombre_responsable')->after('email');
            $table->string('descripcion')->after('nombre_responsable');
            $table->integer('iva')->after('descripcion');
            $table->integer('porcentaje_deposito')->after('iva');
           

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
            
            $table->dropColumn('telefono');
            $table->dropColumn('email');
            $table->dropColumn('nombre_responsable');
            $table->dropColumn('descripcion');
        

        });


    }
}
