<?php

use Illuminate\Database\Migrations\Migration;
use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;

class CreateUbicacionPropiedad extends Migration {

    public function up() {
        Schema::create(
            'ubicacion_propiedad', 
            function(Blueprint $table) {
                $table->increments('id');
                $table->integer('prop_id')->unsigned();
                $table->point('location')->nullable();
                $table->foreign('prop_id')
                    ->references('id')
                ->on('propiedades');
                $table->timestamps();
            }
        );
    }

    public function down() {
        Schema::drop('ubicacion_propiedad');
    }
}