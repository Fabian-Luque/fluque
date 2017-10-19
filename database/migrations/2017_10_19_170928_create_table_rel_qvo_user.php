<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRelQvoUser extends Migration {

    public function up() {
        Schema::create(
            'qvousers', 
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->index();
                $table->string('qvo_id');
                $table->foreign('user_id')
                    ->references('id')
                ->on('users');
                $table->timestamps();
            }
        );
    }

    public function down() {
    }
}