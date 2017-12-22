<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignkeyTableInfoMyallocator extends Migration {
    public function up() {
        Schema::table(
            'credenciales_myallocator', 
            function ($table) {
                $table->foreign('prop_id')
                    ->references('id')
                ->on('propiedades');
            }
        );
    }

    public function down() {
    }
}