<?php

namespace App\Listeners;

use App\Events\ReservasMotorEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LRedis;
use Response;

class ReservasMotorEventListener {

    CONST EVENT = 'message';
    CONST CHANNEL = 'message';

    public function __construct() {
    }

    public function handle(ReservasMotorEvent $event) {
        $redis = LRedis::connection();
        $redis->publish(self::CHANNEL, json_encode($event));
    }
}
