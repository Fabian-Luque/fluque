<?php

namespace App\Listeners;

use App\Events\VerificaCuenta;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenerVerificaCuenta
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
     * @param  VerificaCuenta  $event
     * @return void
     */
    public function handle(VerificaCuenta $event)
    {
        //
    }
}
