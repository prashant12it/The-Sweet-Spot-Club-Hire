<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilityController;
use App;
use App\Partners;
use DB;
use Config;
use View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;


class AdminPartnersController extends Controller
{
    
    public function __construct()
    {
        $this->utility = new UtilityController;
    }
    
    public function getPartnerReferenceId(){
        
        $DBTables = Config::get('constants.DbTables');

        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	$reference_key = "";
        $unique_key = false;
        
        while ($i <= 10){
                $reference_key .= $chars{mt_rand(0,strlen($chars)-1)};
                $i++;
        }
        if(trim($reference_key) != ''){
            do {
                    $invalid = 1;

                    $partnerKeys = DB::table($DBTables['Partners'])
                                    ->where('reference_id','=',trim($reference_key))
                                    ->select('id')
                                    ->get();
                    
                    if(count($partnerKeys) == 0){
                        $unique_key = true;
                    }else{
                        $reference_key .= $reference_key.$invalid;
                        $invalid = $invalid+1;
                    }

            } while (!$unique_key);
            return $reference_key;
        }
            
        
    }
    
    public function index(Request $request) {
        View::share('title', 'Partners');
        session()->flash('searchPartner', null);
        $DBTables = Config::get('constants.DbTables');
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');

        $partnersAry = Partners::orderBy('name', 'ASC')->paginate($rowsPerPage);
        
        if(count($partnersAry) >0){
            foreach($partnersAry as $p=>$partner){
                $partnersAry[$p]->total_clicks = 0;
                $partnersAry[$p]->date_last_clicked = '';
                $partnersAry[$p]->earned_commission = 0;
                $partnersAry[$p]->total_orders = 0;

                $partner_click = DB::table($DBTables['Partner_Clicks'])
                                ->where('partner_ref_key','=',trim($partner->reference_id))
                                ->select(DB::raw('count(id) as total_clicks, MAX(dt_clicked_on) as lastClicked'))
                                ->get();
                
                if(count($partner_click)>0){
                    $partnersAry[$p]->total_clicks = $partner_click[0]->total_clicks;
                    $partnersAry[$p]->date_last_clicked = $partner_click[0]->lastClicked;
                }

                $partner_order_stat = DB::table($DBTables['Orders'])
                                ->where('partner_ref_key','=',trim($partner->reference_id))
                                ->select(DB::raw('count(id) as total_ref_orders,SUM(partner_cmsn_amt) as total_earned_com'))
                                ->get();

                if(count($partner_order_stat)>0){
                    $partnersAry[$p]->earned_commission = (int)$partner_order_stat[0]->total_earned_com;
                    $partnersAry[$p]->total_orders = $partner_order_stat[0]->total_ref_orders;
                }
            }
        }
        
        return view('pages.partners.partner_listing', compact('partnersAry'))
                        ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function create() {
        View::share('title', 'Add Partner');
        $countriesAry = $this->utility->getCountriesList();
        return view('pages.partners.partner_add', compact('countriesAry'));
    }

    public function store(Request $request) {
        
        Input::merge(array_map('trim', Input::all()));

        $rules = array(
            'name' => 'required | max:255',
            'email' => 'required |unique:partners|unique:users|email',
            'password' => 'required | min:5',
            'address' => 'required',
            'zipcode' => 'required',
            'state' => 'required',
            'country' => 'required|min:1',
        );
        
        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                            ->withInput($request->input())
                            ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {
            $inputData = $request->all();
            $inputData['iActive'] = '1';
            $inputData['password'] = bcrypt($inputData['password']);
            $inputData['reference_id'] = $this->getPartnerReferenceId();
            
            Partners::create($inputData);
            $BaseURL = Config::get('constants.BaseURL');
            $replace_ary = array();
            $replace_ary['szName'] = $request->name;
            $replace_ary['email'] = $request->email;
            $replace_ary['password'] = $request->password;
            $replace_ary['szLink'] = "<a href='".$BaseURL."partner/login'>Click here to login</a>";
            
//            $this->utility->createEmail("__NEW_PARTNER_ACC__", $replace_ary, $request->email);
            
            return redirect()->to('/partners')
                            ->with('success', 'New partner account successfully created.');
        }
    }

    public function show($idPartner = 0) {
        if ($idPartner > 0) {
            View::share('title', 'Partner Details');
            $DBTables = Config::get('constants.DbTables');
            
            $partnerDetails = DB::table($DBTables['Partners'])
                                ->join($DBTables['Countries'], $DBTables['Countries'].'.id','=',$DBTables['Partners'].'.country')
                                ->select($DBTables['Partners'].'.*',$DBTables['Countries'].'.name as country_name')
                                ->where($DBTables['Partners'].'.id','=',(int)$idPartner)
                                ->get();

            $partnerDetailsData = $partnerDetails[0];
            $partnerDetailsData->total_clicks = 0;
            $partnerDetailsData->date_last_clicked = '';
            $partnerDetailsData->earned_commission = 0;
            $partnerDetailsData->total_orders = 0;
            
            $partner_click = DB::table($DBTables['Partner_Clicks'])
                            ->where('partner_ref_key','=',trim($partnerDetailsData->reference_id))
                            ->select(DB::raw('count(id) as total_clicks, MAX(dt_clicked_on) as lastClicked'))
                            ->get();
            if(count($partner_click)>0){
                $partnerDetailsData->total_clicks = $partner_click[0]->total_clicks;
                $partnerDetailsData->date_last_clicked = $partner_click[0]->lastClicked;
            }
            
            $partner_order_stat = DB::table($DBTables['Orders'])
                            ->where('partner_ref_key','=',trim($partnerDetailsData->reference_id))
                            ->select(DB::raw('count(id) as total_ref_orders,SUM(partner_cmsn_amt) as total_earned_com'))
                            ->get();
            
            if(count($partner_order_stat)>0){
                $partnerDetailsData->earned_commission = (int)$partner_order_stat[0]->total_earned_com;
                $partnerDetailsData->total_orders = $partner_order_stat[0]->total_ref_orders;
            }
            
            $whereOrderAry = array();
            $whereOrderAry['user_id'] = (int)$partnerDetailsData->id;
            $whereOrderAry['user_email'] = trim($partnerDetailsData->email);
            
            $partner_orders = DB::table($DBTables['Orders'])
                            ->where($whereOrderAry)
                            ->select(DB::raw('id,dt_book_from,dt_book_upto,paid_amnt,payment_date,order_status'))
                            ->get();
            
            return view('pages.partners.partner_details', compact('partnerDetailsData','partner_orders'));
        } else {
            return redirect()->to("/partners");
        }
    }

    public function edit($idPartner =0) {
        if ($idPartner > 0) {
            View::share('title', 'Edit Partner');
            $countriesAry = $this->utility->getCountriesList();
            $partner = Partners::find( $idPartner );
            return view('pages.partners.partner_edit', compact('countriesAry','partner'));
            
        } else {
            return redirect()->to("/partners");
        }
        
        
    }

    public function update(Request $request) {
       Input::merge(array_map('trim', Input::all()));

        if((int)$request->partnerId >0){
            $rules = array(
                'name' => 'required | max:255',
                'email' => 'required|email|unique:users,email'.($request->partnerId ? ",$request->partnerId" : '').'|unique:partners,email'.($request->partnerId ? ",$request->partnerId" : ''),
                'address' => 'required',
                'zipcode' => 'required',
                'state' => 'required',
                'country' => 'required|min:1',
            );
            $validator = $this->getValidationFactory()->make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->to($this->getRedirectUrl())
                                ->withInput($request->input())
                                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
            } else {
                
                $inputData = $request->all();
                $inputData['iActive'] = '1';
                if(isset($inputData['password']) && trim($inputData['password']) != '')
                    $inputData['password'] = bcrypt($inputData['password']);
                else
                    unset($inputData['password']);

                $partnerData = Partners::find( $request->partnerId )->update( $inputData );

                return redirect()->to('/partners')
                                ->with('success', 'Partner details successfully updated.');
            }

        }else{
            return redirect()->to("/partners");
        }
        
    }

