<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
Use Redirect;
use Hash;
use App\Http\Requests;
use App\user;
use DB;
use Config;
use View;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Factory;

class AdminController extends Controller {
	//
	public function index() {
		/*$users = array(
			0 => [
				'first_name' => 'Prashant',
				'last_name'  => 'singh',
				'age'        => '27'
			],
			1 => [
				'first_name' => 'Swapnil',
				'last_name'  => 'jaiswal',
				'age'        => '26'
			]
		);
		return view('admin.index',compact('users'));*/
        return redirect()->to('/login');
	}

	public function create(){
		return view('admin.createuser');
	}

	public function store(request $request){
		user::create($request->all());
		return 'Success';
		return $request->all();
	}

	public function dashboard(Request $request){
            
            $DBTables = Config::get('constants.DbTables');
            $parent_pro_type = 4;
        $servicingDays = Config::get('constants.stateServicingDays');
            if ($request->searchDashboard) {
                session()->flash('searchDashboard', $request->searchDashboard);
            }
            else{
                session()->flash('searchDashboard', null);
            }
            
            if(trim($request->searchDashboard['dt_from']) != ''){
                $dtFrom = date("Y-m-d",  strtotime($request->searchDashboard['dt_from']));
                
                if(trim($request->searchDashboard['week_count']) != '' && (int)$request->searchDashboard['week_count']>0){
                    $week_count = (int)$request->searchDashboard['week_count'];
                    $dtUpto = date("Y-m-d",strtotime($dtFrom." +".$week_count." WEEKS"));
                }else{
                    $dtUpto = date("Y-m-d",strtotime($dtFrom." +2 WEEKS"));
                }

            }else{
                $dtFrom = date("Y-m-d", strtotime('monday this week'));
                $dtUpto = date("Y-m-d",strtotime($dtFrom." +2 WEEKS"));
            }
            
            $oneDayTime = 84600;
            
            $days_count = (strtotime($dtUpto)-strtotime($dtFrom))/$oneDayTime;
            $total_days = number_format($days_count,0);
            
            $dateAry = array();
            for($day=0;$day<$total_days;$day++){
                $dateAry[$day] = date("Y-m-d",strtotime($dtFrom." +".$day." DAYS"));
            }
            $whereAry = array();
            $whereAry[0] = array('product_type','=',$parent_pro_type);
            
            if(trim($request->searchDashboard['product_name']) != ''){
                $productsAry = DB::table($DBTables['Products'])
                                        ->select('id','name')
                                        ->where($whereAry)
                                        ->whereRaw("(name like '%".$request->searchDashboard['product_name']."%' OR description like '%".$request->searchDashboard['product_name']."%'  OR sku like '%".$request->searchDashboard['product_name']."%')")
                                        ->orderBy('id', 'ASC')
                                        ->get()
                                        ->toArray();
            }
            else{
                $productsAry = DB::table($DBTables['Products'])
                                        ->select('id','name')
                                        ->where($whereAry)
                                        ->orderBy('id', 'ASC')
                                        ->get()
                                        ->toArray();
            }   
            if(!empty($productsAry)){
                foreach($productsAry as $key=>$parent){
                    $childProductAry = DB::table($DBTables['Group_Products'])
                                                ->join($DBTables['Products'], $DBTables['Group_Products']. '.product_id', '=', $DBTables['Products'] . '.id')
                                                ->select($DBTables['Group_Products'].'.product_id',$DBTables['Products'].'.name',$DBTables['Products'].'.sku',$DBTables['Products'].'.quantity',$DBTables['Products'].'.price')
                                                ->where($DBTables['Group_Products'].'.parent_productid','=',$parent->id)
                                                ->orderBy($DBTables['Products'].'.name', 'ASC')
                                                ->get()
                                                ->toArray();
                    
//                    dd($parent->id);die;
                    
                    if(!empty($childProductAry)){
                        $dtOrdersStartFrom = date('Y-m-d 00:00:00',strtotime($dtFrom));
                        $dtOrdersEndUpto = date('Y-m-d 00:00:00',strtotime($dtUpto));
                            
                        foreach($childProductAry as $iChild=>$childPro){
                            
                            DB::enableQueryLog();
                            $bookedProductsAry = DB::table($DBTables['Booked_Products'])
                                                ->join($DBTables['Orders'], $DBTables['Booked_Products']. '.order_id', '=', $DBTables['Orders'] . '.id')
                                                ->join($DBTables['Orders_Products'], $DBTables['Orders_Products']. '.order_id', '=', $DBTables['Orders'] . '.id')
                                                ->select($DBTables['Booked_Products'].'.booked_quantity',$DBTables['Booked_Products'].'.order_id',
                                                        $DBTables['Booked_Products'].'.dt_booked_from',$DBTables['Booked_Products'].'.dt_booked_upto',
                                                        $DBTables['Booked_Products'].'.is_active',$DBTables['Orders'].'.user_name',
                                                        $DBTables['Orders'].'.user_email',$DBTables['Orders'].'.state_id',$DBTables['Orders_Products'].'.sub_total_amnt',
                                                        $DBTables['Booked_Products'].'.product_id')
                                                ->whereRaw("(".$DBTables['Booked_Products'].".dt_booked_from >= '".$dtOrdersStartFrom."' OR ".$DBTables['Booked_Products'].".dt_booked_upto >= '".$dtOrdersStartFrom."')")
                                                ->whereRaw("(".$DBTables['Booked_Products'].".dt_booked_from <= '".$dtOrdersEndUpto."' OR ".$DBTables['Booked_Products'].".dt_booked_upto <= '".$dtOrdersEndUpto."')")
                                                ->where($DBTables['Booked_Products'].".product_id","=",$childPro->product_id)
                                                ->orderBy($DBTables['Booked_Products']. '.dt_booked_from', 'ASC')
                                                ->groupBy($DBTables['Orders']. '.id')
                                                ->get()
                                                ->toArray();
                            
//                                    dd(DB::getQueryLog());die;
                            $calendarRowArray = array();
                            $totalRowCount = 0;
                            $childProductAry[$iChild]->calendarRowArray = $calendarRowArray;
                            
                            if(!empty($bookedProductsAry)){
                                for($day =0;$day < count($bookedProductsAry);$day++){
                                    
                                    $dtWeekStartOn = date('Y-m-d',strtotime($dtFrom));
                                    $dtWeekEndOn = date('Y-m-d',strtotime($dtUpto));
                                    $dtBookedFrom = date('Y-m-d',  strtotime($bookedProductsAry[$day]->dt_booked_from));
                                    $dtBookedUpto = date('Y-m-d',  strtotime($bookedProductsAry[$day]->dt_booked_upto));
                                    
                                    $rowEventCountAry = $this->getRowAndEventsCount($calendarRowArray,$bookedProductsAry[$day],$totalRowCount,$dtWeekStartOn);
                                    
                                    $row = $rowEventCountAry['rowCount'];
                                    $eventCount = $rowEventCountAry['eventCount'];
                                    $previousEventEnd = $rowEventCountAry['previousEventEnd'];
                                    $extraDay = $rowEventCountAry['extraDay'];
                                    
                                    if((int)$row > $totalRowCount){
                                        $totalRowCount = $row;
                                    }
                                    
                                    if((int)$day == 0){
                                        $day_difference = (strtotime($dtBookedFrom)-strtotime($dtWeekStartOn))/$oneDayTime;
                                    }
                                    else{
                                        if($extraDay == 1)
                                            $day_difference = (strtotime($dtBookedFrom)-(strtotime($previousEventEnd)+$oneDayTime))/$oneDayTime;
                                        else
                                            $day_difference = (strtotime($dtBookedFrom)-strtotime($previousEventEnd))/$oneDayTime;
                                    }

                                    $days_between_booking = number_format($day_difference,0);
                                    $bookigDays = (strtotime($dtBookedUpto)-strtotime($dtBookedFrom))/$oneDayTime;
                                    $booking_days = number_format($bookigDays,0);
                                   
                                        
                                    if($days_between_booking <= 0){
                                        $booking_days = $booking_days+$days_between_booking;
                                    }else if($days_between_booking > 0){
                                        $calendarRowArray[$row]['bookingAry'][$eventCount]['isEmptyCol'] = 1;
                                        $calendarRowArray[$row]['bookingAry'][$eventCount]['colSpan'] = $days_between_booking;
                                        $calendarRowArray[$row]['bookingAry'][$eventCount]['rowNum'] = $row;
                                        $eventCount = $eventCount+1;
                                    }
                                    
                                    if(strtotime($dtBookedUpto) > strtotime($dtWeekEndOn)){
                                        
                                        $extraDays = (strtotime($dtBookedUpto)-strtotime($dtWeekEndOn))/$oneDayTime;
                                        $booking_days = ($booking_days-number_format($extraDays,0))-1;
                                    }
                                    
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['isEmptyCol'] = 0;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['colSpan'] = $booking_days+1;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['rowNum'] = $row;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['booked_quantity'] = $bookedProductsAry[$day]->booked_quantity;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['order_id'] = $bookedProductsAry[$day]->order_id;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['dt_booked_from'] = $bookedProductsAry[$day]->dt_booked_from;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['dt_booked_upto'] = $bookedProductsAry[$day]->dt_booked_upto;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['user_name'] = $bookedProductsAry[$day]->user_name;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['user_email'] = $bookedProductsAry[$day]->user_email;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['sub_total_amnt'] = $bookedProductsAry[$day]->sub_total_amnt;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['product_id'] = $bookedProductsAry[$day]->product_id;
                                    $calendarRowArray[$row]['bookingAry'][$eventCount]['state_id'] = $bookedProductsAry[$day]->state_id;
                                    
                                }
                                
                                $childProductAry[$iChild]->calendarRowArray = $calendarRowArray; 
                            }
                            
                        }
                        
                    }
                    
                    $productsAry[$key]->childProductAry = $childProductAry;
                }
            }
//            print_r($productsAry);
            return view('pages.dashboard',compact('dateAry','total_days','productsAry','servicingDays'));
	}

