<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoPropiedadTable extends Migration {
    public function up() {
        Schema::create(
            'tipo_propiedad', 
            function(Blueprint $table){
                $table->increments('id');
                $table->string('nombre');
                $table->timestamps();                
            }
        );

        DB::table('tipo_propiedad')->insert(
            array(
                'nombre' => 'HOTEL'
            ),
            array(
                'nombre' => 'HOSTAL'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            
        Schema::drop('tipo_propiedad');

    }
}
