<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatEvent extends Event {
    use SerializesModels;
    public $data;
    public $evento;
    public $conv_no;

    public function __construct($propiedad_id, $conv_no) {
        $this->data    = $propiedad_id;
        $this->evento  = "chat";
        $this->conv_no = $conv_no;
    }

    public function broadcastOn() {
        return ['message'];
    }
}