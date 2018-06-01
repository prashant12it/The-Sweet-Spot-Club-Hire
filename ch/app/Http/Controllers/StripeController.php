<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
Use Redirect;
use App\Http\Requests;
use DB;
use Config;
use View;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Factory;
use Stripe\Stripe;
use Stripe\Charge;

class StripeController extends Controller {
	//
    public function store()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Get the credit card details submitted by the form
        $input = Input::all();
        $token = Input::get('stripeToken');
        // Create the charge on Stripe's servers - this will charge the user's card
        try {
            $charge = Charge::create(array(
                    "amount" => $input['amount'],
                    "currency" => "usd",
                    "card"  => $token,
                    "description" => $input['description'])
            );
            $paymentid     = $charge->id;
            $payerid       = $charge->source->id;
            $status        = $charge->status;
            dd($paymentid.' --- '.$payerid.' ---- '.$status);
            // if status="succeeded" do rest of the insert operation start
            // end
        } catch(Stripe_CardError $e) {
            $e_json = $e->getJsonBody();
            $error = $e_json['error'];
            // The card has been declined
            // redirect back to checkout page
            /*return Redirect::to('/')
                ->with_input()
                ->with('card_errors',$error);*/
            return redirect()->to( '/thankyou' )
                ->with( 'card_errors',$error);
		}
    }
}