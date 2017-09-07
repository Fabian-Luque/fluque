<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regiones', function(Blueprint $table){
        $table->increments('id');
        $table->string('nombre');
        $table->integer('pais_id')->unsigned();
        $table->foreign('pais_id')->references('id')->on('paises')->onDelete('cascade');
        $table->timestamps();
                            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('regiones');
    }
}
