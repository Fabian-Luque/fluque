<?php

namespace App\Console\Commands;

use LRedis;
use Illuminate\Console\Command;

class ejm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        LRedis::subscribe(['test-channel'], function($message) {
            echo $message;
        });
    }
}