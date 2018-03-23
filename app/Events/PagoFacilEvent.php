<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PagoFacilEvent extends Event {
    use SerializesModels;
    public $data;
    public $evento;

    public function __construct($propiedad_id) {
        $this->data = $propiedad_id;
        $this->evento = "pagofacil";
    }

    public function broadcastOn() {
        return ['message'];
    }
}