        public function getRowAndEventsCount($calendarRowArray=array(),$bookingAry=array(),$totalRowCount=0,$dtWeekStartOn=''){
            
            $response = array();
            
            if(empty($calendarRowArray)){
                $response['rowCount'] = 1;
                $response['eventCount'] = 0;
                $response['previousEventEnd'] = '';
                $response['extraDay'] = 0;
            }else{
                
                $bookedFrom = date('Y-m-d',strtotime($bookingAry->dt_booked_from));
                $bookedUpto = date('Y-m-d',strtotime($bookingAry->dt_booked_upto));
                
                $exitFromLoop = false;
                $eventInserted = false;
                
                for($iRow=1;$iRow<=$totalRowCount;++$iRow){
                    if($exitFromLoop == false){
                        $rowEventAry = $calendarRowArray[$iRow];
                        $existingEventCount = count($rowEventAry['bookingAry']);
                        $previousEvent = $rowEventAry['bookingAry'][$existingEventCount-1];
                        $previousEventEndDate = date('Y-m-d',strtotime($previousEvent['dt_booked_upto']));
                        
                        if(strtotime($previousEventEndDate) < strtotime($bookedFrom)){
                            $response['rowCount'] = $iRow;
                            $response['eventCount'] = $existingEventCount;
                            $response['previousEventEnd'] = $previousEventEndDate;
                            $response['extraDay'] = 1;
                            $exitFromLoop = true;
                            $eventInserted = true;
                        }
                        
                    }
                }
                
                if($eventInserted == false){

                    $response['rowCount'] = $totalRowCount+1;
                    $response['eventCount'] = 0;
                    $response['previousEventEnd'] = date('Y-m-d',strtotime($dtWeekStartOn));
                    $response['extraDay'] = 0;

                }
            }
            
            return $response;
        }
        