    public function searchPartner(Request $request) {
        View::share('title', 'Partner Search');
        $DBTables = Config::get('constants.DbTables');
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        
        $previousSearchAry = session('searchPartner');
        if ($previousSearchAry && !$request->searchAry) {
            $request->searchAry = $previousSearchAry;
        }

        if ($request->searchAry) {
            session()->flash('searchPartner', $request->searchAry); // Store it as flash data.

            $activeAry = array();

            if (trim($request->searchAry['iActive']) != '') {
                $activeAry['iActive']= (int)$request->searchAry['iActive'];
            }
//            DB::enableQueryLog();
            if (trim($request->searchAry['name']) != '') {
                $name =trim($request->searchAry['name']);
                $partnersAry = DB::table($DBTables['Partners'])
                        ->whereRaw("(name like '%".$name."%' OR email like '%".$name."%' OR state like '%".$name."%')")
                        ->where($activeAry)
                        ->select('*')
                        ->orderBy('id', 'DESC')
                        ->paginate($rowsPerPage);
            }
            else{
                $partnersAry = DB::table($DBTables['Partners'])
                        ->where($activeAry)
                        ->select('*')
                        ->orderBy('name', 'ASC')
                        ->paginate($rowsPerPage);
            }
            
//            dd(DB::getQueryLog());die;
        } else {
            $partnersAry = Partners::orderBy('name', 'ASC')->paginate($rowsPerPage);
        }
        
        if(count($partnersAry) >0){
            foreach($partnersAry as $p=>$partner){
                $partnersAry[$p]->total_clicks = 0;
                $partnersAry[$p]->date_last_clicked = '';
                $partnersAry[$p]->earned_commission = 0;
                $partnersAry[$p]->total_orders = 0;

                $partner_click = DB::table($DBTables['Partner_Clicks'])
                                ->where('partner_ref_key','=',trim($partner->reference_id))
                                ->select(DB::raw('count(id) as total_clicks, MAX(dt_clicked_on) as lastClicked'))
                                ->get();
                
                if(count($partner_click)>0){
                    $partnersAry[$p]->total_clicks = $partner_click[0]->total_clicks;
                    $partnersAry[$p]->date_last_clicked = $partner_click[0]->lastClicked;
                }

                $partner_order_stat = DB::table($DBTables['Orders'])
                                ->where('partner_ref_key','=',trim($partner->reference_id))
                                ->select(DB::raw('count(id) as total_ref_orders,SUM(partner_cmsn_amt) as total_earned_com'))
                                ->get();

                if(count($partner_order_stat)>0){
                    $partnersAry[$p]->earned_commission = (int)$partner_order_stat[0]->total_earned_com;
                    $partnersAry[$p]->total_orders = $partner_order_stat[0]->total_ref_orders;
                }
            }
        }
        
        return view('pages.partners.partner_listing', compact('partnersAry'))
                        ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }
    
    public function status_update(Request $request){
        $DBTables = Config::get('constants.DbTables');

        $idPartner = (int) $request->idPartner;
        $iActive = (int) $request->iActive;

        if ($idPartner > 0) {
            DB::table($DBTables['Partners'])->where('id', $idPartner)->update(['iActive' => (int) $iActive]);
            return redirect()->to("/partners")
                            ->with('success', 'Partner account status successfully updated.');
        } else {
            return redirect()->to("/partners")
                            ->with('error', 'Something is wrong, status not updated successfully.');
        }
    }
}
