<?php

namespace App\Listeners;

use App\Events\ReservasMotorEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReservasMotorEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReservasMotorEvent  $event
     * @return void
     */
    public function handle(ReservasMotorEvent $event)
    {
        //
    }
}
