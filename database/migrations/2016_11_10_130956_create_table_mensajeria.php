<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMensajeria extends Migration {

    public function up() {
        Schema::create(
            'mensajeria', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('emisor_id')->unsigned();
                $table->integer('receptor_id')->unsigned();
                $table->string('mensaje');
                $table->foreign('emisor_id')
                    ->references('id')
                ->on('propiedades');
                $table->foreign('receptor_id')
                    ->references('id')
                ->on('propiedades');
                $table->timestamps();
            }
        );
    }

    public function down() {
        Schema::drop('mensajeria');
    }
}