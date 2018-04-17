<?php

namespace App\Listeners;

use App\Events\PagoFacilEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LRedis;

class PagoFacilListener {
    CONST EVENT = 'message';
    CONST CHANNEL = 'message';

    public function __construct() {
    }

    public function handle(PagoFacilEvent $event) {
        $redis = LRedis::connection();
        $redis->publish(self::CHANNEL, json_encode($event));
    }
}