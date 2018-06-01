<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App;
use DB;
use Config;
use View;
use Session;

class ClubSearchController extends Controller
{
    public function __construct() {
        $this->DBTables = Config::get( 'constants.DbTables' );
	    $this->hire     = new HireController;
    }
    
    public function index() {
            if(getenv('APP_ENV') == 'local'){
                $PreOrderDetsArr = array();
                View::share( 'title', 'home' );
                if(isset( $_COOKIE['order_reference_id'] )){
                    $PreOrderDetsArr = $this->hire->getPreOrderDetails($_COOKIE['order_reference_id']);
                }
                return view( 'pages.frontend.club_search',compact('PreOrderDetsArr') );
            }elseif(getenv('APP_ENV') == 'live'){
                return redirect('http://www.tssclubhire.com');
            }else{
                return redirect('http://tssclubhire.com/wordpress/');
            }

    }
}