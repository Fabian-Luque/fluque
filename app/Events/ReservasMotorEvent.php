<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReservasMotorEvent extends Event {
    use SerializesModels;

    public $data;
    public $evento;

    public function __construct($propiedad_id) {
        $this->data = $propiedad_id;
        $this->evento = "motor";
    }

    public function broadcastOn() {
        return ['message'];
    }
}