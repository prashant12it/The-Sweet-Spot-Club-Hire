<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App;
use App\Product;
use App\Orders;
use App\Http\Controllers\CustomerOrderController;
use DB;
use Config;
use View;
use Session;
use Cookie;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Weblee\Mandrill\Mail;
use Excel;

class HireController extends Controller
{
    public function __construct()
    {
        $this->pcrud = new ProductCRUDController;
        $this->AdminOrder = new AdminOrdersController;
        $this->DBTables = Config::get('constants.DbTables');
        DB::enableQueryLog();
    }

    public function index(Request $request)
    {
        ob_end_clean();
        ob_start();
        session_start();
        if(isset($request->lang) && !empty($request->lang)){
            session()->put('lang', $request->lang);
        }
        $this->setLang();
        View::share('filter', 'sets');
        View::share('title', 'Club Sets');
        View::share('redirectPage', 'insurance');
        View::share('Page', 'clubsearch');
        View::share('PageHeading', 'Golf Club Hire Australia');
        View::share('PageDescription1', 'At The Sweet Spot Club Hire, we offer the latest to market clubs from the leading brands – Callaway and TaylorMade. We have designed our sets to cater for all levels of golfer, whether it be someone playing from scratch or someone just starting out. Hit the Sweet Spot with your next hire!');
        View::share('PageDescription2', '');
        $EstimatedShipping = Config::get('constants.stateEstimatedShipping');
        $giftProdId = array();
        $insurance = 0;
        $setCount = 0;
        $showGift = false;
        $AddedProdsArr = array();
//        if (Auth::guest()) {
            $cartDetailArr = array();
            $insurance = 0;
            $orderrefid = (isset($_COOKIE['order_reference_id']) ? $_COOKIE['order_reference_id'] : null);
            //$defaultFilterArr = array(55,53,28,58,59);
            $defaultFilterArr = (!empty(session()->get('defaultFilterArr')) ? session()->get('defaultFilterArr') : Config::get('constants.Default_Filter'));
            if ($orderrefid !== null) {
                session()->put('page1', '1');
                $PreorderDetArr = $this->getPreOrderDetails($orderrefid);
                if(count($PreorderDetArr)>0){
                    if (!empty($PreorderDetArr) && $PreorderDetArr[0]->shipping_amnt > 0) {
                        $this->setShippingZeroByOrderRefId($orderrefid, $PreorderDetArr[0]->shipping_amnt);
                    }
                    if (count($PreorderDetArr) > 0) {
                        session()->put('fromDate', date('Y-m-d', strtotime($PreorderDetArr[0]->dt_book_from)));
                        session()->put('toDate', date('Y-m-d', strtotime($PreorderDetArr[0]->dt_book_upto)));
                        session()->put('states', $PreorderDetArr[0]->state_id);
                        $insurance = $PreorderDetArr[0]->insurance_amnt;
                    }
                    setcookie('order_reference_id', $orderrefid, time() + (86400 * 10), "/");
                    $cartDetailArr = $this->getCartByRefId($orderrefid, $defaultFilterArr);
//                    dd($cartDetailArr);
                    $cartDetailArr = $this->getCart($cartDetailArr);
                    $setCount = $this->getCartSetCount($orderrefid);
                    $AddedProdsArr = $this->preOrderProdMapDetails($orderrefid);
                }else{
                    $time = -(time() + (86400 * 10));
                    setcookie('order_reference_id', null, -$time, "/");
                    setcookie('TSS_PARTNER', null, -$time, "/");
                }
            }
            $ParentProdsArr = $this->getParentHireProduct();
            if (!empty($request->fromDate)) {
$request->fromDate = $this->formatDates($request->fromDate);

                session()->put('fromDate', $request->fromDate);
                session()->put('showgift', '1');
                session()->forget('page2');
                session()->forget('page3');
                session()->forget('page4');
                session()->put('page1', '1');
            }
            if (!empty($request->toDate)) {
                $request->toDate = $this->formatDates($request->toDate);
                session()->put('toDate', $request->toDate);
            }
            if (!empty($request->states)) {
                session()->put('states', $request->states);
            }
            if(!$orderrefid && empty(session()->get('states')) && empty(session()->get('fromDate')) && empty(session()->get('toDate')) ){
                header('Refresh: 5;url='.url('../'));
                return view('pages.frontend.empty_cart');
            }
            $servicingDays = Config::get('constants.stateServicingDays');
            $extendedDays = $servicingDays[session()->get('states')];
            $AllAvailProdsArr = array();
            $TotalUpsellProdsArr = array();
            $AttributesUnsortedArr = $this->getAttributes();
            $AttributesArr = array();
            $hireDays = $this->getDaysFromDates(session()->get('fromDate'), session()->get('toDate'));
            if ((session()->get('showgift') == '1')) {
                $showGift = true;
                $giftProdId = $this->getGiftProds($hireDays);
//                dd($giftProdId);
                session()->put('showgift', '0');
            }
            foreach ($AttributesUnsortedArr as $key => $Attributes) {
                if ($Attributes->attrib_name == 'Handicap') {
                    $AttributesArr[4] = $Attributes;
                    unset($AttributesUnsortedArr[$key]);
                }
                if ($Attributes->attrib_name == 'Flex') {
                    $AttributesArr[3] = $Attributes;
                    unset($AttributesUnsortedArr[$key]);
                }
                if ($Attributes->attrib_name == 'Shaft Type') {
                    $AttributesArr[2] = $Attributes;
                    unset($AttributesUnsortedArr[$key]);
                }
                if ($Attributes->attrib_name == 'Hand') {
                    $AttributesArr[1] = $Attributes;
                    unset($AttributesUnsortedArr[$key]);
                }
                if ($Attributes->attrib_name == 'Gender') {
                    $AttributesArr[0] = $Attributes;
                    unset($AttributesUnsortedArr[$key]);
                }

            }
            foreach ($AttributesUnsortedArr as $key => $Attributes) {
                array_push($AttributesArr, $Attributes);
                unset($AttributesUnsortedArr[$key]);
            }
            ksort($AttributesArr);
            $AttribOptsArr = $this->getAttributesOptions();
            if (!empty($ParentProdsArr)) {
                foreach ($ParentProdsArr as $Parentprod) {
                    $AvailProdArr = $this->getAvailChildProds($Parentprod->id, session()->get('fromDate'), session()->get('toDate'), $extendedDays, $defaultFilterArr, $AddedProdsArr);

                    if (!empty($AvailProdArr)) {;
                        $ParentProdDetailArr['parent-prod-id'] = $Parentprod->id;
                        $ParentProdDetailArr['parent-prod-name'] = $Parentprod->name;
                        $ParentProdDetailArr['parent-prod-description'] = $Parentprod->description;
                        $ParentProdDetailArr['parent-prod-feat_img'] = $Parentprod->feat_img;
                        $ParentProdDetailArr['parent-prod-sku'] = $Parentprod->sku;
                        $ParentProdDetailArr['parent-prod-quantity'] = count($AvailProdArr);
                        $ParentProdDetailArr['parent-prod-price'] = $Parentprod->price;
                        $ParentProdDetailArr['parent-prod-category'] = $Parentprod->category;
                        $ParentProdDetailArr['parent-prod-product_type'] = $Parentprod->product_type;
                        $ParentProdDetailArr['parent-prod-is_upsell_product'] = $Parentprod->is_upsell_product;
                        $ParentProdDetailArr['parent-prod-sale'] = $Parentprod->sale;
                        $ParentProdDetailArr['parent-prod-sale_price '] = $Parentprod->sale_price;
                        $ParentProdDetailArr['parent-prod-rent'] = $Parentprod->rent;
                        $ParentProdDetailArr['parent-prod-rent_price'] = $Parentprod->rent_price;
                        $ParentProdDetailArr['parent-prod-prod_video'] = $Parentprod->prod_video;
                        $ParentProdDetailArr['parent-prod-attrib-handicap'] = $AvailProdArr[0]->handicap;
                        array_push($AvailProdArr, $ParentProdDetailArr);
                        array_push($AllAvailProdsArr, $AvailProdArr);
                    }

                }
            }
//            dd($AllAvailProdsArr);
            return view('pages.frontend.hire', compact('AllAvailProdsArr', 'TotalUpsellProdsArr', 'AttributesArr', 'AttribOptsArr', 'defaultFilterArr', 'cartDetailArr', 'hireDays', 'insurance', 'showGift', 'giftProdId','setCount','EstimatedShipping'));

    }
    public function setLang(){
        $setLang = session()->get('lang');
        if(isset($setLang)){
            App::setLocale($setLang);
        }else{
            App::setLocale('en');
        }
    }
    public function preOrderProdMapDetails($orderRefId){
        $ProdsArr = DB::table($this->DBTables['Pre_Orders_Products'])->where('order_reference_id', '=', $orderRefId)->get();

        return $ProdsArr;
    }

