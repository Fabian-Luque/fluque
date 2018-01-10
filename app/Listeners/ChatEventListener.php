<?php

namespace App\Listeners;

use App\Events\ChatEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LRedis;

class ChatEventListener {
    CONST EVENT = 'chat';
    CONST CHANNEL = 'chat';

    public function __construct() {
        //
    }

    public function handle(ChatEvent $event) {
        $redis = LRedis::connection();
        $redis->publish(self::CHANNEL, json_encode($event));
    }
}