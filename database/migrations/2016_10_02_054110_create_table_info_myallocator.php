<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInfoMyallocator extends Migration {
   public function up() {
        if (!Schema::hasTable('credenciales_myallocator')) {
            Schema::create(
                'credenciales_myallocator', 
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('m_user_id');
                    $table->string('m_user_pass');
                    $table->string('m_user_token')->nullable();
                    $table->string('m_property_id')->nullable();
                    $table->integer('prop_id')->unsigned();
                    $table->timestamps();
                }
            );
        } 
    }

    public function down() {
        if (Schema::hasTable('credenciales_myallocator')) {
            Schema::drop('credenciales_myallocator');
        }
    }
}