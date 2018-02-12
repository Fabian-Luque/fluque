<?php

use Illuminate\Database\Seeder;
use Cartalyst\Stripe\Stripe;
use Cartalyst\Stripe\Exception\NotFoundException;
use Cartalyst\Stripe\Exception\MissingParameterException;

class PlanesStripe extends Seeder {
    public function run() {
    	$tipos = [
    		'month', 
    		'semester',
    		'year'
    	];
        $stripe = Stripe::make(config('app.STRIPE_SECRET'));

        for ($i = 1; $i < 28; $i++) { 
        	$plan = $stripe->plans()->create([
				'id'                 => $tipos[0].'_'.$i,
				'name'               => $tipos[0].'_'.$i,
				'amount'             => config('app.PRECIO_X_HAB_QVO') * $i,
				'currency'           => 'USD',
				'interval'           => $tipos[0],
				'trial_period_days'  => '15',
				'interval_count'     => 1,
			]);
			$this->command->info($tipos[0].'_'.$i);
        }

        for ($i = 1; $i < 28; $i++) { 
        	$plan = $stripe->plans()->create([
				'id'                 => $tipos[1].'_'.$i,
				'name'               => $tipos[1].'_'.$i,
				'amount'             => config('app.PRECIO_X_HAB_QVO') * $i,
				'currency'           => 'USD',
				'interval'           => $tipos[0],
				'trial_period_days'  => '15',
				'interval_count'     => 6,
			]);
			$this->command->info($tipos[1].'_'.$i);
        }

        for ($i = 1; $i < 28; $i++) { 
        	$plan = $stripe->plans()->create([
				'id'                 => $tipos[2].'_'.$i,
				'name'               => $tipos[2].'_'.$i,
				'amount'             => config('app.PRECIO_X_HAB_QVO') * $i,
				'currency'           => 'USD',
				'interval'           => $tipos[0],
				'trial_period_days'  => '15',
				'interval_count'     => 12,
			]);
			$this->command->info($tipos[2].'_'.$i);
        }
    }
}
