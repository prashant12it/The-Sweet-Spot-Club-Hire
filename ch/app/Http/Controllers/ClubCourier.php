<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 25-09-2018
 * Time: 10:56 AM
 */

namespace App\Http\Controllers;

use App;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session;
use Validator;
use View;
use Weblee\Mandrill\Mail;
use XeroPrivate;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Error\Card;


class ClubCourier extends Controller
{
    public function __construct()
    {
        $this->utility = new UtilityController;
        $this->DBTables = Config::get('constants.DbTables');
    }

    function index(){
        View::share('title', 'Courier Booking');
        View::share('Page', 'booking');
        View::share('PageHeading', 'Golf Club Hire Australia');
        /*View::share('PageDescription1', 'At The Sweet Spot Club Hire, we offer the latest to market clubs from the leading brands – Callaway and TaylorMade. We have designed our sets to cater for all levels of golfer, whether it be someone playing from scratch or someone just starting out. Hit the Sweet Spot with your next hire!');
        View::share('PageDescription2', '');*/
        return view('pages.clubcourier.booking');
    }
}