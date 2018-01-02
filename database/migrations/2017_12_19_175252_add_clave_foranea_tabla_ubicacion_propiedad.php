<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClaveForaneaTablaUbicacionPropiedad extends Migration {
    public function up() {
        if (Schema::hasTable('propiedades')) {
            if (Schema::hasColumn('propiedades', 'prop_id')) {
                Schema::table(
                    'propiedades', 
                    function($table) {
                        $table->foreign('prop_id')
                            ->references('id')
                        ->on('propiedades');
                    }
                );
            }
        }
    }

    public function down() {
        if (Schema::hasTable('propiedades')) {
            if (Schema::hasColumn('propiedades', 'prop_id')) {
                Schema::table(
                    'propiedades', 
                    function ($table) {
                        $table->dropColumn('prop_id');
                    }
                );                
            }
        }
    }
}