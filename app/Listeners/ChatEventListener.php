<?php

namespace App\Listeners;

use App\Events\ReservasMotorEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LRedis;
use Response;

class ChatEventListener {

    CONST EVENT = 'chat';
    CONST CHANNEL = 'chat';

    public function __construct() {
    }

    public function handle(ReservasMotorEvent $event) {
        $redis = LRedis::connection();
        $redis->publish(self::CHANNEL, json_encode($event));
    }
}