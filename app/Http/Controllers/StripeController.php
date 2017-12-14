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
use Cartalyst\Stripe\Exception\NotFoundException;
use App\DatosStripe;

class StripeController extends Controller {
    public function ClienteStripeCrear(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'email'   => 'required',
            	'prop_id' => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
        	$datos_stripe = DatosStripe::where(
        		'prop_id',
        		$request->prop_id
        	)->first();

        	if (isset($datos_stripe->plan_id)) {
        		$plan = $datos_stripe->plan_id;
        	} else {
        		$plan = null;
        	}

        	if ($request->has('moneda')) {
        		$moneda = 'USD';
        	} else {
        		$moneda = $request->moneda;
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

    public function tarjetaStripeCrear(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'prop_id' 	=> 'required',
            	'number'    => 'required',
        		'exp_month' => 'required',
        		'cvc'       => 'required',
        		'exp_year'  => 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
        	$datos_stripe = DatosStripe::where(
        		'prop_id',
        		$request->prop_id
        	)->first();

        	$stripe = Stripe::make(config('app.STRIPE_SECRET'));

    		$token = $stripe->tokens()->create([
    			'card' => [
        			'number'    => $request->number,
        			'exp_month' => $request->exp_month,
        			'cvc'       => $request->cvc,
        			'exp_year'  => $request->exp_year,
    			],
			]);

			$card = $stripe->cards()->create(
				$datos_stripe->cliente_id, 
				$token['id']
			);

    		$retorno['errors'] = false;
    		$retorno['msg'] = $card;
    	}
    	return Response::json($retorno);
    }

    public function tarjetaStripeObtener(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'prop_id' 	=> 'required',
           	)
        );

        if ($validator->fails()) {
        	$retorno['errors'] = true;
        	$retorno["msj"] = $validator->errors();
        } else {
        	$datos_stripe = DatosStripe::where(
        		'prop_id',
        		$request->prop_id
        	)->first();

        	$stripe = Stripe::make(config('app.STRIPE_SECRET'));

    		if ($request->has('card_id')) {
    			try {
    				$retorno['errors'] = false;
    				$cards = $stripe->cards()->find(
    					$request->cliente_id, 
    					$request->card_id
    				);
    			} catch (NotFoundException $e) {
    				$retorno['errors'] = true;
					$cards = $e->getMessage();
    			}
    		
    			$retorno['msg'] = $cards;
    		} else {
    			try {
    				$retorno['errors'] = false;
    				$cards = $stripe->cards()->all($request->cliente_id);
    			} catch (NotFoundException $e) {
    				$retorno['errors'] = true;
					$cards = $e->getMessage();
    			}
    		
    			$retorno['msg'] = $cards;
    		}
    	}
    	return Response::json($retorno);
    }
}
