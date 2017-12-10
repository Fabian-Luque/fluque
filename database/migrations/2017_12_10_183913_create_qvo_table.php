<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQvoTable extends Migration {
    public function up() {
        Schema::create(
            'qvousers', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('prop_id')->unsigned()->index();
                $table->string('qvo_id');
                $table->string('solsub_id')->nullable();
                
                $table->foreign('prop_id')
                    ->references('id')
                ->on('propiedades');
                $table->timestamps();
            }
        );
    }

    public function down() {
        Schema::drop('qvousers');
    }
}
