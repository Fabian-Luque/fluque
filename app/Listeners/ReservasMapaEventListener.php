<?php

namespace App\Listeners;

use App\Events\ReservasMapaEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LRedis;

class ReservasMapaEventListener {
    CONST EVENT = 'message';
    CONST CHANNEL = 'message';

    public function __construct() {
    }

    public function handle(ReservasMapaEvent $event) {
        $redis = LRedis::connection();
        $redis->publish(self::CHANNEL, json_encode($event));
    }
}