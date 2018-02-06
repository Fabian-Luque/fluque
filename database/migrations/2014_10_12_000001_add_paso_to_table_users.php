<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPasoToTableUsers extends Migration {

    public function up() {
        Schema::table(
            'users', 
            function ($table) {
                $table->tinyInteger('paso')
                ->default(0)
                ->after('phone');
            }
        );
    }

    public function down() {
        Schema::table(
            'users', 
            function ($table) {
                $table->dropColumn('paso');
            }
        );
    }
}