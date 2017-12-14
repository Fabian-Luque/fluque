<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Propiedad;
use Response;
use Cartalyst\Stripe\Stripe;

class StripeController extends Controller {
    public function ClienteStripeCrear(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'email' => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {

        	if ($request->has('plan')) {
        		$plan = $request->plan;
        	} else {
        		$plan = null;
        	}

        	if ($request->has('modena')) {
        		$modena = 'USD';
        	} else {
        		$modena = $request->modena;
        	}

    		$stripe = Stripe::make(config('app.STRIPE_SECRET'));

    		$cliente = $stripe->customers()->create([
    			'email' => $request->correo,
    			'plan' => $plan,
    			'metadata' => [
    				'currency' => $moneda
    			],
			]);

    		$retorno['errors'] = false;
    		$retorno['msg'] = $cliente;			
    	}
    	return Response::json($retorno);
    }

	public function PlanStripeCrear(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'email' => 'required',
            	'habitaciones' => 'required',
            	'periodo' => 'required', // month, day, year
            	'intervalo' => 'required', // cada cuanto se factura
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {

    		$stripe = Stripe::make(config('app.STRIPE_SECRET'));

    		$plan = $stripe->plans()->create([
    			'id'                   => $request->email,
    			'name'                 => $request->email,
    			'amount'               => config('app.PRECIO_X_HAB_QVO') * $request->habitaciones,
    			'currency'             => 'USD',
    			'interval'             => $request->periodo,
    			'trial_period_days'	   => '15',
    			'interval_count'	   => $request->intervalo,
			]);

    		$retorno['errors'] = false;
    		$retorno['msg'] = $plan;
    	}
    	return Response::json($retorno);
    }

    public function SubscripcionStripeCrear(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'email' => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {

    		$stripe = Stripe::make(config('app.STRIPE_SECRET'));

    		$subscription = $stripe->subscriptions()->create(
    			'cus_BwaUTcbTWF7Le0', [
    				'plan' => 'id del plan',
			]);

    		$retorno['errors'] = false;
    		$retorno['msg'] = $plan;
    	}
    	return Response::json($retorno);
    }
}