        public function admin_credential_rules(array $data) {
            $messages = [
                'current-password.required' => 'Please enter current password',
                'password.required' => 'Please enter password',
            ];

            $validator = Validator::make($data, [
                        'current-password' => 'required',
                        'password' => 'required|same:password',
                        'password_confirmation' => 'required|same:password',
                            ], $messages);

            return $validator;
        }

        public function savepass(Request $request) {
            if (Auth::Check()) {
                $request_data = $request->All();
                $validator = $this->admin_credential_rules($request_data);
                if ($validator->fails()) {
                    return redirect()->to($this->getRedirectUrl())
                                    ->withInput($request->input())
                                    ->withErrors($validator);
                } else {
                    $current_password = Auth::User()->password;
                    if (Hash::check($request_data['current-password'], $current_password)) {
                        $user_id = Auth::User()->id;
                        $obj_user = User::find($user_id);
                        $obj_user->password = Hash::make($request_data['password']);
                        ;
                        $obj_user->save();
                        return redirect()->back()->with('message', 'Your password has been changed successfully.');
                    } else {
                        $validator = array('current-password' => 'Please enter correct current password');
                        return redirect()->to($this->getRedirectUrl())
                                        ->withInput($request->input())
                                        ->withErrors($validator);
                    }
                }
            } else {
                return redirect()->to('/');
            }
        }

}