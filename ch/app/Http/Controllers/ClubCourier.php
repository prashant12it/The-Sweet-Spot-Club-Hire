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
        $this->hire = new HireController;
        $this->utility = new UtilityController;
        $this->DBTables = Config::get('constants.DbTables');
    }

    function index(){
        ob_end_clean();
        ob_start();
        session_start();
        View::share('title', 'Courier Booking');
        View::share('Page', 'booking');
        View::share('PageHeading', 'Golf Club Hire Australia');
        $orderId = session()->get('orderId');
        if($orderId>0){
            $orderDetArr = DB::table($this->DBTables['CCOrders'])
                ->where('id', '=', $orderId)
                ->get();
            if(count($orderDetArr)>0){
                $orderDetails = $orderDetArr[0];
                $bagArr = DB::table($this->DBTables['CCOrders_Products'])
                    ->where('order_id', '=', $orderId)
                    ->get();
                $outShipPrice = 0;
                $retShipPrice = 0;
                if(count($bagArr)>0){
                    foreach ($bagArr as $bag){
                        $outShipPrice = $outShipPrice + $bag->sub_total_amnt_out;
                        $retShipPrice = $retShipPrice + $bag->sub_total_amt_ret;
                    }
                }
                return view('pages.clubcourier.booking', compact('orderDetails','bagArr','outShipPrice','retShipPrice'));
            }
        }else{
            return view('pages.clubcourier.booking');
        }
    }

    function prefilledBookingData(Request $request){

    }

    function getRegionById($id){
        $RegionArr = DB::table($this->DBTables['CCRegion'])
            ->where('id', '=', $id)
            ->select('region')
            ->get();
        return $RegionArr;
    }

    function getCourierPrice($fromId,$toId){
        $PriceArr = DB::table($this->DBTables['CCCost'])
            ->where([['from_region_id', '=', $fromId],['to_region_id', '=', $toId]])
            ->select('id','small_bag_cost','standard_bag_cost','large_bag_cost','transit_days')
            ->get();
        return $PriceArr;
    }
    public function formatDates($dateval){
        $dateArr = explode('/',$dateval);
        $datenewval = $dateArr[2].'-'.$dateArr[0].'-'.$dateArr[1];
        return $datenewval;
    }
    function addBooking(Request $request){
        ob_end_clean();
        ob_start();
        session_start();
        $orderId = session()->get('orderId');
        $response = array();
        Input::merge(array_map('trim', Input::all()));
        $allInput = $request->input();
        $rules = array(
            'ccp_name' => 'required | min:1 | max:255',
            'ccp_email' => 'required | email | min:1 | max:255',
            'ccp_phone' => 'required | min:1 | max:15',
            'ccp_pickup_region' => 'required | numeric | min:1',
            'ccp_company_name' => 'required | min:1 | max:255',
            'ccp_contact_name' => 'required | min:1 | max:100',
            'ccp_conatct_phone' => 'required | min:1 | max:15',
            'ccp_address' => 'required | min:1 | max:255',
            'ccp_suburb' => 'required | min:1 | max:150',
            'ccp_postcode' => 'required | min:1 | max:10',
            'ccp_date' => 'required | min:1 | max:10',
            'ccd_dropoff_region' => 'required | numeric | min:1',
            'ccd_company_name' => 'required | min:1 | max:255',
            'ccd_contact_name' => 'required | min:1 | max:100',
            'ccd_conatct_phone' => 'required | min:1 | max:15',
            'ccd_address' => 'required | min:1 | max:255',
            'ccd_suburb' => 'required | min:1 | max:150',
            'ccd_postcode' => 'required | min:1 | max:10',
            'bagTitle1' => 'required',
            'bagType1' => 'required',
            'here_abt_us' => 'min:1 | max:100'
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                ->withInput($request->input())
                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {
            $pickupRegion = '';
            $destinationRegion = '';
            $ReturnPickupRegion = '';
            $ReturnDestinationRegion = '';
            $retShipArr = array();
            $outShippingCost = 0;
            $retShippingCost = 0;
            $transitOut = 0;
            $transitret = 0;
            $voucherId = 0;
            $voucherName = '';
            $voucherDesc = '';
            $voucherCode = '';
            $voucherType = 0;
            $voucherPercentage = 0;
            $voucherAmount = 0;
            $PRegionArr = $this->getRegionById($allInput['ccp_pickup_region']);
            if (count($PRegionArr) > 0) {
                $pickupRegion = $PRegionArr[0]->region;
            }else{
                $response['status'] = "ERROR";
                $response['errors'] = "You have selected an invalid region.";
            }
            $DRegionArr = $this->getRegionById($allInput['ccd_dropoff_region']);
            if (count($DRegionArr) > 0) {
                $destinationRegion = $DRegionArr[0]->region;
            }else{
                $response['status'] = "ERROR";
                $response['errors'] = "You have selected an invalid region.";
            }
            if ($allInput['shipOpt'] == 2) {
                $RETPRegionArr = $this->getRegionById($allInput['retccp_pickup_region']);
                if (count($DRegionArr) > 0) {
                    $ReturnPickupRegion = $RETPRegionArr[0]->region;
                }else{
                    $response['status'] = "ERROR";
                    $response['errors'] = "You have selected an invalid region.";
                }
                $RETdestinationRegion = $this->getRegionById($allInput['retccd_dropoff_region']);
                if (count($RETdestinationRegion) > 0) {
                    $ReturnDestinationRegion = $RETdestinationRegion[0]->region;
                }else{
                    $response['status'] = "ERROR";
                    $response['errors'] = "You have selected an invalid region.";
                }
                $retShipArr = $this->getCourierPrice($allInput['retccp_pickup_region'], $allInput['retccd_dropoff_region']);
                $transitret = $retShipArr[0]->transit_days;
            }
            $outShipArr = $this->getCourierPrice($allInput['ccp_pickup_region'], $allInput['ccd_dropoff_region']);
            $transitOut = $outShipArr[0]->transit_days;
            $actualBagCount = 0;
            for ($i = 1; $i <= $allInput['bagcount']; $i++) {
                if (!empty($allInput['bagTitle' . $i])) {
                    if (count($outShipArr) > 0) {
                        if ($allInput['bagType' . $i] == 1) {
                            $outShippingCost = $outShippingCost + $outShipArr[0]->standard_bag_cost;
                        } elseif ($allInput['bagType' . $i] == 2) {
                            $outShippingCost = $outShippingCost + $outShipArr[0]->large_bag_cost;
                        } elseif ($allInput['bagType' . $i] == 3) {
                            $outShippingCost = $outShippingCost + $outShipArr[0]->small_bag_cost;
                        } else {
                            $outShippingCost = $outShippingCost + 0;
                        }
                    }else{
                        $response['status'] = "ERROR";
                        $response['errors'] = "Something goes wrong. Please try after sometime.";
                    }
                    if (count($retShipArr) > 0) {
                        if ($allInput['shipOpt'] == 2) {
                            if ($allInput['bagType' . $i] == 1) {
                                $retShippingCost = $retShippingCost + $retShipArr[0]->standard_bag_cost;
                            } elseif ($allInput['bagType' . $i] == 2) {
                                $retShippingCost = $retShippingCost + $retShipArr[0]->large_bag_cost;
                            } elseif ($allInput['bagType' . $i] == 3) {
                                $retShippingCost = $retShippingCost + $retShipArr[0]->small_bag_cost;
                            } else {
                                $retShippingCost = $retShippingCost + 0;
                            }
                        }
                    }
                    $actualBagCount++;
                }
            }
            if($allInput['outshipment'] == 2){
                $outShippingCost = $outShippingCost + 20;
            }
            if($allInput['shipOpt'] == 2 && $allInput['returnshipment'] == 2){
                $retShippingCost = $retShippingCost + 20;
            }
            $SubTotalCost = $outShippingCost + $retShippingCost;
            $InitialSubTotalCost = $SubTotalCost;
            $multiSetDiscount = $this->getMultiSetDiscountedPrice($actualBagCount,$InitialSubTotalCost);
            $SubTotalCost = $InitialSubTotalCost - $multiSetDiscount;
            if(!empty($allInput['voucher_code'])){
                $voucherArr = $this->hire->getVoucherDetails(date('Y-m-d'),$allInput['voucher_code']);
                if(count($voucherArr)>0){
                    $voucherId = $voucherArr[0]->id;
                    $voucherName = $voucherArr[0]->name;
                    $voucherCode = $voucherArr[0]->szCoupnCode;
                    $voucherDesc = $voucherArr[0]->description;
                    $voucherType = $voucherArr[0]->offer_type;
                    $voucherPercentage = $voucherArr[0]->offer_percntg;
                    $voucherAmount = $voucherArr[0]->offer_amnt;
                }
            }
            if($voucherPercentage>0){
                $voucherAmount = $SubTotalCost*$voucherPercentage*0.01;
            }
            $TotalCost = $SubTotalCost - $voucherAmount;
            $InsertArr = ['order_reference_id' => time(),
                'user_name' => $allInput['ccp_name'],
                'user_email' => $allInput['ccp_email'],
                'user_phone' => $allInput['ccp_phone'],
                'multiset_discount' => $multiSetDiscount,
                'offer_id' => $voucherId,
                'offer_name' => $voucherName,
                'offer_description' => $voucherDesc,
                'offer_Code' => $voucherCode,
                'offer_type' => $voucherType,
                'offer_percntg' => $voucherPercentage,
                'offer_amnt' => $voucherAmount,
                'total_amnt' => $TotalCost,
                'sub_total_amnt' => $InitialSubTotalCost,
                'order_status' => 0,
                'pickup_region' => $pickupRegion,
                'pickup_date' => $this->formatDates($allInput['ccp_date']),
                'pickup_company_name' => $allInput['ccp_company_name'],
                'pickup_contact_name' => $allInput['ccp_contact_name'],
                'pickup_phone_num' => $allInput['ccp_conatct_phone'],
                'pickup_address' => $allInput['ccp_address'],
                'pickup_suburb' => $allInput['ccp_suburb'],
                'pickup_postal_code' => $allInput['ccp_postcode'],
                'pickup_delivery_note' => $allInput['ccp_collection_notes'],
                'destination_region' => $destinationRegion,
                'destination_company_name' => $allInput['ccd_company_name'],
                'destination_contact_name' => $allInput['ccd_contact_name'],
                'destination_phone_num' => $allInput['ccd_conatct_phone'],
                'destination_address' => $allInput['ccd_address'],
                'destination_suburb' => $allInput['ccd_suburb'],
                'destination_postal_code' => $allInput['ccd_postcode'],
                'destination_note' => $allInput['ccd_collection_notes'],
                'return_region' => $ReturnPickupRegion,
                'return_date' => (!empty($allInput['retccp_date'])?$this->formatDates($allInput['retccp_date']):''),
                'return_company_name' => (!empty($allInput['retccp_company_name'])?$allInput['retccp_company_name']:''),
                'return_contact_name' => (!empty($allInput['retccp_contact_name'])?$allInput['retccp_contact_name']:''),
                'return_phone_num' => (!empty($allInput['retccp_conatct_phone'])?$allInput['retccp_conatct_phone']:''),
                'return_address' => (!empty($allInput['retccp_address'])?$allInput['retccp_address']:''),
                'return_suburb' => (!empty($allInput['retccp_suburb'])?$allInput['retccp_suburb']:''),
                'return_postal_code' => (!empty($allInput['retccp_postcode'])?$allInput['retccp_postcode']:''),
                'return_collection_note' => (!empty($allInput['retccp_collection_notes'])?$allInput['retccp_collection_notes']:''),
                'return_d_region' => $ReturnDestinationRegion,
                'return_d_company_name' => (!empty($allInput['retccd_company_name'])?$allInput['retccd_company_name']:''),
                'return_d_contact_name' => (!empty($allInput['retccd_contact_name'])?$allInput['retccd_contact_name']:''),
                'return_d_phone_num' => (!empty($allInput['retccd_conatct_phone'])?$allInput['retccd_conatct_phone']:''),
                'return_d_address' => (!empty($allInput['retccd_address'])?$allInput['retccd_address']:''),
                'return_d_suburb' => (!empty($allInput['retccd_suburb'])?$allInput['retccd_suburb']:''),
                'return_d_postal_code' => (!empty($allInput['retccd_postcode'])?$allInput['retccd_postcode']:''),
                'return_d_note' => (!empty($allInput['retccd_collection_notes'])?$allInput['retccd_collection_notes']:''),
                'outgoing_shipment' => (!empty($allInput['outshipment'])?$allInput['outshipment']:''),
                'return_shipment' => ($allInput['shipOpt'] == 2?$allInput['returnshipment']:0),
                'transit_days_out' => $transitOut,
                'transit_days_out' => $transitOut,
                'here_abt_us' => $allInput['here_abt_us']
            ];
            if(isset($orderId) && $orderId>0){
                $res = DB::table($this->DBTables['CCOrders'])
                    ->where('id', '=', $orderId)
                    ->update($InsertArr);
                if($res){
                    DB::table($this->DBTables['CCOrders'])
                        ->where('id', '=', $orderId)
                        ->update(['dtUpdatedOn'=>date('Y-m-d H:i:s')]);
                    DB::table($this->DBTables['CCOrders_Products'])->where('order_id', '=', $orderId)->delete();
                }
            }else{
                $orderId = DB::table($this->DBTables['CCOrders'])->insertGetId($InsertArr);
                if($orderId){
                    DB::table($this->DBTables['CCOrders'])
                        ->where('id', '=', $orderId)
                        ->update(['dtCreatedOn' => date('Y-m-d H:i:s')]);
                }
            }

            if($orderId>0){
                session()->put('orderId', $orderId);
                for ($i = 1; $i <= $allInput['bagcount']; $i++) {
                    if (!empty($allInput['bagTitle' . $i])) {
                        if (count($outShipArr) > 0) {
                            if ($allInput['bagType' . $i] == 1) {
                                $orderSProdId = DB::table($this->DBTables['CCOrders_Products'])->insertGetId(
                                    ['order_id' => $orderId,
                                        'product_name'=>'Standard bag (30x35x123cm)',
                                        'bag_title'=>$allInput['bagTitle' . $i],
                                        'quantity'=>1,
                                        'sub_total_amnt_out'=>$outShipArr[0]->standard_bag_cost,
                                        'sub_total_amt_ret'=>(count($retShipArr) > 0 && $allInput['shipOpt'] == 2?$retShipArr[0]->standard_bag_cost:0)]
                                );
                                if($orderSProdId>0){
                                    $outShippingCost = $outShippingCost + $outShipArr[0]->standard_bag_cost;
                                }else{
                                    $response['status'] = "ERROR";
                                    $response['errors'] = "Something goes wrong. Please try after sometime.";
                                }
                            } elseif ($allInput['bagType' . $i] == 2) {

                                $orderLProdId = DB::table($this->DBTables['CCOrders_Products'])->insertGetId(
                                    ['order_id' => $orderId,
                                        'product_name'=>'Large bag (35x40x123cm)',
                                        'bag_title'=>$allInput['bagTitle' . $i],
                                        'quantity'=>1,
                                        'sub_total_amnt_out'=>$outShipArr[0]->large_bag_cost,
                                        'sub_total_amt_ret'=>(count($retShipArr) > 0 && $allInput['shipOpt'] == 2?$retShipArr[0]->large_bag_cost:0)]
                                );
                                if($orderLProdId>0){
                                    $outShippingCost = $outShippingCost + $outShipArr[0]->large_bag_cost;
                                }else{
                                    $response['status'] = "ERROR";
                                    $response['errors'] = "Something goes wrong. Please try after sometime.";
                                }
                            } elseif ($allInput['bagType' . $i] == 3) {

                                $orderLProdId = DB::table($this->DBTables['CCOrders_Products'])->insertGetId(
                                    ['order_id' => $orderId,
                                        'product_name'=>'Small bag (30x30x123cm)',
                                        'bag_title'=>$allInput['bagTitle' . $i],
                                        'quantity'=>1,
                                        'sub_total_amnt_out'=>$outShipArr[0]->small_bag_cost,
                                        'sub_total_amt_ret'=>(count($retShipArr) > 0 && $allInput['shipOpt'] == 2?$retShipArr[0]->small_bag_cost:0)]
                                );
                                if($orderLProdId>0){
                                    $outShippingCost = $outShippingCost + $outShipArr[0]->small_bag_cost;
                                }else{
                                    $response['status'] = "ERROR";
                                    $response['errors'] = "Something goes wrong. Please try after sometime.";
                                }
                            } else {
                                $outShippingCost = $outShippingCost + 0;
                            }
                        }else{
                            $response['status'] = "ERROR";
                            $response['errors'] = "Something goes wrong. Please try after sometime.";
                        }
                        if (count($retShipArr) > 0) {
                            if ($allInput['shipOpt'] == 2) {
                                if ($allInput['bagType' . $i] == 1) {
                                    $retShippingCost = $retShippingCost + $retShipArr[0]->standard_bag_cost;
                                } elseif ($allInput['bagType' . $i] == 2) {
                                    $retShippingCost = $retShippingCost + $retShipArr[0]->large_bag_cost;
                                } elseif ($allInput['bagType' . $i] == 3) {
                                    $retShippingCost = $retShippingCost + $retShipArr[0]->small_bag_cost;
                                } else {
                                    $retShippingCost = $retShippingCost + 0;
                                }
                            }
                        }
                    }
                }
            }else{
                $response['status'] = "ERROR";
                $response['errors'] = "Something goes wrong while saving your order details. Please try after sometime.";
            }
            if(!empty($response) && $response['status'] == 'ERROR'){
                return $response;
            }else{
                return redirect()->to('/clubcourier/preview-booking');
            }
        }
    }

    public function getMultiSetDiscountedPrice($setsCount, $totalPrice)
    {
        $DiscountList = Config::get('constants.CCDiscount');
        $discountPrice = 0;
        if ($setsCount > 1 && $setsCount < 11) {
            $discountPrice = $totalPrice * $DiscountList[$setsCount] * 0.01;
        } elseif ($setsCount > 10) {
            $discountPrice = $totalPrice * $DiscountList['11'] * 0.01;
        }

        return $discountPrice;
    }

    function getEstimatedDeliveryTime(Request $request){
        Input::merge(array_map('trim', Input::all()));
        $allInput = $request->input();
        $PickupReg = $allInput['pickup'];
        $DropReg = $allInput['drop'];
        $dataArr = DB::table($this->DBTables['CCCost'])
            ->where([['from_region_id','=',$PickupReg],['to_region_id','=',$DropReg]])
            ->get();
        if(count($dataArr)>0) {
            $responseData = array("code"=>200,"days" => $dataArr[0]->transit_days, "small" => $dataArr[0]->small_bag_cost, 'large' => $dataArr[0]->large_bag_cost);
        }else{
            $responseData = array("code"=>201);
        }
        header('Content-Type: application/json');
        echo json_encode($responseData);
    }

    function previewBooking(){
        ob_end_clean();
        ob_start();
        session_start();
        $orderId = session()->get('orderId');
        if($orderId>0){
            View::share('title', 'Order Summary');
            View::share('Page', 'preview');
            View::share('PageHeading', 'Golf Club Hire Australia');
            $orderDetArr = DB::table($this->DBTables['CCOrders'])
                ->where('id', '=', $orderId)
                ->get();
            if(count($orderDetArr)>0){
                $orderDetails = $orderDetArr[0];
                $bagArr = DB::table($this->DBTables['CCOrders_Products'])
                    ->where('order_id', '=', $orderId)
                    ->get();
                $outShipPrice = 0;
                $retShipPrice = 0;
                if(count($bagArr)>0){
                    foreach ($bagArr as $bag){
                        $outShipPrice = $outShipPrice + $bag->sub_total_amnt_out;
                        $retShipPrice = $retShipPrice + $bag->sub_total_amt_ret;
                    }
                }
                return view('pages.clubcourier.preview', compact('orderDetails','bagArr','outShipPrice','retShipPrice'));
            }else{
                return redirect()->to('/clubcourier/booking');
            }
        }else{
            return redirect()->to('/clubcourier/booking');
        }
    }

    public function getOrderByTransactionId($transactionId){
        $orderDetArr = DB::table($this->DBTables['CCOrders'])
            ->where('payment_transaction_id', '=', $transactionId)
            ->get();
        return $orderDetArr;
    }

    public function updatePaymentOptByParameter($orderRefId,$paymentOpt){
        $result = DB::table($this->DBTables['CCOrders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->update(['payment_option' => $paymentOpt,
                'order_status' => 5,
                'dtUpdatedOn'=> date('Y-m-d h:i:s')]);
        return $result;
    }
    public function updateOrderStatus($orderRefId,$status){
        $result = DB::table($this->DBTables['CCOrders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->update(['order_status' => $status,
                'dtUpdatedOn'=> date('Y-m-d h:i:s')]);
        return $result;
    }

    public function updateInvoiceNo($invoiceNo, $orderRefId, $transactionId)
    {
        $result = DB::table($this->DBTables['CCOrders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->update([
                'invoice_no' => $invoiceNo,
                'payment_transaction_id' => $transactionId
            ]);
        return $result;
    }

    public function updateOrderPaymentDetails($orderRefId, $payerId,$transactionId,$status,$paidAmount)
    {
        $result = DB::table($this->DBTables['CCOrders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->update([
                'merchant_email' => $payerId,
                'payment_transaction_id' => $transactionId,
                'paid_amnt' => $paidAmount,
                'payment_success_response' => 'Payment done successfully.',
                'order_status' => $status,
                'payment_date' => date('Y-m-d H:i:s'),
                'purchased_date' => date('Y-m-d H:i:s')
            ]);
        return $result;
    }


    public function checkOrderByPaymentRefId($transactionId)
    {
        $OrderDet = DB::table($this->DBTables['CCOrders'])
            ->where('payment_transaction_id', '=', $transactionId)
            ->get();
        return $OrderDet;
    }

    public function thank_you(Mail $mandrill, Request $request)
    {
        if (isset($request->idStatus) && $request->idStatus == '0') {
            $idOrder = (int)$request->idOrder;
            $updateAry = array();
            $updateAry['order_status'] = 3;
            DB::table($this->DBTables['CCOrders'])->where('order_reference_id', $idOrder)->update($updateAry);
            return redirect()->to("/club_courier_disputed_orders")
                ->with('success', 'Order cancelled successfully.');
        } else {
            $supportEmail = Config::get('constants.CCcustomerSupportEmail');
//            $parm = Input::get();
            $trnsId = session()->get('transactionId');
            $PaymentRefId = (isset($request->idOrder) ? $request->idOrder : (isset($trnsId) && !empty($trnsId)?$trnsId:''));
            View::share('title', 'Club Courier Thank You!');
            View::share('PageDescription1', 'Thank you for booking your club from the Sweet Spot Club Courier.');
            View::share('PageDescription2', 'We will aim to provide the best quality service for you and will be there for whatever you need along the way. Feel free to contact us via email at info@tssclubhire or via our social media pages @tssclubhire if you have any questions or concerns. We will be sending you information in regards to your hire closer to your initial hire date, with all the details you will require. Thank you again for choosing The Sweet Spot and we hope your next golf experience is a great one.');
            $FirstTime = '0';
            $discountErr = '0';
            if (!empty($PaymentRefId)) {
                $checkOrderExistArr = $this->checkOrderByPaymentRefId($PaymentRefId);
                if (count($checkOrderExistArr) > 0) {
                    $checkOrderExist = $checkOrderExistArr[0];
                    $response['status'] = "";
                    $response['errors'] = "";
                    $error = $response['errors'];
                    $errorFlag = 0;
                    return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr','errorFlag'));
                } else {
                    $order_reference_id = (isset($request->idOrder) ? $request->idOrder : '');
                    $FirstTime = '1';
                    $xero = App::make('XeroPrivate');
                    $contact = App::make('XeroContact');
                    $invoice = App::make('XeroInvoice');
                    $orderDetails = array();
                    $orderDetArr = DB::table($this->DBTables['CCOrders'])
                        ->where('order_reference_id', '=', $order_reference_id)
                        ->get();
                    /*if(count($orderDetArr)>0) {
                        $orderDetails = $orderDetArr[0];
                    }
                    $orderDetailsAry = $this->hire->getPreOrderDetails($order_reference_id);*/

                    if (count($orderDetArr) > 0) {
                        $orderDetails = $orderDetArr[0];
                        $contact->setContactStatus('ACTIVE');
                        $contact->setName($orderDetails->user_name);
                        /*$contact->setFirstName($orderDetails->buyer_first_name);
                        $contact->setLastName($orderDetails->buyer_last_name);*/
                        $contact->setEmailAddress($orderDetails->user_email);
                        $contact->setDefaultCurrency('AUD');
                        $invoice->setContact($contact);
                        $invoice->setType('ACCREC');
                        $invoice->setDate(Carbon::now());
                        $invoice->setDueDate(Carbon::now()->addDays(4));
                        $invoice->setLineAmountType('Exclusive');
                        $invoice->setStatus('AUTHORISED');
                        $bagArr = DB::table($this->DBTables['CCOrders_Products'])
                            ->where('order_id', '=', $orderDetails->id)
                            ->get();
                        /*$outShipPrice = 0;
                        $retShipPrice = 0;
                        if(count($bagArr)>0){
                            foreach ($bagArr as $bag){
                                $outShipPrice = $outShipPrice + $bag->sub_total_amnt_out;
                                $retShipPrice = $retShipPrice + $bag->sub_total_amt_ret;
                            }
                        }*/
                        /*$PreOrderProductsArr = $this->hire->getCartByRefId($order_reference_id, array(), $orderDetails->state_id);
                        $setCount = $this->hire->getCartSetCount($order_reference_id);*/
                        $i = 0;
                        $line = array();
                        if (count($bagArr) > 0) {
                            $discountFlag = true;
                            $disCount = 0;
                            foreach ($bagArr as $bag){
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription(strip_tags($bag->bag_title.' - '.$bag->product_name));
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount($bag->sub_total_amnt_out+$bag->sub_total_amt_ret);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if (!empty($orderDetails->offer_Code)) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Offer Code Applied - "' . $orderDetails->offer_Code . '"');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount('-' . $orderDetails->offer_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if($orderDetails->multiset_discount >0){
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Multiset Discount');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount('-' . $orderDetails->multiset_discount);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if($orderDetails->outgoing_shipment == 2){
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Express courier - Outgoing');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount(20);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if($orderDetails->return_shipment == 2){
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Express courier - Return');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount(20);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                        }
                        $line[$i] = App::make('XeroInvoiceLine');
                        $xero->save($invoice);
                        $invoicesArr = XeroPrivate::load('Accounting\\Invoice')->execute();
                        $totalInvoices = count($invoicesArr);
                        $line[$i]->setAccountCode('SALES');
                        $this->updateInvoiceNo($invoicesArr[$totalInvoices - 1]->InvoiceNumber, $order_reference_id, $PaymentRefId);
                        /*Template Data For Mandrill START*/
                        /*$cartDetailArr = $this->hire->getCartByRefId($order_reference_id, array(), $orderDetails->state_id);
                        $cartDetailArr = $this->hire->getCart($cartDetailArr);
                        $preOrderArr = $this->hire->getPreOrderDetails($order_reference_id);
                        $orderDetails = $preOrderArr[0];

                        $insurance = 0;
                        if (!empty($orderDetails)) {
                            $insurance = $orderDetails->insurance_amnt;
                        }

                        $countriesAry = $this->utility->getCountriesList($orderDetails->buyer_country);
                        $DeliverystatesAry = $this->utility->getStatesList($orderDetails->delvr_state_id);
                        $PickupstatesAry = $this->utility->getStatesList($orderDetails->pickup_state_id);
                        $country = $countriesAry[0]->name;
                        $Deliverystates = $DeliverystatesAry[0]->name;
                        $Pickupstates = $PickupstatesAry[0]->name;*/

                        $templateData = '<table cellspacing="1" cellpadding="4" border="1">
				<thead>
				<tr>
					<th align="left">YOUR ORDER : ' .$order_reference_id . '</th>
					<th align="left">COST</th>
				</tr>
				</thead>
				<tbody>';
                        $total = 0;
                        if (count($bagArr) > 0) {
                            foreach ($bagArr as $bag){
                                $templateData .= '<tr>
					<td align="left"><b>' . $bag->bag_title.'</b> - '.$bag->product_name . '</td>
					<td align="left">$' . number_format($bag->sub_total_amnt_out+$bag->sub_total_amt_ret, 2, '.', ',') . '</td>
				</tr>';

                            }
                            if($orderDetails->outgoing_shipment == 2){
                                $templateData .='<tr><td align="left">Express Courier Charge - Outgoing</td>
<td align="left">$20</td></tr>';
                            }
                            if($orderDetails->return_shipment == 2){
                                $templateData .='<tr><td align="left">Express Courier Charge - Return</td>
<td align="left">$20</td></tr>';
                            }
                        }
                        $templateData .= '</tbody>
			</table><br />
			<table cellspacing="1" cellpadding="1" border="1">
				<tbody>
				<tr>
					<td align="left"><strong>SUB TOTAL</strong></td>
					<td align="left">$' . number_format($orderDetails->sub_total_amnt, 2, '.', ',') . '</td>
				</tr>
                <tr>
                    <td align="left"><strong>VOUCHER CODE</strong></td>
                    <td align="left">' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_Code : 'N/A') . '</td>
                </tr>
                <tr>
                    <td align="left"><strong>VOUCHER DISCOUNT</strong></td>
                    <td align="left"><b>- </b>$' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_amnt : '0.00') . '</td>
                </tr>
                <tr>
                    <td align="left"><strong>MULTISET DISCOUNT</strong></td>
                    <td align="left"><b>- </b>$' . $orderDetails->multiset_discount . '</td>
                </tr>
				<tr>
					<td align="left"><strong>TOTAL</strong></td>
					<td align="left">$' . number_format($orderDetails->total_amnt, 2, '.', ',') . '</td>
				</tr>
				</tbody>
			</table><br />
			<h2>Your Details</h2>
			<br />
			<table cellspacing="1" cellpadding="4" border="1">
			<tr>
			<th align="left">Name</th>
			<td align="left">' . $orderDetails->user_name . '</td>
</tr>
<tr>
			<th align="left">Email</th>
			<td align="left"><a href="mailto:' . $orderDetails->user_email . '">' . $orderDetails->user_email . '</a></td>
</tr>
<tr>
			<th align="left">Contact Number</th>
			<td align="left"><a href="tel:' . $orderDetails->user_phone . '">' . $orderDetails->user_phone . '</a></td>
</tr>
</table><br />
<table cellpadding="1" cellspacing="4" border="1">
<tr>
<th colspan="2" align="left">Delivery Details (Pick up) '.date('jS M Y',strtotime($orderDetails->pickup_date)).'</th>
<th colspan="2" align="left">Delivery Details (Drop off)</th>
</tr>
<tr>
<th align="left">Region of pickup</th>
<td align="left">' . $orderDetails->pickup_region . '</td>
<th align="left">Region of drop off</th>
<td align="left">' . $orderDetails->destination_region . '</td>
</tr>
<tr>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->pickup_company_name . '</td>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->destination_company_name . '</td>
</tr>
<tr>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->pickup_contact_name . '</td>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->destination_contact_name . '</td>
</tr>
<tr>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->pickup_phone_num . '</td>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->destination_phone_num . '</td>
</tr>
<tr>
<th align="left">Address </th>
<td align="left">' . $orderDetails->pickup_address . '</td>
<th align="left">Address </th>
<td align="left">' . $orderDetails->destination_address . '</td>
</tr>
<tr>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->pickup_suburb . '</td>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->destination_suburb . '</td>
</tr>
<tr>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->pickup_postal_code . '</td>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->destination_postal_code . '</td>
</tr>
<tr>
<th align="left">Collection Note </th>
<td align="left">' . $orderDetails->pickup_delivery_note . '</td>
<th align="left">Delivery Note </th>
<td align="left">' . $orderDetails->destination_note . '</td>
</tr>
<tr>
<th colspan="2" align="left">Shipping Option</th>
<th colspan="2" align="left">'.(!empty($orderDetails->return_region)?'Return':'One way').'</th>
</tr>';
                        if(!empty($orderDetails->return_region)){
                            $templateData .='<tr>
<th colspan="2" align="left">Return Delivery Details (Pick up) '.date('jS M Y',strtotime($orderDetails->return_date)).'</th>
<th colspan="2" align="left">Return Delivery Details (Drop off)</th>
</tr>
<tr>
<th align="left">Region of pickup</th>
<td align="left">' . $orderDetails->return_region . '</td>
<th align="left">Region of drop off</th>
<td align="left">' . $orderDetails->return_d_region . '</td>
</tr>
<tr>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->return_company_name . '</td>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->return_d_company_name . '</td>
</tr>
<tr>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->return_contact_name . '</td>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->return_d_contact_name . '</td>
</tr>
<tr>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->return_phone_num . '</td>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->return_d_phone_num . '</td>
</tr>
<tr>
<th align="left">Address </th>
<td align="left">' . $orderDetails->return_address . '</td>
<th align="left">Address </th>
<td align="left">' . $orderDetails->return_d_address . '</td>
</tr>
<tr>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->return_suburb . '</td>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->return_d_suburb . '</td>
</tr>
<tr>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->return_postal_code . '</td>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->return_d_postal_code . '</td>
</tr>
<tr>
<th align="left">Collection Note </th>
<td align="left">' . $orderDetails->return_collection_note . '</td>
<th align="left">Delivery Note </th>
<td align="left">' . $orderDetails->return_d_note . '</td>
</tr>';
                        }

                        $templateData .='</table>';

                        /*Template Data For Mandrill END*/

                        if ($this->updateOrderPaymentDetails($order_reference_id,'Admin',$PaymentRefId,2,$orderDetails->total_amnt)) {
                            $checkOrderExistArr = $this->getOrderByTransactionId($PaymentRefId);
                            if (count($checkOrderExistArr) > 0) {
                                $checkOrderExist = $checkOrderExistArr[0];

                                $MandrillOrderData = '<table cellspacing="1" cellpadding="4" border="1">
<tr>
<th align="left">Order Reference ID</th>
<td align="left">' . $checkOrderExist->order_reference_id . '</td>
</tr>
<tr>
<th align="left">Buyer Email ID</th>
<td align="left">' . $checkOrderExist->user_email . '</td>
</tr>
<tr>
<th align="left">' . ($checkOrderExist->payment_option == 3? 'Payer ID':($checkOrderExist->payment_option == 1 ? 'Merchant ID' : ($checkOrderExist->payment_option == 2 ? 'Vendor ID' : ''))) . '</th>
<td align="left">' . ($checkOrderExist->payment_option == 3?'Admin':$checkOrderExist->merchant_email) . '</td>
</tr>
<tr>
<th align="left">Amount Paid</th>
<td align="left">$' . number_format($checkOrderExist->paid_amnt, '2', '.', ',') . '</td>
</tr>
<tr>
<th align="left">Payment Gateway</th>
<td align="left">' . ($checkOrderExist->payment_option == 1 ? 'Pay Dollar' : ($checkOrderExist->payment_option == 2 ? 'NAB Transact' : 'Stripe')) . '</td>
</tr>
<tr>
<th align="left">Status</th>
<td align="left">' . $checkOrderExist->payment_success_response . '</td>
</tr>
<tr>
<th align="left">Date/Time</th>
<td align="left">' . date('jS M Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn)) . '</td>
</tr>
</table><br />';

                                $MandrillOrderData .= $templateData;

                                $mandrillDataArr = array();

                                $mandrillDataArr['username'] = $orderDetails->user_name;
                                $mandrillDataArr['useremail'] = $checkOrderExist->user_email;
                                $mandrillDataArr['orderDetail'] = $MandrillOrderData;
                                $mandrillDataArr['templateName'] = '1st email Customer Purchase  CC';
                                $mandrillDataArr['htmlmessage'] = 'Thank you for your booking.';
                                $mandrillDataArr['subject'] = 'The Sweet Spot Club Courier - Order Details' . date('d-m-Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn));
//                            $this->hire->sendMandrilMail($mandrillDataArr);
                                $data = $mandrillDataArr;
                                try {
                                    $template_content[] = array(
                                        'name' => 'FNAME',
                                        'content' => $data['username']
                                    );
                                    $template_content[] = array(
                                        'name' => 'ORDER_DETAILS',
                                        'content' => $data['orderDetail']
                                    );
                                    $template_content[] = array(
                                        'name' => 'LIST:COMPANY',
                                        'content' => 'The Sweet Spot Club Courier'
                                    );
                                    $template_content[] = array(
                                        'name' => 'HTML:LIST_ADDRESS_HTML',
                                        'content' => $supportEmail
                                    );
                                    $to_addresses = array();
                                    $to_addresses[0]['name'] = $data['username'];
                                    $to_addresses[0]['email'] = $data['useremail'];
                                    $to_addresses[0]['type'] = 'to';
                                    $message = array(
                                        'subject' => 'The Sweet Spot Club Courier - Your order details.',
                                        'html' => $data['htmlmessage'],
                                        'from_email' => $supportEmail,
                                        'to' => $to_addresses,
                                        'headers' => array('Reply-To' => $supportEmail),
                                        'track_opens' => true,
                                        'track_clicks' => true,
                                        'auto_text' => true,
                                        'url_strip_qs' => true,
                                        'preserve_recipients' => true,
                                        'merge' => true,
                                        'merge_language' => 'mailchimp',
                                        'global_merge_vars' => $template_content,
                                    );
                                    $result = $mandrill->messages()->sendTemplate($data['templateName'], $template_content, $message);
                                    if ($result[0]['status'] != 'sent') {
                                        file_put_contents('../mandrill-fail.txt', "Following email has been " . $result[0]['status'] . "-\nSubject: " . $message['subject'] . "\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: " . $message['from_email'] . "\nErorr: " . $result[0]['reject_reason'] . "\nTime: " . date("m/d/Y h:i:s A") . "\n\n");

                                    } else {
                                        file_put_contents('../mandrill-success.txt', "Following email has been sent successfully-\nSubject: " . $message['subject'] . "\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: " . $message['from_email'] . "\nTime: " . date("m/d/Y h:i:s A") . "\n\n");
                                    }
                                } catch (Mandrill_Error $e) {

                                    file_put_contents('../mandrill-fail.txt', date("m/d/Y h:i:s A") . "Error: " . $e->getMessage() . "\n\n");

                                }
                            }
                            if (isset($request->adminFlag) && $request->adminFlag == '1') {
                                return redirect()->to('/club_courier_disputed_orders')
                                    ->with('success', 'Order created successfully');
                            } else {
                                $error = '';
                                $errorFlag = 0;
                                return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
                            }
                        } else {
                            $response['status'] = "ERROR";
                            $response['errors'] = "Something goes wrong. Failed to create your order. Please try after sometime.";
                            $error = $response['errors'];
                            $errorFlag = 1;
                            return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
                        }
                    }else{
                        $error = 'Order does not exist.';
                        $errorFlag = 1;
                        return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
                    }
                }

            }
        }

    }

    public function thankyouCC(Mail $mandrill, Request $request)
    {
        Session::forget('orderId');
        View::share('title', 'Thank You!');
        $FirstTime = '0';
        $discountErr = '0';
        $payerid = '';
        $supportEmail = Config::get('constants.CCcustomerSupportEmail');
        $checkOrderExist = array();
        $error = '';
        $errorFlag = 0;
        $order_reference_id = (isset($request->order_reference_id) ? $request->order_reference_id:0);
        /*Stripe Payment*/
        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Get the credit card details submitted by the form
        $input = Input::all();
        $token = Input::get('stripeToken');
        // Create the charge on Stripe's servers - this will charge the user's card
        if(!empty($token)){
            try {
                $this->updatePaymentOptByParameter($order_reference_id,3);
                $charge = Charge::create(array(
                        "amount" => $input['amount'],
                        "currency" => "aud",
                        "card"  => $token,
                        "description" => $input['description'])
                );
                $transactionId     = $charge->balance_transaction;
                $payerid       = $charge->source->id;
                $status        = $charge->status;
                $log  = "--Club Courier Log---".PHP_EOL.
                        "Token: ".$token.PHP_EOL.
                    "transactionId: ".$transactionId.PHP_EOL.
                    "payerid: ".$payerid.PHP_EOL.
                    "status: ".$status.PHP_EOL.
                    "order reference id: ".$order_reference_id.PHP_EOL.
                    "time: ".time().PHP_EOL.
                    "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('../couriers/stripe_log_post.txt', $log, FILE_APPEND);

                if(!empty($transactionId) && $status == 'succeeded'){
                    View::share('PageDescription1', 'Thank you for booking your club from the Sweet Spot Club Courier.');
                    View::share('PageDescription2', 'We will aim to provide the best quality service for you and will be there for whatever you need along the way. Feel free to contact us via email at info@tssclubhire or via our social media pages @tssclubhire if you have any questions or concerns. We will be sending you information in regards to your hire closer to your initial hire date, with all the details you will require. Thank you again for choosing The Sweet Spot and we hope your next golf experience is a great one.');
                    $FirstTime = '1';
                    $xero = App::make('XeroPrivate');
                    $contact = App::make('XeroContact');
                    $invoice = App::make('XeroInvoice');
                    $orderDetails = array();
                    $orderDetArr = DB::table($this->DBTables['CCOrders'])
                        ->where('order_reference_id', '=', $order_reference_id)
                        ->get();
                    /*if(count($orderDetArr)>0) {
                        $orderDetails = $orderDetArr[0];
                    }
                    $orderDetailsAry = $this->hire->getPreOrderDetails($order_reference_id);*/

                    if (count($orderDetArr) > 0) {
                        $orderDetails = $orderDetArr[0];
                        $contact->setContactStatus('ACTIVE');
                        $contact->setName($orderDetails->user_name);
                        /*$contact->setFirstName($orderDetails->buyer_first_name);
                        $contact->setLastName($orderDetails->buyer_last_name);*/
                        $contact->setEmailAddress($orderDetails->user_email);
                        $contact->setDefaultCurrency('AUD');
                        $invoice->setContact($contact);
                        $invoice->setType('ACCREC');
                        $invoice->setDate(Carbon::now());
                        $invoice->setDueDate(Carbon::now()->addDays(4));
                        $invoice->setLineAmountType('Exclusive');
                        $invoice->setStatus('AUTHORISED');
                        $bagArr = DB::table($this->DBTables['CCOrders_Products'])
                            ->where('order_id', '=', $orderDetails->id)
                            ->get();
                        $i = 0;
                        $line = array();
                        if (count($bagArr) > 0) {
                            $discountFlag = true;
                            $disCount = 0;
                            foreach ($bagArr as $bag){
                                $line[$i] = App::make('XeroInvoiceLine');
                                    $line[$i]->setDescription(strip_tags($bag->bag_title.' - '.$bag->product_name));
                                    $line[$i]->setQuantity(1);
                                    $line[$i]->setUnitAmount($bag->sub_total_amnt_out+$bag->sub_total_amt_ret);
                                    $line[$i]->setAccountCode(230);
                                    $line[$i]->setTaxType('NONE');
                                    $invoice->addLineItem($line[$i]);
                                    $i++;
                            }
                            if (!empty($orderDetails->offer_Code)) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Offer Code Applied - "' . $orderDetails->offer_Code . '"');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount('-' . $orderDetails->offer_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if($orderDetails->multiset_discount >0){
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Multiset Discount');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount('-' . $orderDetails->multiset_discount);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if($orderDetails->outgoing_shipment == 2){
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Express courier - Outgoing');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount(20);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if($orderDetails->return_shipment == 2){
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Express courier - Return');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount(20);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                        }
                        $line[$i] = App::make('XeroInvoiceLine');
                        $xero->save($invoice);
                        $invoicesArr = XeroPrivate::load('Accounting\\Invoice')->execute();
                        $totalInvoices = count($invoicesArr);
                        $line[$i]->setAccountCode('SALES');
                        $this->updateInvoiceNo($invoicesArr[$totalInvoices - 1]->InvoiceNumber, $order_reference_id, $transactionId);
                        /*Template Data For Mandrill START*/

                        $templateData = '<table cellspacing="1" cellpadding="4" border="1">
				<thead>
				<tr>
					<th align="left">YOUR ORDER : ' .$order_reference_id . '</th>
					<th align="left">COST</th>
				</tr>
				</thead>
				<tbody>';
                        $total = 0;
                        if (count($bagArr) > 0) {
                            foreach ($bagArr as $bag){
                                $templateData .= '<tr>
					<td align="left"><b>' . $bag->bag_title.'</b> - '.$bag->product_name . '</td>
					<td align="left">$' . number_format($bag->sub_total_amnt_out+$bag->sub_total_amt_ret, 2, '.', ',') . '</td>
				</tr>';

                            }
                            if($orderDetails->outgoing_shipment == 2){
                                $templateData .='<tr><td align="left">Express Courier Charge - Outgoing</td>
<td align="left">$20</td></tr>';
                            }
                            if($orderDetails->return_shipment == 2){
                                $templateData .='<tr><td align="left">Express Courier Charge - Return</td>
<td align="left">$20</td></tr>';
                            }
                        }
                        $templateData .= '</tbody>
			</table><br />
			<table cellspacing="1" cellpadding="1" border="1">
				<tbody>
				<tr>
					<td align="left"><strong>SUB TOTAL</strong></td>
					<td align="left">$' . number_format($orderDetails->sub_total_amnt, 2, '.', ',') . '</td>
				</tr>
                <tr>
                    <td align="left"><strong>VOUCHER CODE</strong></td>
                    <td align="left">' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_Code : 'N/A') . '</td>
                </tr>
                <tr>
                    <td align="left"><strong>VOUCHER DISCOUNT</strong></td>
                    <td align="left"><b>- </b>$' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_amnt : '0.00') . '</td>
                </tr>
                <tr>
                    <td align="left"><strong>MULTISET DISCOUNT</strong></td>
                    <td align="left"><b>- </b>$' . $orderDetails->multiset_discount . '</td>
                </tr>
				<tr>
					<td align="left"><strong>TOTAL</strong></td>
					<td align="left">$' . number_format($orderDetails->total_amnt, 2, '.', ',') . '</td>
				</tr>
				</tbody>
			</table><br />
			<h2>Your Details</h2>
			<br />
			<table cellspacing="1" cellpadding="4" border="1">
			<tr>
			<th align="left">Name</th>
			<td align="left">' . $orderDetails->user_name . '</td>
</tr>
<tr>
			<th align="left">Email</th>
			<td align="left"><a href="mailto:' . $orderDetails->user_email . '">' . $orderDetails->user_email . '</a></td>
</tr>
<tr>
			<th align="left">Contact Number</th>
			<td align="left"><a href="tel:' . $orderDetails->user_phone . '">' . $orderDetails->user_phone . '</a></td>
</tr>
</table><br />
<table cellpadding="1" cellspacing="4" border="1">
<tr>
<th colspan="2" align="left">Delivery Details (Pick up) '.date('jS M Y',strtotime($orderDetails->pickup_date)).'</th>
<th colspan="2" align="left">Delivery Details (Drop off)</th>
</tr>
<tr>
<th align="left">Region of pickup</th>
<td align="left">' . $orderDetails->pickup_region . '</td>
<th align="left">Region of drop off</th>
<td align="left">' . $orderDetails->destination_region . '</td>
</tr>
<tr>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->pickup_company_name . '</td>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->destination_company_name . '</td>
</tr>
<tr>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->pickup_contact_name . '</td>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->destination_contact_name . '</td>
</tr>
<tr>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->pickup_phone_num . '</td>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->destination_phone_num . '</td>
</tr>
<tr>
<th align="left">Address </th>
<td align="left">' . $orderDetails->pickup_address . '</td>
<th align="left">Address </th>
<td align="left">' . $orderDetails->destination_address . '</td>
</tr>
<tr>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->pickup_suburb . '</td>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->destination_suburb . '</td>
</tr>
<tr>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->pickup_postal_code . '</td>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->destination_postal_code . '</td>
</tr>
<tr>
<th align="left">Collection Note </th>
<td align="left">' . $orderDetails->pickup_delivery_note . '</td>
<th align="left">Delivery Note </th>
<td align="left">' . $orderDetails->destination_note . '</td>
</tr>
<tr>
<th colspan="2" align="left">Shipping Option</th>
<th colspan="2" align="left">'.(!empty($orderDetails->return_region)?'Return':'One way').'</th>
</tr>';
                        if(!empty($orderDetails->return_region)){
                            $templateData .='<tr>
<th colspan="2" align="left">Return Delivery Details (Pick up) '.date('jS M Y',strtotime($orderDetails->return_date)).'</th>
<th colspan="2" align="left">Return Delivery Details (Drop off)</th>
</tr>
<tr>
<th align="left">Region of pickup</th>
<td align="left">' . $orderDetails->return_region . '</td>
<th align="left">Region of drop off</th>
<td align="left">' . $orderDetails->return_d_region . '</td>
</tr>
<tr>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->return_company_name . '</td>
<th align="left">Company Name</th>
<td align="left">' . $orderDetails->return_d_company_name . '</td>
</tr>
<tr>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->return_contact_name . '</td>
<th align="left">Contact Name</th>
<td align="left">' . $orderDetails->return_d_contact_name . '</td>
</tr>
<tr>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->return_phone_num . '</td>
<th align="left">Contact Phone No.</th>
<td align="left">' . $orderDetails->return_d_phone_num . '</td>
</tr>
<tr>
<th align="left">Address </th>
<td align="left">' . $orderDetails->return_address . '</td>
<th align="left">Address </th>
<td align="left">' . $orderDetails->return_d_address . '</td>
</tr>
<tr>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->return_suburb . '</td>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->return_d_suburb . '</td>
</tr>
<tr>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->return_postal_code . '</td>
<th align="left">Postcode </th>
<td align="left">' . $orderDetails->return_d_postal_code . '</td>
</tr>
<tr>
<th align="left">Collection Note </th>
<td align="left">' . $orderDetails->return_collection_note . '</td>
<th align="left">Delivery Note </th>
<td align="left">' . $orderDetails->return_d_note . '</td>
</tr>';
                        }

                        $templateData .='</table>';

                        /*Template Data For Mandrill END*/

                        if ($this->updateOrderPaymentDetails($order_reference_id,$payerid,$transactionId,2,$orderDetails->total_amnt)) {
                            session()->put('transactionId', $transactionId);
                            $checkOrderExistArr = $this->getOrderByTransactionId($transactionId);
                            if (count($checkOrderExistArr) > 0) {
                                $checkOrderExist = $checkOrderExistArr[0];

                                $MandrillOrderData = '<table cellspacing="1" cellpadding="4" border="1">
<tr>
<th align="left">Order Reference ID</th>
<td align="left">' . $checkOrderExist->order_reference_id . '</td>
</tr>
<tr>
<th align="left">Buyer Email ID</th>
<td align="left">' . $checkOrderExist->user_email . '</td>
</tr>
<tr>
<th align="left">' . ($checkOrderExist->payment_option == 3? 'Payer ID':($checkOrderExist->payment_option == 1 ? 'Merchant ID' : ($checkOrderExist->payment_option == 2 ? 'Vendor ID' : ''))) . '</th>
<td align="left">' . ($checkOrderExist->payment_option == 3?$payerid:$checkOrderExist->merchant_email) . '</td>
</tr>
<tr>
<th align="left">Amount Paid</th>
<td align="left">$' . number_format($checkOrderExist->paid_amnt, '2', '.', ',') . '</td>
</tr>
<tr>
<th align="left">Payment Gateway</th>
<td align="left">' . ($checkOrderExist->payment_option == 1 ? 'Pay Dollar' : ($checkOrderExist->payment_option == 2 ? 'NAB Transact' : 'Stripe')) . '</td>
</tr>
<tr>
<th align="left">Status</th>
<td align="left">' . $checkOrderExist->payment_success_response . '</td>
</tr>
<tr>
<th align="left">Date/Time</th>
<td align="left">' . date('jS M Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn)) . '</td>
</tr>
</table><br />';

                                $MandrillOrderData .= $templateData;

                                $mandrillDataArr = array();

                                $mandrillDataArr['username'] = $orderDetails->user_name;
                                $mandrillDataArr['useremail'] = $checkOrderExist->user_email;
                                $mandrillDataArr['orderDetail'] = $MandrillOrderData;
                                $mandrillDataArr['templateName'] = '1st email Customer Purchase  CC';
                                $mandrillDataArr['htmlmessage'] = 'Thank you for your booking.';
                                $mandrillDataArr['subject'] = 'The Sweet Spot Club Courier - Order Details' . date('d-m-Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn));
//                            $this->hire->sendMandrilMail($mandrillDataArr);
                                $data = $mandrillDataArr;
                                try {
                                    $template_content[] = array(
                                        'name' => 'FNAME',
                                        'content' => $data['username']
                                    );
                                    $template_content[] = array(
                                        'name' => 'ORDER_DETAILS',
                                        'content' => $data['orderDetail']
                                    );
                                    $template_content[] = array(
                                        'name' => 'LIST:COMPANY',
                                        'content' => 'The Sweet Spot Club Courier'
                                    );
                                    $template_content[] = array(
                                        'name' => 'HTML:LIST_ADDRESS_HTML',
                                        'content' => $supportEmail
                                    );
                                    $to_addresses = array();
                                    $to_addresses[0]['name'] = $data['username'];
                                    $to_addresses[0]['email'] = $data['useremail'];
                                    $to_addresses[0]['type'] = 'to';
                                    $message = array(
                                        'subject' => 'The Sweet Spot Club Courier - Your order details.',
                                        'html' => $data['htmlmessage'],
                                        'from_email' => $supportEmail,
                                        'to' => $to_addresses,
                                        'headers' => array('Reply-To' => $supportEmail),
                                        'track_opens' => true,
                                        'track_clicks' => true,
                                        'auto_text' => true,
                                        'url_strip_qs' => true,
                                        'preserve_recipients' => true,
                                        'merge' => true,
                                        'merge_language' => 'mailchimp',
                                        'global_merge_vars' => $template_content,
                                    );
                                    $result = $mandrill->messages()->sendTemplate($data['templateName'], $template_content, $message);
                                    if ($result[0]['status'] != 'sent') {
                                        file_put_contents('../mandrill-fail.txt', "Following email has been " . $result[0]['status'] . "-\nSubject: " . $message['subject'] . "\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: " . $message['from_email'] . "\nErorr: " . $result[0]['reject_reason'] . "\nTime: " . date("m/d/Y h:i:s A") . "\n\n");

                                    } else {
                                        file_put_contents('../mandrill-success.txt', "Following email has been sent successfully-\nSubject: " . $message['subject'] . "\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: " . $message['from_email'] . "\nTime: " . date("m/d/Y h:i:s A") . "\n\n");
                                    }
                                } catch (Mandrill_Error $e) {

                                    file_put_contents('../mandrill-fail.txt', date("m/d/Y h:i:s A") . "Error: " . $e->getMessage() . "\n\n");

                                }
                            }
                            if (isset($request->adminFlag) && $request->adminFlag == '1') {
                                return redirect()->to('/disputed_orders')
                                    ->with('success', 'Order created successfully');
                            } else {
                                $error = '';
                                $errorFlag = 0;
                                header('Refresh: 8;url='.url('/clubcourier/booking'));
                                return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
                            }
                        } else {
                            $response['status'] = "ERROR";
                            $response['errors'] = "Something goes wrong. Failed to create your order. Please try after sometime.";
                            $error = $response['errors'];
                            $errorFlag = 1;
                            header('Refresh: 8;url='.url('/clubcourier/booking'));
                            return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
                        }
                    }else{
                        $error = 'Order does not exist.';
                        $errorFlag = 1;
                        header('Refresh: 8;url='.url('/clubcourier/booking'));
                        return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
                    }
                }else{
                    $error = 'Payment declined. Please try again later.';
                    $errorFlag = 1;
                    header('Refresh: 8;url='.url('/clubcourier/booking'));
                    return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
                }
                // if status="succeeded" do rest of the insert operation start
                // end
            } catch(Card $e) {
                $e_json = $e->getJsonBody();
                $error = $e_json['error'];
                // The card has been declined
                // redirect back to checkout page

                $errorFlag = 1;
                header('Refresh: 8;url='.url('/clubcourier/booking'));
                return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime', 'error','errorFlag'));
            }
        }else{
            $log  = "Token: No token generated.".PHP_EOL.
                "Date Time: ".(date('d-m-Y h:i:s')).PHP_EOL.
                "-------------------------".PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents('../stripe_log_post.txt', $log, FILE_APPEND);

            $error = 'Access denied. Invalid access to the page.';
            $errorFlag = 2;
            header('Refresh: 8;url='.url('/clubcourier/booking'));
            return view('pages.clubcourier.thankyou', compact('checkOrderExist', 'FirstTime','error','errorFlag'));

        }
        /*Payment End*/

    }
}