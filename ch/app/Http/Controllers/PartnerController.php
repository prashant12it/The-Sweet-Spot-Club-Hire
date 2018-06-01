<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\AdminPartnersController;
use App;
use App\Partners;
use App\Banner;
use DB;
use Config;
use View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class PartnerController extends Controller
{
    protected $hasher;
    
    public function __construct( HasherContract $hasher)
    {
        $this->hasher = $hasher;
        $this->utility = new UtilityController;
        $this->admin_partner = new AdminPartnersController;
    }
    
    public function index(Request $request){
        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to('/partner/dashboard');
        }else{
            return redirect()->to('/partner/logout');
        }
    }
    
    public function login(Request $request){
        View::share('title', 'Partner Login');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to("/partner/dashboard");
        }else{
            $countriesAry = $this->utility->getCountriesList();
            return view('pages.front_partner.login',compact('countriesAry'));
        }
        
    }
    public function signup(Request $request){
        View::share('title', 'Partner Signup');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to("/partner/dashboard");
        }else{
            $countriesAry = $this->utility->getCountriesList();
            return view('pages.front_partner.signup',compact('countriesAry'));
        }
        
    }
    
    public function logout(Request $request){
        session()->forget('partner_credn');
        return redirect()->to("/partner/login")
                ->withCookie(cookie('GLH_PARTNER_CK',null));
    }
    
    public function validate_accnt(request $request){
        
        Input::merge(array_map('trim', Input::all()));

        $rules = array(
            'login_email' => 'required|email',
            'login_password' => 'required| min:1'
        );
        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                            ->withInput($request->input())
                            ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {


            $DBTables = Config::get('constants.DbTables');
            
            $inputData = $request->all();
            $partner = DB::table($DBTables['Partners'])
                        ->where('email','=',trim($inputData['login_email']))
                        ->select('id','name','email','password','iActive')
                        ->get();

            if(count($partner)>0){

                if(Hash::check($inputData['login_password'], $partner[0]->password)){
                    if((int)$partner[0]->iActive == 1){
                        $partnerSession = array();
                        $partnerSession['idPartner'] = $partner[0]->id;
                        $partnerSession['partnerName'] = $partner[0]->name;
                        $partnerSession['partnerEmail'] = $partner[0]->email;
                        session()->put('partner_credn',$partnerSession);
                        
                        if(isset($inputData['remember'])){
                            if(trim($inputData['remember']) == "1"){
                                $encKey1 = "#17GCH#77#";
                                $encKey2="#PCH2017#GcHpl#";
                                $cookieData = $partner[0]->id . "~$encKey1" . $partner[0]->email. "~$encKey2";
                                $encryptedC = base64_encode($cookieData);
                                $cookie_time = 60*24*30;
                                return redirect()->to("/partner/dashboard")
                                        ->withCookie(cookie('GLH_PARTNER_CK',$encryptedC,$cookie_time));
                            }
                        }
                        
                        return redirect()->to("/partner/dashboard");
                        
                    }else{
                        return redirect()->to("/partner/login")
                            ->with('error', "Your account is inactive. Please contact Golf Club Hire administrator."); 
                    }
                }else{
                    return redirect()->to("/partner/login")
                            ->with('error', "Oops, password is not matched."); 
                }
            }else{
               return redirect()->to("/partner/login")
                            ->with('error', 'This email is not registered with Golf Club Hire.'); 
            }
            
        }
    }
    
    public function signup_partner(request $request){
        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to("/partner/dashboard");
        }else{
            Input::merge(array_map('trim', Input::all()));

            $rules = array(
                'name' => 'required | max:255',
                'email' => 'required |unique:partners|unique:users|email',
                'password' => 'required | min:5',
                'address' => 'required',
                'zipcode' => 'required',
                'state' => 'required',
                'country' => 'required|min:1',
                'Terms' => 'required'
            );

            $validator = $this->getValidationFactory()->make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->to($this->getRedirectUrl())
                                ->withInput($request->input())
                                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
            } else {
                $DBTables = Config::get('constants.DbTables');
                $inputData = $request->all();
                $inputData['iActive'] = '1';
                $inputData['password'] = bcrypt($inputData['password']);
                $inputData['reference_id'] = $this->admin_partner->getPartnerReferenceId();

                Partners::create($inputData);

                $BaseURL = Config::get('constants.BaseURL');
                $SupportEmail = Config::get('constants.customerSupportEmail');
                $replace_ary = array();
                $replace_ary['szName'] = $request->name;
                $replace_ary['email'] = $request->email;
                $replace_ary['password'] = $request->password;
                $replace_ary['szLink'] = "<a href='".$BaseURL."/partner/login'>Click here to login</a>";

                $this->utility->createEmail("__NEW_PARTNER_ACC__", $replace_ary, $request->email);
                $countriesAry = $this->utility->getCountriesList($request->country);
                $SupportReplace_ary = array();
                $SupportReplace_ary['szName'] = $request->name;
                $SupportReplace_ary['email'] = $request->email;
                $SupportReplace_ary['address'] = $request->address;
                $SupportReplace_ary['zipcode'] = $request->zipcode;
                $SupportReplace_ary['state'] = $request->state;
                $SupportReplace_ary['country'] = $countriesAry[0]->name;
                $this->utility->createEmail("__NEW_AFFILIATE__", $SupportReplace_ary, $SupportEmail);
                
                $partner = DB::table($DBTables['Partners'])
                                ->where('email','=',trim($inputData['email']))
                                ->select('id','name','email')
                                ->get();
                
                if(count($partner)>0){
                    $partnerSession = array();
                    $partnerSession['idPartner'] = $partner[0]->id;
                    $partnerSession['partnerName'] = $partner[0]->name;
                    $partnerSession['partnerEmail'] = $partner[0]->email;
                    session()->put('partner_credn',$partnerSession);
                    return redirect()->to('/partner/dashboard')
                                ->with('success', 'Your account successfully created.');
                }else{
                    return redirect()->to('/partner/logout');
                }
            }
        }
    }
    
    public function forgot_password(Request $request){
        View::share('title', 'Forgot Password');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to("/partner/dashboard");
        }else{
            return view('pages.front_partner.forgot_password');
        }
    }
    
    public function sendforgotPassword(Request $request){
        View::share('title', 'Forgot Password');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to("/partner/dashboard");
        }else{
            Input::merge(array_map('trim', Input::all()));

            $rules = array(
                'email' => 'required|email'
            );

            $validator = $this->getValidationFactory()->make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->to($this->getRedirectUrl())
                                ->withInput($request->input())
                                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
            } else {
                $DBTables = Config::get('constants.DbTables');
                $inputData = $request->all();
                $partner = DB::table($DBTables['Partners'])
                                ->where('email','=',trim($inputData['email']))
                                ->select('id','name','email')
                                ->get();

                if(count($partner)>0){
                    $reset_key = $partner[0]->id.time();
                    
                    DB::table($DBTables['Partners'])
                                ->where('id','=',(int)$partner[0]->id)
                                ->update(['reset_password_key'=>trim($reset_key)]);
                    
                    $BaseURL = Config::get('constants.BaseURL');
                    $replace_ary = array();
                    $replace_ary['szName'] = $partner[0]->name;
                    
                    $link = $BaseURL."partner/reset_pass/".$reset_key;
                    
                    $replace_ary['szLink'] = "<a href='".$link."'>Click here to reset password</a>";
                    $replace_ary['szHttpsLink'] = $link;

                    $this->utility->createEmail("__FORGOT_PASSWORD__", $replace_ary, $partner[0]->email);
                    
                    return redirect()->to("/partner/login")
                            ->with('success', 'Please check email for password reset instructions.'); 
                    
                }else{
                    return redirect()->to("/partner/forgotPassword")
                            ->with('error', 'This email is not registered with Golf Club Hire.'); 
                }
            }
        }
    }
    
    public function resetPassword(Request $request, $reset_key=''){
        View::share('title', 'Reset Password');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to("/partner/dashboard");
        }else{
            
            if(trim($reset_key) == ''){
                return redirect()->to("/partner/logout");
            }else{
                $DBTables = Config::get('constants.DbTables');
                $partner = DB::table($DBTables['Partners'])
                                ->where('reset_password_key','=',trim($reset_key))
                                ->select('id')
                                ->get();
                
                if(count($partner)>0){
                    return view('pages.front_partner.reset_password',compact('reset_key'));
                }else{
                    return redirect()->to("/partner/forgotPassword")
                            ->with('error', 'Reset password link is expired.');
                }
                
            }
        }
    }
    
    public function updatePassword(Request $request){

        if($this->utility->checkPartnerAlreadyLogin($request)){
            return redirect()->to("/partner/dashboard");
        }else{
            Input::merge(array_map('trim', Input::all()));

            $rules = array(
                'reset_key' => 'required',
                'password' => 'required|min:5',
                'confirm_password' => 'required|min:5|same:password'
            );

            $validator = $this->getValidationFactory()->make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->to($this->getRedirectUrl())
                                ->withInput($request->input())
                                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
            } else {
                $DBTables = Config::get('constants.DbTables');
                $inputData = $request->all();
                $partner = DB::table($DBTables['Partners'])
                                ->where('reset_password_key','=',trim($inputData['reset_key']))
                                ->select('id')
                                ->get();

                if(count($partner)>0){
                    $updateAry = array();
                    $updateAry['reset_password_key'] = '';
                    $updateAry['password'] = bcrypt($inputData['password']);
                    $updateAry['updated_at'] = date('Y-m-d H:i:s');
                    
                    DB::table($DBTables['Partners'])
                                ->where('id','=',(int)$partner[0]->id)
                                ->update($updateAry);
                    
                    return redirect()->to("/partner/login")
                            ->with('success', 'Your new password successfully updated.'); 
                    
                }else{
                    return redirect()->to("/partner/forgotPassword")
                            ->with('error', 'Reset password link is expired.'); 
                }
            }
        }
    }
    
    public function show_profile(Request $request){
        View::share('title', 'Profile');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            $partnerDetails = session()->get("partner_credn");
            View::share('title', 'Profile');
            $partnerId = (int)$partnerDetails['idPartner'];
            $countriesAry = $this->utility->getCountriesList();
            $partner = Partners::find( $partnerId );
            
            return view('pages.front_partner.profile',compact('partner','countriesAry'));
        }else{
            return redirect()->to('/partner/logout');
        }
    }
    
    public function updateProdileDetails(Request $request){

        if($this->utility->checkPartnerAlreadyLogin($request)){
            Input::merge(array_map('trim', Input::all()));
            $partnerDetails = session()->get("partner_credn");
            $partnerId = (int)$partnerDetails['idPartner'];
            $rules = array(
                'name' => 'required | max:255',
                'email' => 'required|email|unique:users,email'.($partnerId ? ",$partnerId" : '').'|unique:partners,email'.($partnerId ? ",$partnerId" : ''),
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
                Partners::find( $partnerId )->update( $inputData );

                return redirect()->to('/partner/dashboard')
                                ->with('success', 'Your profile hass been successfully updated.');
            }
            
        }else{
            return redirect()->to('/partner/logout');
        }
    }
    
    public function changePassword(Request $request){
        View::share('title', 'Change Password');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            View::share('title', 'Change Password');
            
            return view('pages.front_partner.change_password');
        }else{
            return redirect()->to('/partner/logout');
        }
    }
    
    public function updateOldPassword(Request $request){
        
        if($this->utility->checkPartnerAlreadyLogin($request)){
            Input::merge(array_map('trim', Input::all()));
            $partnerDetails = session()->get("partner_credn");
            $partnerId = (int)$partnerDetails['idPartner'];
            $rules = array(
                'old_password' => 'required',
                'password' => 'required|min:5',
                'confirm_password' => 'required|min:5|same:password'
            );
            $validator = $this->getValidationFactory()->make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->to($this->getRedirectUrl())
                                ->withInput($request->input())
                                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
            } else {
                $inputData = $request->all();
                $DBTables = Config::get('constants.DbTables');
                $partner = DB::table($DBTables['Partners'])
                        ->where('id','=',(int)$partnerId)
                        ->select('password')
                        ->get();
            
                if(count($partner)>0){
                    if($this->hasher->check($inputData['old_password'],$partner[0]->password)){
                        $updateAry = array();
                        $updateAry['reset_password_key'] = '';
                        $updateAry['password'] = bcrypt($inputData['password']);
                        $updateAry['updated_at'] = date('Y-m-d H:i:s');

                        DB::table($DBTables['Partners'])
                                    ->where('id','=',(int)$partnerId)
                                    ->update($updateAry);
                        
                        return redirect()->to('/partner/dashboard')
                                ->with('success', 'Your password successfully updated.');
                    }else{
                        return redirect()->to('/partner/change_password')
                                ->with('error', 'Your old password not matched.');
                    }
                }else{
                    redirect()->to('/partner/logout');
                }
            }
            
        }else{
            return redirect()->to('/partner/logout');
        }
    }
    
    public function dashboard(Request $request){
        View::share('title', 'Dashboard');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            $DBTables = Config::get('constants.DbTables');
            $partnerDetails = session()->get("partner_credn");
            $partnerId = (int)$partnerDetails['idPartner'];
            
            $partnerDetails = DB::table($DBTables['Partners'])
                        ->where('id','=',(int)$partnerId)
                        ->select('reference_id')
                        ->get();
            if(count($partnerDetails)>0){
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
            }
                
            
            return view('pages.front_partner.dashboard', compact('partnerDetailsData'));
        }else{
            return redirect()->to('/partner/logout');
        }
    }
    
    public function banners(Request $request){
        View::share('title', 'Banners');
        if($this->utility->checkPartnerAlreadyLogin($request)){
            $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
            $partnerDetails = session()->get("partner_credn");
            $partnerId = (int)$partnerDetails['idPartner'];
            $partner = Partners::find( $partnerId );
            $partner_ref = trim($partner->reference_id);
            $bannersAry = Banner::where('iActive','=',1)->orderBy('id', 'DESC')->paginate($rowsPerPage);
            
            if(count($bannersAry)>0){
                $DBTables = Config::get('constants.DbTables');
                $getSiteUrl = Config::get('constants.SiteUrl');
                foreach($bannersAry as $bannerKey=>$banner){
                    $whereAry = array();
                    $whereAry['banner_ref_key'] = trim($banner->banner_reference_id);
                    $whereAry['partner_ref_key'] = $partner_ref;
                    $banner_count = DB::table($DBTables['Partner_Clicks'])
                                    ->where($whereAry)->select('id')->get();
                    $bannersAry[$bannerKey]->clicks_count = count($banner_count);
                }
            }
            
            return view('pages.front_partner.banners_list', compact('bannersAry','partner_ref','getSiteUrl'))
                    ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
        }else{
            return redirect()->to('/partner/logout');
        }
    }
    
    public function clicks_track(Request $request, $partner_ref='',$banner_ref=''){
        
        if(trim($partner_ref) != ''){
            
            $ip_address = $this->utility->getRealIpAddr();
            
            if(!$this->checkPartnerAffilateClick($partner_ref,$banner_ref,$ip_address)){
                
                $clicked_location = $this->utility->ip_info($ip_address);
                
                $DBTables = Config::get('constants.DbTables');
                $insertAry = array();
                $insertAry['partner_ref_key'] = trim($partner_ref);
                $insertAry['banner_ref_key'] = trim($banner_ref);
                $insertAry['dt_clicked_on'] = date('Y-m-d H:i:s');
                $insertAry['ip_click_from'] = trim($ip_address);
                $insertAry['clicked_location'] = serialize($clicked_location);
                
                DB::table($DBTables['Partner_Clicks'])->insert($insertAry);
                $cookieVal = $partner_ref.'~'.$banner_ref.'~'.$ip_address;
                /*$cookie_time = 60*24*30;
                setcookie( 'partner_tss_val', $cookieVal, time() + ( $cookie_time ), "/" );
                return redirect()->to("/");*/
                session()->put('tsspart', $cookieVal);
                if(!$request->cookie('TSS_PARTNER')){
                    $partnerEncKey1 = "#17TssPC#77#";
                    $partnerEncKey2="#PCC2017#tSsPcC#";
                    $partnerEncKey3="#PaPTsSc#";

                    $cookieData = $partner_ref . "~$partnerEncKey1" . $banner_ref. "~$partnerEncKey2" . $ip_address. "~$partnerEncKey3";
                    $encryptedC = base64_encode($cookieData);

                    $cookie_time = 60*24*30;
                    return redirect()->to("/")
                            ->withCookie(cookie('TSS_PARTNER',$encryptedC,$cookie_time));
                    /*return redirect()->to("/")
                        ->withCookie(cookie('TSS_PARTNER',$cookieVal,$cookie_time));*/
                }else{
                    return redirect()->to('/');
                }
                
            }else{
                return redirect()->to('/');
            }
        }else{
            return redirect()->to('/');
        }
        
    }
    
    public function checkPartnerAffilateClick($partner_ref='',$banner_ref='',$ip_address=''){
        
        $clickExit = false;
                
        $whereAry = array();
        if(trim($partner_ref) != ''){

            $whereAry['partner_ref_key'] = trim($partner_ref);

            if(trim($banner_ref) != '')
                $whereAry['banner_ref_key'] = trim($banner_ref);

            if(trim($ip_address) != '')
                $whereAry['ip_click_from'] = trim($ip_address);

            $dtClickedFrom = date('Y-m-d 00:00:00');
            $dtClickedTo = date('Y-m-d 23:59:59');

            $DBTables = Config::get('constants.DbTables');
            $partner_saved_clicks = DB::table($DBTables['Partner_Clicks'])
                                    ->where($whereAry)
                                    ->where('dt_clicked_on','>=',$dtClickedFrom)
                                    ->where('dt_clicked_on','<=',$dtClickedTo)
                                    ->select('id')
                                    ->get();

            if(count($partner_saved_clicks) >0){
                $clickExit = true;
            }

        }

        return $clickExit;
    }

    public function deleteBanner(Request $request){
        $bannerId = $request->input('bannerid');
        $res = Banner::where('id','=',(int)$bannerId)->delete();
        if($res){
            die('SUCCESS');
        }else{
            die('FAILURE');
        }
    }
}
