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
use Cartalyst\Stripe\Exception\MissingParameterException;
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

        	try {
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
					'cus_BwZZtQyTOFkSC3',//$datos_stripe->cliente_id, 
					$token['id']
				);

				$retorno['errors'] = false;
    			$retorno['msg'] = $card;
        	} catch(MissingParameterException $e) {
        		$retorno['errors'] = true;
        		$retorno['msg'] = $e->getMessage();
        	} catch(NotFoundException $e) {
        		$retorno['errors'] = true;
        		$retorno['msg'] = $e->getMessage();
        	}
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

    public function tarjetaStripeActualizar(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'prop_id' 	=> 'required',  
            	'card_id'   => 'required',  	
        		'number'    => 'required',
        		'exp_month' => 'required',
        		'cvc'       => 'required',
        		'exp_year'  => 'required',
    			'name'      => 'required',
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

    		try {
    			$retorno['errors'] = false;
    				
    			$card = $stripe->cards()->update(
    				$datos_stripe->cliente_id, 
    				$request->card_id, [
    					'card' => [
        					'number'    => $request->number,
        					'exp_month' => $request->exp_month,
        					'cvc'       => $request->cvc,
        					'exp_year'  => $request->exp_year,
    					],
    					'name'          => $request->nombre
					]
				);
    		} catch (NotFoundException $e) {
    			$retorno['errors'] = true;
				$cards = $e->getMessage();
    		}
    		
   			$retorno['msg'] = $cards; 		
    	}
    	return Response::json($retorno);
    }

    public function tarjetaStripeEliminar(Request $request) {
    	$validator = Validator::make(
        	$request->all(), 
        	array(
            	'prop_id' 	=> 'required',  
            	'card_id'   => 'required',  	
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

    		try {
    			$retorno['errors'] = false;
    			$card = $stripe->cards()->delete(
    				$datos_stripe->cliente_id, 
    				$request->card_id
    			);
    		} catch (NotFoundException $e) {
    			$retorno['errors'] = true;
				$cards = $e->getMessage();
    		}
    		
   			$retorno['msg'] = $cards; 		
    	}
    	return Response::json($retorno);
    }

    public function InvoiceStripeObtener(Request $request) {
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

        	if (isset($datos_stripe->cliente_id)) {
    			try {
    				$retorno['errors'] = false;
    				$retorno['msg'] = $stripe->invoices()->upcomingInvoice(
    					$datos_stripe->cliente_id
    				);
    			} catch (NotFoundException $e) {
    				$retorno['errors'] = true;
					$retorno['msg'] = $e->getMessage();
    			}
    		} else {
    			$retorno['errors'] = true;
				$retorno['msg'] = 'La propiedad no se encuentra registrada';
    		} 		
    	}
    	return Response::json($retorno);
    }
}