    public function getGiftProds($hireDays)
    {
        $giftProdId = array();
        $GiftsOpts = Config::get('constants.Gift');
        array_push($giftProdId, $GiftsOpts['2']);
        if($hireDays > '3'){
            array_push($giftProdId, $GiftsOpts['3']);
        }
        if ($hireDays >= '7') {
            array_push($giftProdId, $GiftsOpts['7']);
        }
        return $giftProdId;
    }

    public function moveElement(&$array, $a, $b)
    {
        $out = array_splice($array, $a, 1);
        array_splice($array, $b, 0, $out);
    }

    public function getCartByRefId($orderRefId, $defaultFilterArr = array(),$extDays = 0)
    {
        $cartProdArr = DB::table($this->DBTables['Pre_Orders_Products'])
            ->join($this->DBTables['Products'], $this->DBTables['Products'] . '.id', '=', $this->DBTables['Pre_Orders_Products'] . '.product_id')
            ->where([[$this->DBTables['Products'] . '.disable', '=', 0],[$this->DBTables['Pre_Orders_Products'] . '.order_reference_id', '=', $orderRefId], [$this->DBTables['Pre_Orders_Products'] . '.sub_total_amnt', '>', 0]])
            ->get();
        $FreeGiftProdArr = DB::table($this->DBTables['Pre_Orders_Products'])
            ->join($this->DBTables['Products'], $this->DBTables['Products'] . '.id', '=', $this->DBTables['Pre_Orders_Products'] . '.product_id')
            ->where([[$this->DBTables['Products'] . '.disable', '=', 0],[$this->DBTables['Pre_Orders_Products'] . '.order_reference_id', '=', $orderRefId], [$this->DBTables['Pre_Orders_Products'] . '.sub_total_amnt', '=', 0]])
            ->get();
        if (count($FreeGiftProdArr) > 0) {
            $cartProdArr = $cartProdArr->merge($FreeGiftProdArr);
        }
        $parentProdSetArr = array();
        if (!empty($cartProdArr)) {
            $PreorderDetArr = $this->getPreOrderDetails($orderRefId);
            if (count($PreorderDetArr) > 0) {
                $hireDays = $PreorderDetArr[0]->hire_days;
                $SubTotalPrice = 0;
                $setCount = 0;

                foreach ($cartProdArr as $cartProds) {
                    $preOrderProdDetArr = DB::table($this->DBTables['Pre_Orders_Products'] . ' as pop')
                        ->join($this->DBTables['Products'] . ' as p', 'pop.product_id', '=', 'p.id')
                        ->where([
                            ['p.disable','=',0],
                            ['pop.order_reference_id', '=', $orderRefId],
                            ['pop.dtCreatedOn', '=', $cartProds->dtCreatedOn],
                            ['pop.product_id', '=', $cartProds->product_id]
                        ])
                        ->select('p.*', 'pop.pre_order_id', 'pop.quantity as ord_qty', 'pop.sub_total_amnt')
                        ->get();
                    $prodSet = array();
                    $servicingDays = Config::get('constants.stateServicingDays');
                    $state = (session()->get('states') && session()->get('states')>0?session()->get('states'):$extDays);
                    $extendedDays = $servicingDays[$state];
                    if (!empty($preOrderProdDetArr)) {
                        $i = 0;
                        $prodsArr = array();
                        $parentProdRec = array();
                        foreach ($preOrderProdDetArr as $preOrderProdDet) {

                            $availProdArr = array();
                            if ($i == 0 && $preOrderProdDet->product_type == 5) {
                                $parentProdArr = $this->getParentProductByChildId($preOrderProdDet->id);
                                $ArrtibSetArr = $this->getProdAttribsByProdId($preOrderProdDet->id);
                                $prodQuantity = count($preOrderProdDetArr);
                                $prodSet['prod-id'] = $parentProdArr[0]->id;
                                $childProdArr = $this->getAvailChildProds($parentProdArr[0]->id, session()->get('fromDate'), session()->get('toDate'), $extendedDays, $defaultFilterArr);

                                if (!empty($childProdArr)) {
                                    foreach ($childProdArr as $key => $childProd) {

                                        if ($childProd->id != $preOrderProdDet->id) {
                                            array_push($availProdArr, $childProdArr[$key]);
                                        }
                                    }
                                }
                                $prodSet['childProdArr'] = (!empty($availProdArr) ? $availProdArr : $availProdArr);
                                $prodSet['prod-name'] = $parentProdArr[0]->name;
                                $prodSet['parent-prod-id'] = $parentProdArr[0]->id;
                                $prodSet['prod-description'] = $parentProdArr[0]->description;
                                $prodSet['parent-prod-feat_img'] = $parentProdArr[0]->feat_img;
                                $prodSet['product_type'] = $preOrderProdDet->product_type;
                                $prodSet['attributes'] = $ArrtibSetArr;
                                $prodSet['allAttribSet'] = array();
                                $prodSet['quantity'] = $prodQuantity;
                                $prodSet['price'] = $this->getProductPriceByHireDays($hireDays);
                                $setCount = $setCount + $prodQuantity;
                                $SubTotalPrice = $SubTotalPrice + ($prodSet['price'] * $prodSet['quantity']);
                            } elseif ($preOrderProdDet->product_type != 5) {
                                $ArrtibSetArr = $this->getProdAttribsByProdId($preOrderProdDet->id);
                                $prodSet['prod-id'] = $preOrderProdDet->id;
                                $prodSet['childProdArr'] = array();
                                $prodSet['prod-name'] = $preOrderProdDet->name;
                                $prodSet['product_type'] = $preOrderProdDet->product_type;
                                $prodSet['parent-prod-id'] = 0;
                                $prodSet['prod-description'] = $preOrderProdDet->description;
                                $prodSet['parent-prod-feat_img'] = $preOrderProdDet->feat_img;
                                $prodSet['attributes'] = $ArrtibSetArr;
                                $prodSet['allAttribSet'] = array();
                                $prodSet['quantity'] = $preOrderProdDet->ord_qty;
                                $prodSet['price'] = $preOrderProdDet->sub_total_amnt;
                                $SubTotalPrice = $SubTotalPrice + $prodSet['price'];
                            }
                            $prodSet['setcount'] = $setCount;
                            array_push($prodsArr, $preOrderProdDet->id);
                            $i++;
                        }
                        $prodSet['prodidArr'] = $prodsArr;
                        $prodSet['subtotal'] = $SubTotalPrice;
                        $prodSet['cartTotal'] = $PreorderDetArr[0]->total_amnt;
                        $prodSet['partnerDiscount'] = $PreorderDetArr[0]->partner_discount_amnt;
                        $prodSet['Discount'] = $SubTotalPrice - $this->getMultiSetDiscountedPrice($setCount, $SubTotalPrice);
                        $prodSet['shipping'] = $PreorderDetArr[0]->shipping_amnt;
                        $prodSet['insurance'] = $PreorderDetArr[0]->insurance_amnt;
                        $prodSet['tss'] = $PreorderDetArr[0]->tss;
                        array_push($parentProdSetArr, $prodSet);
                    }
                }
            }

        }
        return $parentProdSetArr;
    }

