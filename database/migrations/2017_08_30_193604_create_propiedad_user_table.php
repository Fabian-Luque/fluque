<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropiedadUserTable extends Migration {
    public function up() {
        Schema::create(
            'propiedad_user', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')
                    ->unsigned()
                ->index();
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                ->onDelete('cascade');
                $table->integer('propiedad_id')
                    ->unsigned()
                ->index();
                $table->foreign('propiedad_id')
                    ->references('id')
                ->on('propiedades');
                $table->timestamps();
            }
        );
    }

    public function down() {
    }
}