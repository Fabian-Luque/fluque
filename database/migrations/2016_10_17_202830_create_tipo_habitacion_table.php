<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoHabitacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
if (!Schema::hasTable('tipo_habitacion')) {
        Schema::create('tipo_habitacion', function(Blueprint $table){
        $table->increments('id');
        $table->string('nombre');
        $table->timestamps();
                            
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
        
        Schema::drop('tipo_habitacion');


    }
}
