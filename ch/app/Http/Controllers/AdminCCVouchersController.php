<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 25-09-2018
 * Time: 01:11 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\ClubcourierVouchers;
use DB;
use Config;
use View;
use Illuminate\Support\Facades\Input;
class AdminCCVouchersController extends Controller {

    public function index(Request $request) {
        View::share('title', 'Club Courier Voucher Listing');
        session()->flash('searchVoucher', null);
        $DBTables = Config::get('constants.DbTables');
        $t_offers = $DBTables['CCVouchers'];
        $t_ord = $DBTables['CCOrders'];

        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        $offersAry = ClubcourierVouchers::orderBy('dt_from', 'DESC')->paginate($rowsPerPage);
        $usersAry = array();
        if(!empty($offersAry)){
            foreach($offersAry as $key=>$offerData){
                $usersAry = DB::table($t_ord)
                    ->where('offer_id', '=', $offerData->id)
                    ->where('order_status', '>', 1)
                    ->selectRaw('SUM(offer_amnt) as redeemAmount, COUNT(id) as redeemedCount')
                    ->get();
                $offerData->redeemAmount = $usersAry[0]->redeemAmount;
                $offerData->redeemedCount = $usersAry[0]->redeemedCount;
                $offersAry->$key = $offerData;
            }
        }

        return view('pages.ccvouchers.voucher_listing', compact('offersAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function create() {
        View::share('title', 'Add Club Courier Vouchers');
        return view('pages.ccvouchers.voucher_add');
    }

    public function store(Request $request) {
        $DBTables = Config::get('constants.DbTables');
        $t_offers = $DBTables['CCVouchers'];

        Input::merge(array_map('trim', Input::all()));
        $allInput = $request->input();
//        dd($allInput);die;
        $offerType = $allInput['offer_type'];
        $this->dtFrom = $allInput['dt_from'];
        $this->dUpto = $allInput['dt_upto'];
        $rules = array(
            'name' => 'required | max:255',
            'description' => 'required',
            'szCoupnCode' => 'required | max:50 | unique:'.$t_offers,
            'dt_from' => 'required',
            'dt_upto' => 'required',
        );
        if ($offerType == '1') {
            $rules['offer_percntg'] = 'required | numeric | between:0.01,100';
            $rules['offer_amnt'] = '';
        } else if ($offerType == '0') {
            $rules['offer_amnt'] = 'required | numeric | between:0.01,9999999.99';
            $rules['offer_percntg'] = '';
        }
        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        $validator->after(function ($validator) {
            $dtFrom = date("Y-m-d 00:00:00", strtotime($this->dtFrom));
            $dtUpto = date("Y-m-d 23:59:59", strtotime($this->dUpto));

            if (strtotime($dtFrom) >= strtotime($dtUpto)) {
                $validator->errors()->add('dt_upto', 'Voucher valid upto date should be less than voucher from date!');
            }
        });

        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                ->withInput($request->input())
                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {
            $inputData = $request->all();
            $inputData['dt_from'] = date('Y-m-d 00:00:00', strtotime($inputData['dt_from']));
            $inputData['dt_upto'] = date('Y-m-d 23:59:59', strtotime($inputData['dt_upto']));
            $inputData['is_valid'] = 1;
            if ($inputData['offer_type'] == '1') {
                $inputData['offer_amnt'] = '00.00';
            } else if ($inputData['offer_type'] == '0') {
                $inputData['offer_percntg'] = '00.00';
            }

            $offerData = ClubcourierVouchers::create($inputData);
            return redirect()->to('/voucher_management')
                ->with('success', 'New voucher successfully created.');
        }
    }

    public function show($idOffer = 0) {
        if ($idOffer > 0) {
            View::share('title', 'Club Courier Voucher Details');
            $DBTables = Config::get('constants.DbTables');
            $t_offers = $DBTables['CCVouchers'];
            $t_ord = $DBTables['CCOrders'];

            $offerDetails = DB::table($t_offers)
                ->where('id', '=', $idOffer)->get();

            $offerDetailsData = $offerDetails[0];

            $usersAry = DB::table($t_ord)
                ->where('offer_id', '=', $idOffer)
                ->select('id as idOrder', 'user_name', 'user_email', 'payment_date','paid_amnt','offer_amnt')
                ->get();
            $totalTimeOfferReedemed = 0;
            $totalReedemedAmount = 0;
            if(!empty($usersAry)){
                foreach($usersAry as $usersData){
                    $totalTimeOfferReedemed = $totalTimeOfferReedemed+1;
                    $totalReedemedAmount = $totalReedemedAmount+$usersData->offer_amnt;
                }
            }

            $offerDetailsData->redeemedTime = $totalTimeOfferReedemed;
            $offerDetailsData->redeemedAmount = $totalReedemedAmount;

            return view('pages.ccvouchers.voucher_details', compact('offerDetailsData', 'usersAry'));
        } else {
            return redirect()->to("/voucher_management");
        }
    }

    public function edit($id) {
        View::share('title', 'Edit Club Courier Voucher');
        $offerData= ClubcourierVouchers::find($id);
        $offerData->dt_from = date("m/d/Y",strtotime($offerData->dt_from));
        $offerData->dt_upto = date("m/d/Y",strtotime($offerData->dt_upto));
        return view('pages.ccvouchers.voucher_edit',compact('offerData'));
    }

    public function update(Request $request) {
        $id = (int)$request->offerId;
        $DBTables = Config::get('constants.DbTables');
        $t_offers = $DBTables['CCVouchers'];

        Input::merge(array_map('trim', Input::all()));
        $allInput = $request->input();
        $offerType = $allInput['offer_type'];
        $this->dtFrom = $allInput['dt_from'];
        $this->dUpto = $allInput['dt_upto'];
        $rules = array(
            'name' => 'required | max:255',
            'description' => 'required',
            'szCoupnCode' => 'required | max:50 | unique:'.$t_offers.',szCoupnCode'.($id ? ",$id" : ''),
            'dt_from' => 'required',
            'dt_upto' => 'required',
        );
        if ($offerType == '1') {
            $rules['offer_percntg'] = 'required | numeric | between:0.01,100';
            $rules['offer_amnt'] = '';
        } else if ($offerType == '0') {
            $rules['offer_amnt'] = 'required | numeric | between:0.01,9999999.99';
            $rules['offer_percntg'] = '';
        }
        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        $validator->after(function ($validator) {
            $dtFrom = date("Y-m-d 00:00:00", strtotime($this->dtFrom));
            $dtUpto = date("Y-m-d 23:59:59", strtotime($this->dUpto));

            if (strtotime($dtFrom) >= strtotime($dtUpto)) {
                $validator->errors()->add('dt_upto', 'Voucher valid upto date should be less than voucher from date!');
            }
        });

        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                ->withInput($request->input())
                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {
            $inputData = $request->all();
            $inputData['dt_from'] = date('Y-m-d 00:00:00', strtotime($inputData['dt_from']));
            $inputData['dt_upto'] = date('Y-m-d 23:59:59', strtotime($inputData['dt_upto']));
            $inputData['is_valid'] = 1;

            if(isset($inputData['isOneTimeOffer']))
                $inputData['isOneTimeOffer'] = 1;
            else
                $inputData['isOneTimeOffer'] = 0;

            if ($inputData['offer_type'] == '1') {
                $inputData['offer_amnt'] = '00.00';
            } else if ($inputData['offer_type'] == '0') {
                $inputData['offer_percntg'] = '00.00';
            }
            ClubcourierVouchers::find($id)->update($inputData);
            return redirect()->to('/voucher_management')
                ->with('success', 'Voucher details successfully updated.');
        }
    }

    public function destroy(Request $request) {
        $id = $request->idOffer;
        ClubcourierVouchers::find($id)->delete();
        return redirect()->to('/voucher_management')
            ->with('success', 'Voucher successfully deleted.');
    }

    public function searchOffer(Request $request) {
        View::share('title', 'Club Courier Voucher Search');
        $DBTables = Config::get('constants.DbTables');
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        $t_offers = $DBTables['CCVouchers'];
        $t_ord = $DBTables['CCOrders'];

        $previousSearchAry = session('searchVoucher');
        if ($previousSearchAry && !$request->searchAry) {
            $request->searchAry = $previousSearchAry;
        }

        if ($request->searchAry) {
            session()->flash('searchVoucher', $request->searchAry); // Store it as flash data.

            $whereAry = array();

            if (trim($request->searchAry['offer_type']) != '') {
                $whereAry[0] = array('offer_type', '=', (int) $request->searchAry['offer_type']);
            }
            if (trim($request->searchAry['dt_from']) != '') {
                $offerStart = date('Y-m-d 00:00:00', strtotime($request->searchAry['dt_from']));
                $whereAry[1] = array('dt_from', '>=', $offerStart);
            }

            if (trim($request->searchAry['dt_to']) != '') {
                $offerEnd = date('Y-m-d 23:59:59', strtotime($request->searchAry['dt_to']));
                $whereAry[1] = array('dt_upto', '<=', $offerEnd);
            }
//            DB::enableQueryLog();
            if (trim($request->searchAry['offer_name']) != '') {
                $offersAry = DB::table($t_offers)
                    ->where($whereAry)
                    ->Where('name', 'like', trim($request->searchAry['offer_name']) . "%")
                    ->select('*')
                    ->orderBy('id', 'DESC')
                    ->paginate($rowsPerPage);
            }
            else{
                $offersAry = DB::table($t_offers)
                    ->where($whereAry)
                    ->select('*')
                    ->orderBy('id', 'DESC')
                    ->paginate($rowsPerPage);
            }

//            dd(DB::getQueryLog());die;
        } else {
            $offersAry = ClubcourierVouchers::orderBy('dt_from', 'DESC')->paginate($rowsPerPage);
        }

        $usersAry = array();
        if(!empty($offersAry)){
            foreach($offersAry as $key=>$offerData){
                $usersAry = DB::table($t_ord)
                    ->where('offer_id', '=', $offerData->id)
                    ->where('order_status', '>', 1)
                    ->selectRaw('SUM(offer_amnt) as redeemAmount, COUNT(id) as redeemedCount')
                    ->get();
                $offerData->redeemAmount = $usersAry[0]->redeemAmount;
                $offerData->redeemedCount = $usersAry[0]->redeemedCount;
                $offersAry->$key = $offerData;
            }
        }

        return view('pages.ccvouchers.voucher_listing', compact('offersAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

}