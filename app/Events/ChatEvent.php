<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatEvent extends Event {
    use SerializesModels;
    public $data;

    public function __construct($propiedad_id) {
        $this->data = $propiedad_id;
    }

    public function broadcastOn() {
        return ['chat'];
    }
}