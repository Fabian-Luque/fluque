<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailedJobsTable extends Migration {

    public function up() {
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create(
                'failed_jobs', 
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->text('connection');
                    $table->text('queue');
                    $table->longText('payload');
                    $table->timestamp('failed_at')->useCurrent();
                }
            );
        }
    }

    public function down() {
        Schema::drop('failed_jobs');
    }
}