    public function getPreOrderDetails($orderRefId)
    {
        $PreOrderDetailArr = DB::table($this->DBTables['Pre_Orders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->get();

        return $PreOrderDetailArr;
    }
    public function getAjaxPreorderDetails(Request $request){
        $preorderResult = $this->getPreOrderDetails($request->order_reference_id);
        if(count($preorderResult)>0){
            $responsedata = array( "code" => 200, "oldfromdate" => date('m/d/y',strtotime($preorderResult[0]->dt_book_from)),
                "oldtodate" => date('m/d/y',strtotime($preorderResult[0]->dt_book_upto)),
                "orderrefid" => $preorderResult[0]->order_reference_id,
                "oldstate" => $preorderResult[0]->state_id);
        }
        header( 'Content-Type: application/json' );
        echo json_encode( $responsedata );
    }

    public function getParentProductByChildId($ChildProdId)
    {
        $ParentProdArr = DB::table($this->DBTables['Group_Products'] . ' as GP')
            ->join($this->DBTables['Products'] . ' as P', 'GP.parent_productid', '=', 'P.id')
            ->where('P.disable', '=', 0)
            ->where('GP.product_id', '=', (int)$ChildProdId)
            ->select('P.*')
            ->get();

        return $ParentProdArr;
    }

    public function getProdAttribsByProdId($prodid)
    {
        $AttribValArr = DB::table($this->DBTables['Products_Attribute_Mapping'] . ' as pam')
            ->join($this->DBTables['Attributes_Values'] . ' as av', 'pam.attrib_val_id', '=', 'av.id')
            ->join($this->DBTables['Attributes'] . ' as atrb', 'av.attrib_id', '=', 'atrb.id')
            ->where('pam.prod_id', '=', (int)$prodid)
            ->select('atrb.attrib_name', 'av.value', 'pam.attrib_val_id')
            ->get();


        return $AttribValArr;
    }

    public function getProdQtyByAttribCount($prodidArr){
        $attribSetsArr = array();
        $prodCount = count($prodidArr);
        if($prodCount>0){
            $i = 0;
            foreach ($prodidArr as $key => $ProdVal){
                $ProdAttribSet = $this->getProdAttribsByProdId($ProdVal);
                $AttribSet = array();
                if(count($ProdAttribSet)>0){
                    foreach ($ProdAttribSet as $attribVal){
                        array_push($AttribSet,$attribVal);
                    }
                }
                if($i>0){
                    for($j=0;$j<=$i-1;$j++){

                        if(!empty($attribSetsArr)){

                            foreach ($attribSetsArr as $attribSetsArrKey => $attribSetsVal){
//                                dd($attribSetsVal['sets']);
                                $GVals[0] = $attribSetsVal['sets'][0]->attrib_val_id;
                                $GVals[1] = $attribSetsVal['sets'][1]->attrib_val_id;
                                $GVals[2] = $attribSetsVal['sets'][2]->attrib_val_id;
                                $GVals[3] = $attribSetsVal['sets'][3]->attrib_val_id;
                                $GVals[4] = $attribSetsVal['sets'][4]->attrib_val_id;
//                                dd($GVals);
                                $LVals[0] = $AttribSet[0]->attrib_val_id;
                                $LVals[1] = $AttribSet[1]->attrib_val_id;
                                $LVals[2] = $AttribSet[2]->attrib_val_id;
                                $LVals[3] = $AttribSet[3]->attrib_val_id;
                                $LVals[4] = $AttribSet[4]->attrib_val_id;
                                if(!empty(array_diff($GVals,$LVals))){
                                    $attribSetsArr[$ProdVal]['sets'] = $AttribSet;
                                    $attribSetsArr[$ProdVal]['qty'] = 1;
                                }else{
                                    $attribSetsArr[$attribSetsArrKey]['qty'] = $attribSetsArr[$attribSetsArrKey]['qty'] + 1;
                                }
                            }

                        }else{
                            array_push($attribSetsArr[$ProdVal],$ProdAttribSet);
                            $attribSetsArr[$ProdVal]['qty'] = 1;
                        }
                    }
                }else{
                    $attribSetsArr[$ProdVal]['sets'] = $AttribSet;
                    $attribSetsArr[$ProdVal]['qty'] = 1;
                }

                $i++;
            }
        }
        return $attribSetsArr;

    }

    public function getCartSetCount($orderRefId)
    {
        $SetProds = DB::table($this->DBTables['Pre_Orders_Products'] . ' as po')
            ->join($this->DBTables['Products'] . ' as p', 'po.product_id', '=', 'p.id')
            ->where([['p.disable','=',0],
                ['po.order_reference_id', '=', $orderRefId],
                ['p.product_type','=','5']])
            ->count();


        return $SetProds;
    }

    public function getProductPriceByHireDays($hireDays)
    {
        $PricingList = Config::get('constants.HireDaysPricing');
        $AdditionalPrice = Config::get('constants.AdditionalPrice');
        $stateid = session()->get('states');

        $prodPrice = 0;
        if(($stateid == '6' || $stateid == '7') && $hireDays == 1){
            $prodPrice = 70;
        }elseif ($hireDays > 0 && $hireDays < 8) {
            $prodPrice = $PricingList[$hireDays];
        } elseif ($hireDays > 7) {
            $prodPrice = $PricingList['7'] + (($hireDays - 7) * $AdditionalPrice);
        }

        return $prodPrice;
    }

    public function getMultiSetDiscountedPrice($setsCount, $totalPrice)
    {
        $DiscountList = Config::get('constants.Discount');
        $DiscountedPrice = $totalPrice;
        if ($setsCount > 1 && $setsCount < 11) {
            $discountPrice = $totalPrice * $DiscountList[$setsCount] * 0.01;
            $DiscountedPrice = $totalPrice - $discountPrice;
        } elseif ($setsCount > 10) {
            $discountPrice = $totalPrice * $DiscountList['11'] * 0.01;
            $DiscountedPrice = $totalPrice - $discountPrice;
        }

        return $DiscountedPrice;
    }

    public function getParentHireProduct($id = 0, $type = 4)
    {
        $ProductsArr = Product::where([
            ['disable', '=', 0],
            ['id', ($id > 0 ? '=' : '!='), (int)$id],
            ['product_type', '=', (int)$type]
        ])->get();

        return $ProductsArr;
    }

    public function getListOfProdsByIds($ids)
    {
        $ProductsArr = Product::whereIn('id', $ids)->get();

        return $ProductsArr;
    }

    public function getAttributes()
    {
        $AtteibutesArr = DB::table($this->DBTables['Attributes'])->get();

        return $AtteibutesArr;
    }

    public function getAttributesOptions()
    {
        $attribOptsArr = DB::table($this->DBTables['Attributes_Values'])
            ->get();

        return $attribOptsArr;
    }

    public function getAvailChildProds($parentProdId, $fromDate, $toDate, $extendedDays, $defaultFilterArr,$orderedProdsArr=array(),$selectedFilter = 0)
    {
        $availableProdsArr = array();
        $getChildProdsArr = $this->getChildProducts($parentProdId);
        if (!empty($getChildProdsArr)) {
            /*For handicap*/
            if (empty($defaultFilterArr)) {
                $defaultFilterArr = (!empty(session()->get('defaultFilterArr')) ? session()->get('defaultFilterArr') : Config::get('constants.Default_Filter'));
            }
            /*if($selectedFilter == 60 && $defaultFilterArr[0] == 60){
                $defaultFilterArr[1] = 55;
                $defaultFilterArr[2] = 27;
                $defaultFilterArr[3] = 58;
                $defaultFilterArr[4] = 0;
            }*/
//            $HandicapAttribArr = $this->getProdIDByAttributes($defaultFilterArr[4]);

            $FlexAttribArr = $this->getProdIDByAttributes($defaultFilterArr[3]);

            $HandAttribArr = $this->getProdIDByAttributes($defaultFilterArr[1]);

            $ShaftAttribArr = $this->getProdIDByAttributes($defaultFilterArr[2]);

            $GenderAttribArr = $this->getProdIDByAttributes($defaultFilterArr[0]);
            $addedProdArr = array();
            if(count($orderedProdsArr)>0){
                foreach ($orderedProdsArr as $orderedProds){
                    array_push($addedProdArr, $orderedProds->product_id);
                }
            }
//            in_array($childArr->id, $HandicapAttribArr)&&
            foreach ($getChildProdsArr as $childArr) {
                if (in_array($childArr->id, $FlexAttribArr) && in_array($childArr->id, $HandAttribArr) && in_array($childArr->id, $ShaftAttribArr) && in_array($childArr->id, $GenderAttribArr) && !in_array($childArr->id, $addedProdArr)) {
                    $CheckProdArr = $this->checkChildForBooking($childArr->id, $fromDate, $toDate, $extendedDays);
                    if (empty($CheckProdArr[0])) {
                        $ArrtibSetArr = $this->getProdAttribsByProdId($childArr->id);
                        if(count($ArrtibSetArr)>0){
                            foreach ($ArrtibSetArr as $attrib){
                                if($attrib->attrib_name == 'Handicap'){
                                    $childArr->handicap = $attrib->value;
                                }
                            }
                        }
                        array_push($availableProdsArr, $childArr);
                    }
                }
            }
        }

        return $availableProdsArr;
    }

    /**
     * @return mixed
     */
    public function getChildProducts($parentProdId)
    {
        $ChildProdArr = DB::table($this->DBTables['Group_Products'] . ' as GP')
            ->join($this->DBTables['Products'] . ' as P', 'GP.product_id', '=', 'P.id')
            ->where('P.disable', '=', 0)
            ->where('GP.parent_productid', '=', (int)$parentProdId)
            ->select('P.*')
            ->get();

        return $ChildProdArr;
    }

    public function getProdIDByAttributes($attribValId, $attribArr = array())
    {
        if ($attribValId > 0) {
            $prodIdArr = DB::table($this->DBTables['Products_Attribute_Mapping'])
                ->where('attrib_val_id', '=', (int)$attribValId)
                ->get();
        } else {
            $prodIdArr = DB::table($this->DBTables['Products_Attribute_Mapping'])
                ->get();
        }


        if (!empty($prodIdArr)) {
            foreach ($prodIdArr as $AttribProd) {
                array_push($attribArr, $AttribProd->prod_id);
            }
        }

        return $attribArr;
    }

    public function checkChildForBooking($childProdId, $fromDate, $toDate, $extendedDays)
    {
        $CheckProdArr = DB::table($this->DBTables['Booked_Products'] . ' as BP')
            ->join($this->DBTables['Products'] . ' as P', 'BP.product_id', '=', 'P.id')
            ->where('P.disable', '=', 0)
            ->where('BP.product_id', '=', (int)$childProdId)
            ->whereBetween('dt_booked_from', [
                date('Y-m-d', strtotime($fromDate. ' -' . $extendedDays . ' days')),
                date('Y-m-d', strtotime($toDate . ' -' . $extendedDays . ' days'))
            ])
            ->select('P.*', 'BP.dt_booked_from', 'BP.dt_booked_upto')
            ->get();
        return $CheckProdArr;
    }

    public function getUpsellProducts()
    {
        /*$UpsellProdArr = DB::table( $this->DBTables['Upsell_Products'] . ' as UP' )
                           ->join( $this->DBTables['Products'] . ' as P', 'UP.upsell_prod_id', '=', 'P.id' )
                           ->where( 'UP.product_id', '=', (int) $prodid )
                           ->select( 'P.*' )
                           ->get();*/
        $UpsellProdArr = DB::table($this->DBTables['Products'])
            ->where([['disable', '=', 0],['product_type', '!=', 4], ['product_type', '!=', 5]])
            ->get();

        return $UpsellProdArr;
    }

    public function callMethodByRequest(Request $request)
    {
        if ($request->functionname == 'checkAvailProdsForBooking') {
            $BookedProdsArr = array();
            if (!empty($request->childProdIdArr)) {
                foreach ($request->childProdIdArr as $childProdId) {
                    $ProdDataArr = $this->checkChildForBooking($childProdId['product_id'], $request->fromDate, $request->toDate, $request->extendedDays);
                    if (count($ProdDataArr) > 0) {
                        array_push($BookedProdsArr, $ProdDataArr[0]->id);
                    }
                }
            }

            return $BookedProdsArr;
        } elseif ($request->functionname == 'getParentProductByChildId') {
            $BookedProdsArr = array();
            if (!empty($request->childProdIdArr)) {
                foreach ($request->childProdIdArr as $childProdId) {
                    $parentProdDetArr = $this->getParentProductByChildId($childProdId);
                    if (count($parentProdDetArr) > 0) {
                        array_push($BookedProdsArr, $parentProdDetArr[0]->name);
                    }
                }
            }

            return $BookedProdsArr;
        } elseif ($request->functionname == 'getOfferDetails') {
            $offerCodeDetArr = array();
            if (!empty($request->offercode)) {
                $date = date('Y-m-d');
                $offerCode = $request->offercode;

                $offerCodeDetArr = $this->getOfferDetails($date, $offerCode);
            }

            return $offerCodeDetArr;
        } elseif ($request->functionname == 'getProductGalleryImgs') {
            $gallaryImagesArr = array();
            if (!empty($request->product_id)) {
                $gallaryImagesArr = $this->pcrud->getProductGalleryImgs($request->product_id);
            }
            return $gallaryImagesArr;
        } elseif ($request->functionname == 'getGiftProdDetails') {
            $giftInfo = array();
            if (count($request->productids) > 0) {

                /* print_r($request->productids);
                 die;*/
                $giftInfo = $this->getListOfProdsByIds($request->productids);
            }
            return $giftInfo;
        } elseif ($request->functionname == 'getCCOfferDetails') {
            $offerCodeDetArr = array();
            if (!empty($request->offercode)) {
                $date = date('Y-m-d');
                $offerCode = $request->offercode;

                $offerCodeDetArr = $this->getVoucherDetails($date, $offerCode);
            }

            return $offerCodeDetArr;
        }
    }

    public function filterProducts(Request $request)
    {
        $AddedProdsArr = array();
        if (Auth::guest()) {
            $ParentProdsArr = $this->getParentHireProduct();
            $AllAvailProdsArr = array();
            session()->put('defaultFilterArr', $request->filterArr);
            $defaultFilterArr = session()->get('defaultFilterArr');
            $servicingDays = Config::get('constants.stateServicingDays');
            $extendedDays = $servicingDays[session()->get('states')];
            if(isset($_COOKIE['order_reference_id'])){
                $AddedProdsArr = $this->preOrderProdMapDetails($_COOKIE['order_reference_id']);
            }
            if (!empty($ParentProdsArr)) {
                foreach ($ParentProdsArr as $Parentprod) {
                    $AvailProdArr = $this->getAvailChildProds($Parentprod->id, $request->fromDate, $request->toDate, $extendedDays, $defaultFilterArr,$AddedProdsArr,$request->attribOptId);

                    if (!empty($AvailProdArr)) {
                        $ParentProdDetailArr['parent-prod-id'] = $Parentprod->id;
                        $ParentProdDetailArr['parent-prod-name'] = $Parentprod->name;
                        $ParentProdDetailArr['parent-prod-description'] = $Parentprod->description;
                        $ParentProdDetailArr['parent-prod-feat_img'] = $Parentprod->feat_img;
                        $ParentProdDetailArr['parent-prod-sku'] = $Parentprod->sku;
                        $ParentProdDetailArr['parent-prod-quantity'] = count($AvailProdArr);
                        $ParentProdDetailArr['parent-prod-price'] = $Parentprod->price;
                        $ParentProdDetailArr['parent-prod-category'] = $Parentprod->category;
                        $ParentProdDetailArr['parent-prod-product_type'] = $Parentprod->product_type;
                        $ParentProdDetailArr['parent-prod-is_upsell_product'] = $Parentprod->is_upsell_product;
                        $ParentProdDetailArr['parent-prod-sale'] = $Parentprod->sale;
                        $ParentProdDetailArr['parent-prod-sale_price '] = $Parentprod->sale_price;
                        $ParentProdDetailArr['parent-prod-rent'] = $Parentprod->rent;
                        $ParentProdDetailArr['parent-prod-rent_price'] = $Parentprod->rent_price;
                        $ParentProdDetailArr['parent-prod-prod_video'] = $Parentprod->prod_video;
                        $ParentProdDetailArr['parent-prod-attrib-handicap'] = $AvailProdArr[0]->handicap;
                        array_push($AvailProdArr, $ParentProdDetailArr);
                        array_push($AllAvailProdsArr, $AvailProdArr);
                    }

                }
            }

            return $AllAvailProdsArr;
        } else {
            return redirect('/dashboard');
        }
    }

    public function getCartProdIdsByRefId(Request $request)
    {
        $cartProdIdArr = DB::table($this->DBTables['Pre_Orders_Products'])
            ->where('order_reference_id', '=', $request->orderRefId)
            ->select('product_id')
            ->get();

        return $cartProdIdArr;
    }

    public function getAllOrderedHierableProds($orderRefId)
    {
        $preOrderProdDetArr = DB::table($this->DBTables['Pre_Orders_Products'] . ' as pop')
            ->join($this->DBTables['Products'] . ' as p', 'pop.product_id', '=', 'p.id')
            ->where([
                ['pop.order_reference_id', '=', $orderRefId],
                ['p.product_type', '=', 5]
            ])
            ->select('p.*', 'pop.pre_order_id', 'pop.quantity as ord_qty', 'pop.sub_total_amnt')
            ->get();
        return $preOrderProdDetArr;
    }

    public function addremoveInsuranceToOrder(Request $request)
    {
        $InsuranceAmount = Config::get('constants.InsurancePrice');
        if ($request->flag == 1) {
            $getinsurableProds = $this->getAllOrderedHierableProds($request->orderRefId);
            if (count($getinsurableProds) > 0) {
                $insuranceCost = count($getinsurableProds) * $InsuranceAmount;
                $PreorderDetArr = $this->getPreOrderDetails($request->orderRefId);
                if (count($PreorderDetArr) > 0) {
                    $result = DB::table($this->DBTables['Pre_Orders'])
                        ->where('order_reference_id', '=', $request->orderRefId)
                        ->update([
                            'total_amnt' => DB::raw('total_amnt - insurance_amnt')
                        ]);
                        DB::table($this->DBTables['Pre_Orders'])
                            ->where('order_reference_id', '=', $request->orderRefId)
                            ->update([
                                'insurance_amnt' => $insuranceCost,
                                'total_amnt' => DB::raw('total_amnt + ' . $insuranceCost)
                            ]);
                }
            }

        } elseif ($request->flag == 2) {
            $PreorderDetArr = $this->getPreOrderDetails($request->orderRefId);
            if (count($PreorderDetArr) > 0 && $PreorderDetArr[0]->insurance_amnt > 0) {
                DB::table($this->DBTables['Pre_Orders'])
                    ->where('order_reference_id', '=', $request->orderRefId)
                    ->update([
                        'insurance_amnt' => 0.00,
                        'total_amnt' => DB::raw('total_amnt - ' . $PreorderDetArr[0]->insurance_amnt)
                    ]);
            }

        }

        return '1';
    }
    public function getCart($cartDetailArr)
    {
        $searchKeyArr = array();

        $uniqueIdArr = array();
        $keyArr = array();
//		dd($cartDetailArr);
        foreach ($cartDetailArr as $cartKey => $CartDet) {
            if ($CartDet['price'] > 0) {

                if (!in_array($CartDet['prod-id'], $uniqueIdArr)) {
                    array_push($uniqueIdArr, $CartDet['prod-id']);
                    array_push($cartDetailArr[$cartKey]['allAttribSet'], $cartDetailArr[$cartKey]['attributes']);
//                    $cartDetailArr[$cartKey]['allAttribSet']['pid'] = $cartDetailArr[$cartKey]['prodidArr'][0];
                    $keyArr[$CartDet['prod-id']] = $cartKey;
                } else {
                    foreach ($cartDetailArr[$keyArr[$CartDet['prod-id']]]['prodidArr'] as $prodId) {
                        array_push($cartDetailArr[$cartKey]['prodidArr'], $prodId);
                    }
                    $pkey = 0;

                    array_push($cartDetailArr[$cartKey]['allAttribSet'], $cartDetailArr[$cartKey]['attributes']);
                    foreach ($cartDetailArr[$keyArr[$CartDet['prod-id']]]['allAttribSet'] as $attribKey => $attribsets) {
                        $pkey = $attribKey;
                        array_push($cartDetailArr[$cartKey]['allAttribSet'], $attribsets);
//                        $cartDetailArr[$cartKey]['allAttribSet']['pid'] = $cartDetailArr[$keyArr[$CartDet['prod-id']]]['prodidArr'][$attribKey];
                    }
//                    $cartDetailArr[$cartKey]['allAttribSet']['pid'] = $cartDetailArr[$keyArr[$CartDet['prod-id']]]['prodidArr'][$pkey+1];

                    $cartDetailArr[$cartKey]['quantity'] = $cartDetailArr[$cartKey]['quantity'] + $cartDetailArr[$keyArr[$CartDet['prod-id']]]['quantity'];
                    unset($cartDetailArr[$keyArr[$CartDet['prod-id']]]);
                    $keyArr[$CartDet['prod-id']] = $cartKey;
                }
            }
        }
//        dd($cartDetailArr);
        foreach ($cartDetailArr as $cartDetKey => $CartDetVal) {
            if ($cartDetailArr[$cartDetKey]['price'] > 0) {
                if (count($cartDetailArr[$cartDetKey]['prodidArr']) > 1) {
                    foreach ($cartDetailArr[$cartDetKey]['prodidArr'] as $pkey => $childProdIds) {
                        $arrColUsed = array_column($cartDetailArr[$cartDetKey]['childProdArr'], 'id');
                        $keyUsed = array_search($childProdIds, $arrColUsed);
                        unset($cartDetailArr[$cartDetKey]['childProdArr'][$keyUsed]);
                        $cartDetailArr[$cartDetKey]['childProdArr'] = array_values($cartDetailArr[$cartDetKey]['childProdArr']);
                    }
                }
            }
        }
        foreach ($cartDetailArr as $index => $value) {
            $arrCol = array_column($cartDetailArr, 'prod-id');

            $key = array_search($value['prod-id'], $uniqueIdArr);
            if (!in_array($key, $searchKeyArr)) {
                array_push($searchKeyArr, $key);
            } else {
                if ($cartDetailArr[$index]['price'] > 0) {
                    foreach ($cartDetailArr[$index]['prodidArr'] as $prodIndex => $prodVal) {
                        $arrColUsed = array_column($cartDetailArr[$key]['childProdArr'], 'id');
                        $keyUsed = array_search($prodVal, $arrColUsed);
                        $cartDetailArr[$key]['quantity'] = $cartDetailArr[$key]['quantity'] + $cartDetailArr[$index]['quantity'];
                        $cartDetailArr[$key]['price'] = $cartDetailArr[$key]['price'] + $cartDetailArr[$index]['price'];
                        $cartDetailArr[$key]['setcount'] = $cartDetailArr[$index]['setcount'];
                        $cartDetailArr[$key]['Discount'] = $cartDetailArr[$index]['Discount'];
                        $cartDetailArr[$key]['subtotal'] = $cartDetailArr[$index]['subtotal'] * $cartDetailArr[$key]['quantity'];
                        array_push($cartDetailArr[$key]['prodidArr'], $prodVal);
                        array_push($cartDetailArr, $cartDetailArr[$key]);
                    }
                }
            }
        }
        return $cartDetailArr;
    }

    public function insurance()
    {
        $this->setLang();
        $page1 = session()->get('page1');
        $EstimatedShipping = Config::get('constants.stateEstimatedShipping');
        if(isset($page1) && isset($_COOKIE['order_reference_id']) && $page1 == '1' && $_COOKIE['order_reference_id'] !== null){
            session()->put('page2','1');
            View::share('filter', 'insurance');
            View::share('title', 'Extras &amp;  Insurance');
            View::share('redirectPage', 'shipping');
            View::share('Page', 'insurance');
            View::share('PageHeading', 'Extras &amp;  Insurance');
            View::share('PageDescription1', 'Add some extras to your bag or protect your clubs with insurance.');
            View::share('PageDescription2', '');
            $TotalUpsellProdsArr = array();
            $UpsellProdArr = $this->getUpsellProducts();
            if (!empty($UpsellProdArr)) {
                array_push($TotalUpsellProdsArr, $UpsellProdArr);
            }
            $cartDetailArr = array();
            if ($_COOKIE['order_reference_id'] !== null) {
                $cartDetailArr = $this->getCartByRefId($_COOKIE['order_reference_id']);
                $cartDetailArr = $this->getCart($cartDetailArr);
            }
            $preOrderArr = $this->getPreOrderDetails($_COOKIE['order_reference_id']);
            if (!empty($preOrderArr) && $preOrderArr[0]->shipping_amnt > 0) {
                $this->setShippingZeroByOrderRefId($_COOKIE['order_reference_id'], $preOrderArr[0]->shipping_amnt);
            }
            $insurance = 0;
            if (!empty($preOrderArr)) {
                $insurance = $preOrderArr[0]->insurance_amnt;
            }
            $setCount = $this->getCartSetCount($_COOKIE['order_reference_id']);

            return view('pages.frontend.insurance', compact('TotalUpsellProdsArr', 'cartDetailArr', 'insurance','setCount','EstimatedShipping'));
        }else{
            return redirect()->to('/');
        }

    }

    public function checkTssSubscription($email)
    {
        $TSSSubscriptionArr = DB::table($this->DBTables['TSS'])
            ->where('email', '=', $email)
            ->get();

        return $TSSSubscriptionArr;
    }

    public function SubscribeToTss($emailid)
    {
        $res = DB::table($this->DBTables['TSS'])->insert(
            ['email' => $emailid, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
        );

        return $res;
    }

    public function updateInvoiceNo($invoiceNo, $orderRefId, $transactionId)
    {
        $result = DB::table($this->DBTables['Pre_Orders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->update([
                'invoice_no' => $invoiceNo,
                'payment_transaction_id' => $transactionId
            ]);
        return $result;
    }

    public function getOfferDetails($date, $offerCode)
    {
        $result = DB::table($this->DBTables['Offers'])
            ->where('szCoupnCode', 'like', trim($offerCode))
            ->whereDate('dt_upto', '>=', trim($date))
            ->get();
        return $result;

    }

    public function getVoucherDetails($date, $offerCode)
    {
        $result = DB::table($this->DBTables['CCVouchers'])
            ->where('szCoupnCode', 'like', trim($offerCode))
            ->whereDate('dt_upto', '>=', trim($date))
            ->get();
        return $result;

    }

    public function checkOfferAppliedInPreOrder($offerCode,$order_reference_id)
    {
        $result = DB::table($this->DBTables['Pre_Orders'])
            ->where('offer_Code', 'like', trim($offerCode))
            ->where('offer_applied', '=', 0)
            ->where('order_reference_id', '=', $order_reference_id)
            ->get();
        return $result;
    }

    public function checkOfferAppliedInOrder($offerCode)
    {
        $result = DB::table($this->DBTables['Orders'])
            ->where('offer_Code', 'like', trim($offerCode))
            ->get();
        return $result;
    }

    public function updateOfferCode($orderRefID, $offerId, $offerCode, $offerType, $offerPercentage,$flatDiscount)
    {
        $result = DB::table($this->DBTables['Pre_Orders'])
            ->where('order_reference_id', '=', $orderRefID)
            ->update([
                'offer_id' => $offerId,
                'offer_Code' => $offerCode,
                'offer_type' => $offerType,
                'offer_percntg' => $offerPercentage,
                'offer_amnt' => $flatDiscount,
                'offer_applied' => 1
            ]);
        return $result;
    }

    public function removeOrder(Request $request)
    {
        $delOrderRes = DB::table($this->DBTables['Pre_Orders'])->where('order_reference_id', '=', $request->order_reference_id)->delete();
//		if($delOrderRes){
        $delOrderDetRes = DB::table($this->DBTables['Pre_Orders_Products'])->where('order_reference_id', '=', $request->order_reference_id)->delete();
//			if($delOrderDetRes){
        setcookie('order_reference_id', $request->order_reference_id, time() - (86400 * 10), "/");
        $request->dt_book_from = $this->formatDates($request->dt_book_from);
        $request->dt_book_upto = $this->formatDates($request->dt_book_upto);
        session()->put('fromDate', $request->dt_book_from);
        session()->put('toDate', $request->dt_book_upto);
        session()->put('states', $request->states);
        session()->put('showgift', '1');
        session()->forget('page2');
        session()->forget('page3');
        session()->forget('page4');
        session()->put('page1', '1');
        return '1';
        /*}else{
            return '0';
        }
    }else{
        return '0';
    }*/
    }

    public function calculateshipping(Request $request)
    {
        $pickUp = $request->pickup;
        $pickupState = $request->pickupState;
        $dropOff = $request->dropoff;
        $dropoffState = $request->dropoffState;
        $totalShipping = 0;
        $finalShipping = 0;
        if (!empty($pickUp)) {
            $PickUpresult = DB::table($this->DBTables['Shipping'].' as shp')
                ->join($this->DBTables['Regions'].' as reg', 'reg.id', '=', 'shp.region_id')
                ->where([['reg.stateid', '=', $pickupState],['shp.postcode', '=', $pickUp]])
                ->select('shp.id', 'shp.postcode', 'shp.shipping_cost', 'shp.suburb', 'reg.region')
                ->get();
            if (count($PickUpresult) > 0) {
                $totalShipping = $totalShipping + $PickUpresult[0]->shipping_cost;
            }else{
                $totalShipping = $totalShipping + 50;
            }
        }
        if (!empty($dropOff)) {
            $DropOffresult = DB::table($this->DBTables['Shipping'].' as shp')
                ->join($this->DBTables['Regions'].' as reg', 'reg.id', '=', 'shp.region_id')
                ->where([['reg.stateid', '=', $dropoffState],['shp.postcode', '=', $dropOff]])
                ->select('shp.id', 'shp.postcode', 'shp.shipping_cost', 'shp.suburb', 'reg.region')
                ->get();
            if (count($DropOffresult) > 0) {
                $totalShipping = $totalShipping + $DropOffresult[0]->shipping_cost;
            }else{
                $totalShipping = $totalShipping + 50;
            }
        }
        $freightPrice = Config::get('constants.FreightPrice');
        if ($totalShipping > $freightPrice) {
            $finalShipping = $totalShipping - $freightPrice;
        }
        return $finalShipping;
    }

    public function setShippingZeroByOrderRefId($orderRefId, $shippingAmount)
    {
        $result = DB::table($this->DBTables['Pre_Orders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->update([
                'shipping_amnt' => 0.00,
                'total_amnt' => DB::raw('total_amnt - ' . $shippingAmount)
            ]);
        return $result;
    }

    public function clearfilter(Request $request)
    {
        if (!empty(session()->get('defaultFilterArr'))) {
            $request->session()->forget('defaultFilterArr');
        }
        return '1';
    }

    public function getSiteFooter(Request $request)
    {
        $url = $request->siteUrl;
        if ($url != "")
            $sitehtml = file_get_contents($url);
        die($sitehtml);
    }

    public function getOrderProdDetailWithProdIdAndAmount($orderRefId, $ProdId, $amount)
    {
        $result = DB::table($this->DBTables['Pre_Orders_Products'])
            ->where([
                ['product_id', '=', $ProdId],
                ['order_reference_id', '=', $orderRefId],
                ['sub_total_amnt', '=', $amount]
            ])
            ->get();
        return $result;
    }

    public function getDaysFromDates($fromdate, $todate)
    {
        $oneDayTime = 84600;
        $dt_book_from = date('Y-m-d', strtotime($fromdate));
        $dt_book_upto = date('Y-m-d', strtotime($todate));
        $days_count = ((strtotime($dt_book_upto) - strtotime($dt_book_from)) / $oneDayTime) + 1;
        $hire_days = number_format($days_count, 0);
        return $hire_days;
    }

    public function updatePaymentOpt(Request $request){
        $result = $this->updatePaymentOptByParameter($request->orderRefId,$request->optval);
        return $result;
    }

    public function updatePaymentOptByParameter($orderRefId,$paymentOpt){
        $result = DB::table($this->DBTables['Pre_Orders'])
            ->where('order_reference_id', '=', $orderRefId)
            ->update(['payment_option' => $paymentOpt,
                'payment_in_progress' => 1,
                'dtUpdatedOn'=> date('Y-m-d h:i:s')]);
        return $result;
    }

    public function formatDates($dateval){
      $dateArr = explode('/',$dateval);
      $datenewval = $dateArr[2].'-'.$dateArr[0].'-'.$dateArr[1];
      return $datenewval;
    }
    public function SendCronMail(){

        $mail = new PHPMailer(true);
        $fromEmail = Config::get('constants.supportEmailProduction');
        $attach = $this->AdminOrder->courier(true);
        if($attach){
            $handle = fopen (public_path('../data.csv'), "w+");
            fclose($handle);
            file_get_contents(public_path('../data.csv'));
            file_put_contents(public_path('../data.csv'),$attach);
            file_put_contents('../couriers/courier-mail-'.date('d-m-Y-h-i-s').'.csv', $attach, FILE_APPEND);
            sleep(20);
            try{
                $mail->isSMTP();
                $mail->CharSet = 'utf-8'; #set it utf-8
                $mail->SMTPAuth = false; #set it true
                /*$mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com'; #gmail has host  smtp.gmail.com
                $mail->Port = '587'; #gmail has port  587 . without double quotes
                $mail->Username = 'prashant@whiz-solutions.com'; #your username. actually your email
                $mail->Password = 'Whiz@2016'; # your password. your mail password*/
                $mail->setFrom($fromEmail, 'Support - TSS Clubhire');
                $mail->Subject = 'TSS Clubhire - Courier Order Details '.date('d/m/Y',strtotime('-1 day'));
                $mail->MsgHTML('Dear sir/mam <br /><p>Please find attached The Sweet Spot order details for '.date('d/m/Y',strtotime('-1 day')).'</p>');
                $mail->addAttachment(public_path('../data.csv'));
                $mail->addAddress('info@tssclubhire.com' ,'Info');
                $mail->addAddress('hello@lucasarthur.net.au' ,'Luca');
                /*$mail->addAddress('prashant@whiz-solutions.com' ,'Info');
                $mail->addAddress('prashant12it@gmail.com' ,'Luca');*/
                $mail->addBCC('prashant21it@gmail.com','Prashant');
                $mail->addBCC('prashant@whiz-solutions.com' ,'Whiz');
                $mail->addBCC('lukesantamaria@tssclubhire.com','Tss-Clubhire');
                $mail->addBCC('lukecerra@tssclubhire.com','Lukecerra');
                $mail->send();
            }catch(Exception $e){
                dd($e);
            }
        }
    }

    public function DisputedOrderNotification(){
        $mail = new PHPMailer(true);
        $fromEmail = Config::get('constants.supportEmailProduction');
        $currentTime = date('Y-m-d h:i:s');
        $orderIdArr = array();
        $DisputedorderDetails = DB::table($this->DBTables['Pre_Orders'])
            ->where('payment_in_progress', '=', 1)
            ->get();
        if(count($DisputedorderDetails)>0){
            foreach ($DisputedorderDetails as $DisputedOrder){
                if(empty($DisputedOrder->disputed_order_notification)){
                    $orederGenerationTime = $DisputedOrder->dtUpdatedOn;
                    $timeDifference = round(abs(strtotime($currentTime) - strtotime($orederGenerationTime)) / 60,2);
                    if($timeDifference>10){
                        array_push($orderIdArr,$DisputedOrder->order_reference_id);
                    }
                }
            }
        }
        $disputedCounts = count($orderIdArr);
        if($disputedCounts>0){
            $orderRefList = '<ul>';
            foreach ($orderIdArr as $key => $orderRef){
                $orderRefList .= '<li>#'.$orderRef.'</li>';
            }
            $orderRefList .= '</ul>';

        try{
            $mail->isSMTP();
            $mail->CharSet = 'utf-8'; #set it utf-8
            $mail->SMTPAuth = false; #set it true

            $mail->setFrom($fromEmail, 'Support - TSS Clubhire');
            $mail->Subject = 'TSS Clubhire - Disputed Order Notification '.date('d/m/Y H:i:s a');
            $mail->MsgHTML('Dear sir/mam <br /><p>'.$disputedCounts.' disputed orders arrived recently, below are their order reference ID(s). Please check the admin panel and do the needful.</p><p></p>'.$orderRefList);
            $mail->addAddress('info@tssclubhire.com' ,'Info');
            $mail->addAddress('hello@lucasarthur.net.au' ,'Luca');
            /*$mail->addAddress('prashant@whiz-solutions.com' ,'Info');
            $mail->addAddress('prashant12it@gmail.com' ,'Luca');*/
            $mail->send();
            foreach ($orderIdArr as $key => $orderRef){
                $updateAry['disputed_order_notification'] = date('Y-m-d h:i:s');
                DB::table($this->DBTables['Pre_Orders'])->where('order_reference_id', $orderRef)->update($updateAry);
            }
        }catch(Exception $e){
            dd($e);
        }
        }
    }

    public function PickupMailCron(){

        $mail = new PHPMailer(true);
        $fromEmail = Config::get('constants.supportEmailProduction');
        $attach = $this->AdminOrder->courier(true,true);
        if($attach){
            $handle = fopen (public_path('../pickup-data.csv'), "w+");
            fclose($handle);
            file_get_contents(public_path('../pickup-data.csv'));
            file_put_contents(public_path('../pickup-data.csv'),$attach);
            file_put_contents('../couriers/pickup-courier-mail-'.date('d-m-Y-h-i-s').'.csv', $attach, FILE_APPEND);
            sleep(20);
            try{
                $mail->isSMTP();
                $mail->CharSet = 'utf-8'; #set it utf-8
                $mail->SMTPAuth = false; #set it true
                /*$mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com'; #gmail has host  smtp.gmail.com
                $mail->Port = '587'; #gmail has port  587 . without double quotes
                $mail->Username = 'prashant@whiz-solutions.com'; #your username. actually your email
                $mail->Password = 'Whiz@2016'; # your password. your mail password*/
                $mail->setFrom($fromEmail, 'Support - TSS Clubhire');
                $mail->Subject = 'TSS Clubhire - Pickup Courier Order Details '.date('d/m/Y',strtotime('-1 day'));
                $mail->MsgHTML('Dear sir/mam <br /><p>Please find attached The Sweet Spot order details for those hirings which ends on '.date('d/m/Y',strtotime('-1 day')).'</p>');
                $mail->addAttachment(public_path('../pickup-data.csv'));
                $mail->addAddress('info@tssclubhire.com' ,'Info');
                $mail->addAddress('hello@lucasarthur.net.au' ,'Luca');
                /*$mail->addAddress('prashant@whiz-solutions.com' ,'Info');
                $mail->addAddress('prashant12it@gmail.com' ,'Luca');*/
                $mail->addBCC('prashant21it@gmail.com','Prashant');
                $mail->addBCC('prashant@whiz-solutions.com' ,'Whiz');
                $mail->addBCC('lukesantamaria@tssclubhire.com','Tss-Clubhire');
                $mail->addBCC('lukecerra@tssclubhire.com','Lukecerra');
                if($mail->send()){
                    $this->CourierReminderCron();
                }
            }catch(Exception $e){
                dd($e);
            }
        }
    }
    public function CourierReminderCron(){

        $mail = new PHPMailer(true);
        $fromEmail = Config::get('constants.supportEmailProduction');
        $attach = $this->AdminOrder->courier(true,false,true);
        if($attach){
            $handle = fopen (public_path('../courier-reminder-data.csv'), "w+");
            fclose($handle);
            file_get_contents(public_path('../courier-reminder-data.csv'));
            file_put_contents(public_path('../courier-reminder-data.csv'),$attach);
            file_put_contents('../couriers/courier-reminder-mail-'.date('d-m-Y-h-i-s').'.csv', $attach, FILE_APPEND);
            sleep(20);
            try{
                $mail->isSMTP();
                $mail->CharSet = 'utf-8'; #set it utf-8
                $mail->SMTPAuth = false; #set it true
                /*$mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com'; #gmail has host  smtp.gmail.com
                $mail->Port = '587'; #gmail has port  587 . without double quotes
                $mail->Username = 'prashant@whiz-solutions.com'; #your username. actually your email
                $mail->Password = 'Whiz@2016'; # your password. your mail password*/
                $mail->setFrom($fromEmail, 'Support - TSS Clubhire');
                $mail->Subject = 'TSS Clubhire - Courier Order Reminder '.date('d/m/Y',strtotime('+1 day'));
                $mail->MsgHTML('Dear sir/mam <br /><p>Please find attached The Sweet Spot order details for those hirings which start on '.date('d/m/Y',strtotime('+1 day')).'</p>');
                $mail->addAttachment(public_path('../courier-reminder-data.csv'));
                $mail->addAddress('info@tssclubhire.com' ,'Info');
                $mail->addAddress('hello@lucasarthur.net.au' ,'Luca');
                /*$mail->addAddress('prashant@whiz-solutions.com' ,'Info');
                $mail->addAddress('prashant12it@gmail.com' ,'Luca');*/
                $mail->addBCC('prashant21it@gmail.com','Prashant');
                $mail->addBCC('prashant@whiz-solutions.com' ,'Whiz');
                $mail->addBCC('lukesantamaria@tssclubhire.com','Tss-Clubhire');
                $mail->addBCC('lukecerra@tssclubhire.com','Lukecerra');
                $mail->send();
            }catch(Exception $e){
                dd($e);
            }
        }
    }
    public function sendMandrilSecondMail(Mail $mandrill)
    {
        $supportEmail = Config::get('constants.customerSupportEmail');
        $reqHireDate = date('Y-m-d',strtotime('+2 days'));
//        $reqHireDate = '2017-11-16';
        $ordersAry = Orders::where('dt_book_from','Like','%'.$reqHireDate.'%')->orderBy('id', 'DESC')->get();

        if (count($ordersAry) > 0) {
            foreach ($ordersAry as $orders) {
                try{
                    $template_content[] = array(
                        'name' => 'FNAME',
                        'content' => $orders->user_name
                    );
                    $template_content[] = array(
                        'name' => 'EMAIL_ADDRESS',
                        'content' => $supportEmail
                    );
                    $template_content[] = array(
                        'name' => 'COMPANY',
                        'content' => 'The Sweet Spot Club Hire'
                    );
                    $to_addresses = array();
                    $to_addresses[0]['name'] = $orders->user_name;
                    $to_addresses[0]['email'] = $orders->user_email;
                    $to_addresses[0]['type'] = 'to';
                    $message = array(
                        'subject' => 'The Sweet Spot Club Hire - Your club hire is soon.',
                        'html' => '',
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
                    $result = $mandrill->messages()->sendTemplate('2nd email Customer Purchase', $template_content, $message);
                    if($result[0]['status'] != 'sent')
                    {
                        file_put_contents('../mandrill-second-fail.txt', "Following email has been ".$result[0]['status']."-\nSubject: ".$message['subject']."\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: ".$message['from_email']."\nErorr: ".$result[0]['reject_reason']."\nTime: ".date("m/d/Y h:i:s A")."\n\n",FILE_APPEND);
                    }
                    else
                    {
                        file_put_contents('../mandrill-second-success.txt', "Following email has been sent successfully-\nSubject: ".$message['subject']."\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: ".$message['from_email']."\nTime: ".date("m/d/Y h:i:s A")."\n\n",FILE_APPEND);
                    }
                } catch(Mandrill_Error $e) {

                    file_put_contents('../mandrill-second-fail.txt', date("m/d/Y h:i:s A") . "Error: " . $e->getMessage() . "\n\n");
//                                dd($e->getMessage());
                }
            }
        }
	}

    public function sendMandrilThirdMail(Mail $mandrill)
    {
        $supportEmail = Config::get('constants.customerSupportEmail');
        $reqHireDate = date('Y-m-d',strtotime('-5 days'));
//        dd($reqHireDate);
//        $reqHireDate = '2017-11-16';
        $ordersAry = Orders::where('dt_book_from','Like','%'.$reqHireDate.'%')->orderBy('id', 'DESC')->get();

        if (count($ordersAry) > 0) {
            foreach ($ordersAry as $orders) {
                try{
                    $template_content[] = array(
                        'name' => 'FNAME',
                        'content' => $orders->user_name
                    );
                    $template_content[] = array(
                        'name' => 'EMAIL_ADDRESS',
                        'content' => $supportEmail
                    );
                    $template_content[] = array(
                        'name' => 'COMPANY',
                        'content' => 'The Sweet Spot Club Hire'
                    );
                    $to_addresses = array();
                    $to_addresses[0]['name'] = $orders->user_name;
                    $to_addresses[0]['email'] = $orders->user_email;
                    $to_addresses[0]['type'] = 'to';
                    $message = array(
                        'subject' => 'The Sweet Spot Club Hire - We hope you enjoyed your trip.',
                        'html' => '',
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
                    $result = $mandrill->messages()->sendTemplate('3rd email Post Hire (5th Day post Last Hire)', $template_content, $message);
                    if($result[0]['status'] != 'sent')
                    {
                        file_put_contents('../mandrill-third-fail.txt', "Following email has been ".$result[0]['status']."-\nSubject: ".$message['subject']."\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: ".$message['from_email']."\nErorr: ".$result[0]['reject_reason']."\nTime: ".date("m/d/Y h:i:s A")."\n\n",FILE_APPEND);
                    }
                    else
                    {
                        file_put_contents('../mandrill-third-success.txt', "Following email has been sent successfully-\nSubject: ".$message['subject']."\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: ".$message['from_email']."\nTime: ".date("m/d/Y h:i:s A")."\n\n",FILE_APPEND);
                    }
                } catch(Mandrill_Error $e) {

                    file_put_contents('../mandrill-third-fail.txt', date("m/d/Y h:i:s A") . "Error: " . $e->getMessage() . "\n\n");
//                                dd($e->getMessage());
                }
            }
        }
    }
    public function checkCCregionexist($region){
        $ProdsArr = DB::table($this->DBTables['CCRegion'])
            ->where('region', '=', $region)
            ->select('id')
            ->get();
        return $ProdsArr;
    }
    public function importcccost(){
        dd();
        $data = Excel::load('../couriers/clubcourierprices.xlsx', function($reader) {
        })->get();
        if(!empty($data) && $data->count()){

            foreach ($data as $key => $value) {
                if(!empty($value->from)){

                    $fromId = 0;
                    $toId = 0;
                    $existingRegion = $this->checkCCregionexist($value->from);
                    if(count($existingRegion)>0){
                        $fromId = $existingRegion[0]->id;
                    }else{
                        $fromId = DB::table($this->DBTables['CCRegion'])->insertGetId(
                            ['region' => $value->from]
                        );
                    }
                    $existingRegion = $this->checkCCregionexist($value->to);
                    if(count($existingRegion)>0){
                        $toId = $existingRegion[0]->id;
                    }else{
                        $toId = DB::table($this->DBTables['CCRegion'])->insertGetId(
                            ['region' => $value->to]
                        );
                    }
                    $CostInsertId = DB::table($this->DBTables['CCCost'])->insertGetId(
                        ['from_region_id' => $fromId, 'to_region_id'=>$toId, 'small_bag_cost'=>$value->small_bag_rrp,'standard_bag_cost'=>$value->standard_bag_rrp,'large_bag_cost'=>$value->large_bag_rrp,'transit_days'=>$value->transit_time_days]
                    );
                }
            }
        }
    }
}

