<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyPlanId extends Migration {
    public function up() {
        if (!Schema::hasColumn('pagos_online', 'plan_id')) {
            Schema::table(
                'pagos_online', 
                function (Blueprint $table) {
                    $table->dropForeign(['plan_id']);
                    $table->foreign('plan_id')
                        ->references('plan_id')
                    ->on('planes');
                }
            );
        }
    }

    public function down() {}
}