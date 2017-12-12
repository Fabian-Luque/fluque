<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\User;

class ReservasMotorEvent extends Event {
    use SerializesModels;

    public $data;

    public function __construct($reserva, $propiedad_id) {
        $this->data = array(
            'propiedad_id' => $propiedad_id
        );
    }

    public function broadcastOn() {
        return ['message'];
    }
}
