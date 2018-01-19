<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReservasMapaEvent extends Event {
    use SerializesModels;
    public $data;
    public $evento;

    public function __construct($propiedad_id) {
        $this->data = $propiedad_id;
        $this->evento = "mapa";
    }

    public function broadcastOn() {
        return ['message'];
    }
}