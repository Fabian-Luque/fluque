<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ReservasMotorEvent' => [
            'App\Listeners\ReservasMotorEventListener',
        ],
        'App\Events\ChatEvent' => [
            'App\Listeners\ChatEventListener',
        ],
        'App\Events\ReservasMapaEvent' => [
            'App\Listeners\ReservasMapaEventListener',
        ],
        'App\Events\PagoFacilEvent' => [
            'App\Listeners\PagoFacilListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
