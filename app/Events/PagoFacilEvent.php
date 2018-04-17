<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PagoFacilEvent extends Event {
    use SerializesModels;
    public $data;
    public $evento;
    public $pasarela;
    public $data_pago;

    public function __construct($pasa, $data_pago, $prop_id) {
        $this->evento    = "pago-online";
        $this->pasarela  = $pasa;
        $this->data_pago = $data_pago;
        $this->data      = $prop_id;
    }

    public function broadcastOn() {
        return ['message'];
    }
}
