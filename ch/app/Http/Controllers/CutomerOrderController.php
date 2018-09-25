<?php

namespace App\Http\Controllers;

use App;
use Carbon\Carbon;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Newsletter;
use Session;
use Validator;
use View;
use Weblee\Mandrill\Mail;
use XeroPrivate;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Error\Card;

class CutomerOrderController extends Controller
{
    public function __construct()
    {
        $this->hire = new HireController;
        $this->utility = new UtilityController;
        $this->DBTables = Config::get('constants.DbTables');
    }

    public function getAvailableHireProducts(Request $request)
    {

        if (trim($request->dt_book_from) != '' && trim($request->dt_book_upto) != '') {
            $dt_book_from = date('Y-m-d', strtotime($request->dt_book_from));
            $dt_book_upto = date('Y-m-d', strtotime($request->dt_book_upto));
        } else {
            $dt_book_from = date('Y-m-d', strtotime("+7 DAYS"));
            $dt_book_upto = date('Y-m-d', strtotime($dt_book_from . " +7 DAYS"));
        }

    }

    public function getUniqueReferenceId()
    {

        $DBTables = Config::get('constants.DbTables');
        $t_pre_ord = $DBTables['Pre_Orders'];
        $t_ord = $DBTables['Orders'];

        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        $reference_key = "";
        $unique_key = false;

        while ($i <= 7) {
            $reference_key .= $chars{mt_rand(0, strlen($chars) - 1)};
            $i++;
        }
        if (trim($reference_key) != '') {
            do {
                $invalid = 1;

                $orderKeys = DB::table($t_ord)
                    ->where('order_reference_id', '=', trim($reference_key))
                    ->select('id')
                    ->get();

                if (count($orderKeys) == 0) {

                    $preOrderKeys = DB::table($t_pre_ord)
                        ->where('order_reference_id', '=', trim($reference_key))
                        ->select('id')
                        ->get();

                    if (count($preOrderKeys) == 0) {
                        $unique_key = true;
                    } else {
                        $reference_key .= $reference_key . "#" . $invalid;
                        $invalid = $invalid + 1;
                    }
                } else {
                    $reference_key .= $reference_key . "#" . $invalid;
                    $invalid = $invalid + 1;
                }

            } while (!$unique_key);

            return $reference_key;
        }


    }

    public function addProductToCart(Request $request)
    {

        $response = array();
        if ($request->order_reference_id == '') {
            //$order_reference_id = $this->getUniqueReferenceId();
            $order_reference_id = time();
            setcookie('order_reference_id', $order_reference_id, time() + (86400 * 10), "/");
            $newOrder = true;
        } else {
            $order_reference_id = trim($request->order_reference_id);
//			session()->put( 'order_reference_id', $order_reference_id );
            setcookie('order_reference_id', $order_reference_id, time() + (86400 * 10), "/");

            $newOrder = false;
        }
        $orderRefExistance = $this->checkOrderRefIdExist($request->order_reference_id);

        if (count($orderRefExistance) == 0) {
            $newOrder = true;
        } else {
            $newOrder = false;
        }
        $DBTables = Config::get('constants.DbTables');
        $t_pre_ord = $DBTables['Pre_Orders'];

        $productData = $request->input();

        $rules = array(
            'product_idArr' => 'required',
            'quantity' => 'required | numeric | between:1,99',
            'dt_book_from' => 'required',
            'dt_book_upto' => 'required',
            'state_id' => 'required'
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "ERROR";
            $response['errors'] = $this->formatValidationErrors($validator);
        } else {
            $oneDayTime = 86400;

            $dt_book_from = date('Y-m-d 00:00:00', strtotime($productData['dt_book_from']));
            $dt_book_upto = date('Y-m-d 23:59:59', strtotime($productData['dt_book_upto']));

            $days_count = ((strtotime($dt_book_upto) - strtotime($dt_book_from)) / $oneDayTime);
            $hire_days = number_format($days_count, 0);
            $product_idArr = $productData['product_idArr'];
            $quantity = (int)$productData['quantity'];
            if ($newOrder == true) {
                $pre_order_insert_ary = array();

                $pre_order_insert_ary['order_reference_id'] = trim($order_reference_id);
                $pre_order_insert_ary['dt_book_from'] = date('Y-m-d 00:00:00', strtotime($productData['dt_book_from']));
                $pre_order_insert_ary['dt_book_upto'] = date('Y-m-d 23:59:59', strtotime($productData['dt_book_upto']));
                $pre_order_insert_ary['dtCreatedOn'] = date('Y-m-d H:i:s');
                $pre_order_insert_ary['hire_days'] = $hire_days;
                $pre_order_insert_ary['state_id'] = $productData['state_id'];
                $id_pre_order = DB::table($t_pre_ord)->insertGetId($pre_order_insert_ary);

                $PreorderDetArr = $this->hire->getPreOrderDetails($pre_order_insert_ary['order_reference_id']);
                if (count($PreorderDetArr) > 0) {
                    $offerProdId = Config::get('constants.PromotionProductId');
                    $hireDays = (int)$PreorderDetArr[0]->hire_days;
                    if ($hireDays > 0) {
                        $giftProdId = $this->hire->getGiftProds($hireDays);
                        if (count($giftProdId) > 0) {
                            foreach ($giftProdId as $gift) {
                                $CheckOfferAdded = $this->hire->getOrderProdDetailWithProdIdAndAmount($pre_order_insert_ary['order_reference_id'], $gift, 0.00);
                                if (count($CheckOfferAdded) == 0) {
                                    $GiftAry = array();
                                    $GiftAry['order_reference_id'] = $pre_order_insert_ary['order_reference_id'];
                                    $GiftAry['pre_order_id'] = (int)$PreorderDetArr[0]->id;
                                    $GiftAry['product_id'] = $gift;
                                    $GiftAry['dtCreatedOn'] = $pre_order_insert_ary['dtCreatedOn'];
                                    $GiftAry['quantity'] = $quantity;
                                    $GiftAry['product_attributes'] = 'Free';
                                    $this->addProductInOrder($GiftAry);
                                }
                            }
                        }


                    }
                }

            } else {

                $pre_order = DB::table($t_pre_ord)
                    ->where('order_reference_id', '=', trim($order_reference_id))
                    ->select('id')
                    ->get();

                if (count($pre_order) > 0) {
                    $id_pre_order = (int)$pre_order[0]->id;
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Invalid order refrence id.";

                    return $response;
                }
            }

            $createdOn = date('Y-m-d H:i:s');


            $parent_prod_id = (int)$request->parent_prod_id;
            $success = false;

            if ($parent_prod_id > 0) {
                $saveGift = true;
                for ($i = 0; $i < $quantity; $i++) {
                    $productDeatils = $this->getProductDetailsById($product_idArr[$i]);
                    if (!empty($productDeatils)) {
                        $insertAry = array();
                        $insertAry['order_reference_id'] = $order_reference_id;
                        $insertAry['pre_order_id'] = $id_pre_order;
                        $insertAry['product_id'] = $product_idArr[$i];
                        $insertAry['dtCreatedOn'] = $createdOn;
                        $insertAry['quantity'] = 1;

                        if ($this->addProductInOrder($insertAry)) {
                            $success = true;
                        }

                    } else {
                        $response['status'] = "ERROR";
                        $response['errors'] = "No Product details found with given id.";
                    }
                }
                if ($newOrder) {
                    $saveGift = true;
                } else {
                    $getFreeGiftsArr1 = $this->getFreeOrdersProds($order_reference_id);
                    if (count($getFreeGiftsArr1) > 0) {
                        foreach ($getFreeGiftsArr1 as $freeGiftsProds) {
                            $this->updateFreeGiftsQuantity($freeGiftsProds, $order_reference_id, $quantity);
                        }
                    } else {
                        $saveGift = true;
                    }
                    if ($saveGift) {
                        $PreorderDetArr = $this->hire->getPreOrderDetails($order_reference_id);
                        if (count($PreorderDetArr) > 0) {
                            $offerProdId = Config::get('constants.PromotionProductId');
                            $hireDays = (int)$PreorderDetArr[0]->hire_days;
                            if ($hireDays > 3) {
                                $giftProdId = $this->hire->getGiftProds($hireDays);
                                if (count($giftProdId) > 0) {
                                    foreach ($giftProdId as $gift) {
                                        $CheckOfferAdded = $this->hire->getOrderProdDetailWithProdIdAndAmount($order_reference_id, $gift, 0.00);
                                        if (count($CheckOfferAdded) == 0) {
                                            $GiftAry = array();
                                            $GiftAry['order_reference_id'] = $order_reference_id;
                                            $GiftAry['pre_order_id'] = (int)$PreorderDetArr[0]->id;
                                            $GiftAry['product_id'] = $gift;
                                            $GiftAry['dtCreatedOn'] = $createdOn;
                                            $GiftAry['quantity'] = $quantity;
                                            $GiftAry['product_attributes'] = 'Free';
                                            $this->addProductInOrder($GiftAry);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $productDeatils = $this->getProductDetailsById($product_idArr[0]);
                if (!empty($productDeatils)) {
                    $insertAry = array();
                    $insertAry['order_reference_id'] = $order_reference_id;
                    $insertAry['pre_order_id'] = $id_pre_order;
                    $insertAry['product_id'] = $product_idArr[0];
                    $insertAry['dtCreatedOn'] = $createdOn;
                    $insertAry['quantity'] = $quantity;

                    if ($this->addProductInOrder($insertAry)) {
                        $success = true;
                    }

                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "No Product details found with given id.";
                }
            }


            if ($success == true) {
                if ($this->updateOrderTotalPrice($order_reference_id, $request)) {
                    $response['status'] = "SUCCESS";
                    $response['message'] = "Product Successfully added.";
                    $response['cartDetails'] = $this->getCartDetails($order_reference_id);
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Something is wrong. While we update order price.";
                }
            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Something is wrong. While we insert or update product into cart.";
            }

        }

        return $response;
    }

    public function checkOrderRefIdExist($orderRefId)
    {
        $orderRefIdArr = $orderKeys = DB::table($this->DBTables['Pre_Orders'])->where('order_reference_id', '=', $orderRefId)->select('id')->get();

        return $orderRefIdArr;
    }

    public function getFreeOrdersProds($orderRefId)
    {
        $GiftProdsArr = DB::table($this->DBTables['Pre_Orders_Products'])->where([['order_reference_id', '=', $orderRefId], ['product_attributes', '=', 'Free']])->select('product_id', 'quantity')->get();

        return $GiftProdsArr;
    }

    public function updateFreeGiftsQuantity($freeGiftsProds, $order_reference_id, $quantity)
    {
        $updatedQuantity = $freeGiftsProds->quantity + $quantity;
        DB::table($this->DBTables['Pre_Orders_Products'])
            ->where([['order_reference_id', '=', $order_reference_id], ['product_id', '=', $freeGiftsProds->product_id], ['sub_total_amnt', '=', 0]])
            ->update(['quantity' => (int)$updatedQuantity]);
        return true;
    }

    public function DecreaseFreeGiftsQuantity($freeGiftsProds, $order_reference_id)
    {
        $updatedQuantity = $freeGiftsProds->quantity - 1;
        DB::table($this->DBTables['Pre_Orders_Products'])
            ->where([['order_reference_id', '=', $order_reference_id], ['product_id', '=', $freeGiftsProds->product_id], ['sub_total_amnt', '=', 0]])
            ->update(['quantity' => (int)$updatedQuantity]);
        return true;
    }

    public function getProductDetailsById($idProduct = 0)
    {

        $DBTables = Config::get('constants.DbTables');

        if ((int)$idProduct > 0) {
            $ProdData = DB::table($DBTables['Products'])
                ->where('id', '=', $idProduct)
                ->select('name', 'description', 'sku', 'price', 'product_type', 'quantity')
                ->get();
            if (count($ProdData) > 0) {
                return $ProdData[0];
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    public function addProductInOrder($insertAry = array())
    {
        if (!empty($insertAry)) {
            $DBTables = Config::get('constants.DbTables');

            $id_pre_order_pro = DB::table($DBTables['Pre_Orders_Products'])->insertGetId($insertAry);

            return $id_pre_order_pro;
        } else {
            return false;
        }
    }

    public function updateOrderTotalPrice($order_reference_id = '', $request = '', $tss = false, $offerPercentage = 0, $tssPartner = '')
    {

        if (trim($order_reference_id) != '') {

            $tssDiscountPercentage = Config::get('constants.TssDiscount');
            $DBTables = Config::get('constants.DbTables');
            $paidItemsCount = 0;
            $totalProductAmount = 0;
            $offerAmount = 0;
            $shippingAmount = 0.00;
            $addedProductPriceAry = DB::table($DBTables['Pre_Orders_Products'])
                ->join($DBTables['Products'], $DBTables['Pre_Orders_Products'] . '.product_id', '=', $DBTables['Products'] . '.id')
                ->join($DBTables['Pre_Orders'], $DBTables['Pre_Orders'] . '.order_reference_id', '=', $DBTables['Pre_Orders_Products'] . '.order_reference_id')
                ->where($DBTables['Pre_Orders_Products'] . '.order_reference_id', '=', $order_reference_id)
                ->select(
                    $DBTables['Pre_Orders'] . '.hire_days',
                    $DBTables['Pre_Orders_Products'] . '.product_id',
                    $DBTables['Pre_Orders_Products'] . '.id',
                    $DBTables['Pre_Orders_Products'] . '.quantity',
                    $DBTables['Pre_Orders_Products'] . '.sub_total_amnt',
                    $DBTables['Pre_Orders_Products'] . '.product_attributes',
                    $DBTables['Products'] . '.price',
                    $DBTables['Products'] . '.product_type'
                )
                ->get();
            $totalSets = 0;
            if (count($addedProductPriceAry) > 0) {
                foreach ($addedProductPriceAry as $addedProductPrice) {
                    $sub_total_amnt = 0;
                    if ((int)$addedProductPrice->product_type == 5) {
                        //						$sub_total_amnt = $this->getProductRentPrice( $addedProductPrice->product_id, $addedProductPrice->hire_days );
                        $sub_total_amnt = $this->hire->getProductPriceByHireDays($addedProductPrice->hire_days);
                        $totalSets++;
                    } else {
                        if ($addedProductPrice->product_attributes != 'Free') {
                            $perUnitPrice = $addedProductPrice->price;
                            $totalPrice = $perUnitPrice * $addedProductPrice->quantity;
                            $sub_total_amnt = ($totalPrice + $sub_total_amnt);
                        }
                    }

                    DB::table($DBTables['Pre_Orders_Products'])
                        ->where('id', '=', $addedProductPrice->id)
                        ->update(['sub_total_amnt' => (float)$sub_total_amnt]);
                }
            }


            $productPrices = DB::table($DBTables['Pre_Orders_Products'])
                ->where('order_reference_id', '=', $order_reference_id)
                ->select('sub_total_amnt')
                ->get();

            $orderDetailsAry = DB::table($DBTables['Pre_Orders'])
                ->where('order_reference_id', '=', $order_reference_id)
                ->select(
                    'partner_ref_key',
                    'offer_id',
                    'insurance_amnt',
                    'shipping_tax_percnt',
                    'news_letter_signup'
                )
                ->get();
            if (count($orderDetailsAry) > 0) {
                $orderDetails = $orderDetailsAry[0];
            } else {
                $orderDetails = array();
            }

            if (count($productPrices) > 0) {
                foreach ($productPrices as $eachProPrice) {
                    $totalProductAmount = $totalProductAmount + $eachProPrice->sub_total_amnt;
                    if($eachProPrice->sub_total_amnt>0){
                        $paidItemsCount++;
                    }
                }
            }
            $totalProductAmount = $this->hire->getMultiSetDiscountedPrice($totalSets, $totalProductAmount);
            if ((int)$orderDetails->offer_id > 0 && !empty($orderDetails)) {
                $offerDetails = $this->checkOrderOfferValidity($orderDetails->offer_id, $order_reference_id);

                if (!empty($offerDetails)) {
                    if ((int)$offerDetails->offer_type == 1) {
                        $offerPercentage = $offerDetails->offer_percntg;
                        $offerPrice = (($totalProductAmount * $offerPercentage) / 100);
                        $offerAmount = $offerPrice;
                    } else {
                        $offerAmount = $offerDetails->offer_amnt;
                    }
                }
            }

            $newsLetterPrice = 0;
            if ((int)$orderDetails->news_letter_signup == 1 && !empty($orderDetails)) {
                $newsLetterPercentage = 10;
                $newsLetterPrice = (($totalProductAmount * $newsLetterPercentage) / 100);
                $updateOrderAmount['signup_discount_amnt'] = $newsLetterPrice;
            } else {
                $updateOrderAmount['signup_discount_amnt'] = 0;
            }

//			$shippingAmount = $this->getShippingAmount( $order_reference_id );

            $insuranceAmount = $orderDetails->insurance_amnt;
            /*$proAmountWithOffer = ( $totalProductAmount - $offerAmount );

			$proAmountWithNewsLetterDisc = ( $proAmountWithOffer - $newsLetterPrice );*/

//			$proAmountWithOffer = ( $totalProductAmount - $offerAmount );

            $proAmountWithNewsLetterDisc = ($totalProductAmount - $newsLetterPrice);
            $totalDiscountAmount = $proAmountWithNewsLetterDisc * $offerPercentage * 0.01;
            $proAmountWithNewsLetterDisc = ($proAmountWithNewsLetterDisc - ($totalDiscountAmount));
            $orderTotalAmount = $proAmountWithNewsLetterDisc + $insuranceAmount + $shippingAmount;

            if (count($orderDetails) > 0) {

                if ($this->utility->checkPartnerAlreadyLogin($request)) {
                    $updateOrderAmount['partner_ref_key'] = '';
                    $updateOrderAmount['banner_ref_key'] = '';
                    $updateOrderAmount['partner_discount_percnt'] = 10;
                    $updateOrderAmount['partner_discount_amnt'] = (($totalProductAmount * $updateOrderAmount['partner_discount_percnt']) / 100);
                    $updateOrderAmount['partner_cmsn_percnt'] = 0;
                    $updateOrderAmount['partner_cmsn_amt'] = 0;

                } else {

                    if ($request->cookie('TSS_PARTNER')) {
                        $partner_arr = array();
                        $partnerCookie = $request->cookie('TSS_PARTNER');
                        $partnerEncKey1 = "#17TssPC#77#";
                        $partnerEncKey2 = "#PCC2017#tSsPcC#";
                        $partnerEncKey3 = "#PaPTsSc#";

                        $decryptedC1 = base64_decode($partnerCookie);
                        $decryptedC2 = preg_replace("/$partnerEncKey1/", "", $decryptedC1);
                        $decryptedC3 = preg_replace("/$partnerEncKey2/", "", $decryptedC2);
                        $decryptedC4 = preg_replace("/$partnerEncKey3/", "", $decryptedC3);

                        list($partner_arr['partner_ref'], $partner_arr['banner_ref']) = explode("~", $decryptedC4);
                        if (!empty($partner_arr)) {
                            $partnerContants = Config::get('constants.PartnerConfig');
                            $updateOrderAmount['partner_ref_key'] = trim($partner_arr['partner_ref']);
                            $updateOrderAmount['banner_ref_key'] = trim($partner_arr['banner_ref']);
                            $updateOrderAmount['partner_cmsn_percnt'] = $partnerContants['CommissionPercentage'];
                            $updateOrderAmount['partner_cmsn_amt'] = ($totalProductAmount * $updateOrderAmount['partner_cmsn_percnt'] * 0.01);
                            $updateOrderAmount['partner_discount_percnt'] = $partnerContants['DiscountPercentage'];
                            $updateOrderAmount['partner_discount_amnt'] = ($totalProductAmount * $updateOrderAmount['partner_discount_percnt'] * 0.01);
                        }
                    } else {
                        $updateOrderAmount['partner_ref_key'] = '';
                        $updateOrderAmount['banner_ref_key'] = '';
                        $updateOrderAmount['partner_cmsn_percnt'] = 0;
                        $updateOrderAmount['partner_cmsn_amt'] = 0;
                        $updateOrderAmount['partner_discount_percnt'] = 0;
                        $updateOrderAmount['partner_discount_amnt'] = 0;
                    }

                }
                $orderTotalAmount = $orderTotalAmount - $updateOrderAmount['partner_discount_amnt'];
            }

            $tssDiscountAmount = ($totalProductAmount * $tssDiscountPercentage * 0.01);
            $updateOrderAmount['offer_amnt'] = ($offerAmount);
            $updateOrderAmount['tss'] = ($tssDiscountAmount);
            $updateOrderAmount['sub_total_amnt'] = (($totalProductAmount - $offerAmount - $tssDiscountAmount - $updateOrderAmount['partner_discount_amnt']) + $insuranceAmount + $shippingAmount);
            $request->pickup = $request->pickup_postal_code;
            $request->dropoff = $request->delvr_postal_code;
            $request->pickupState = $request->pickup_state_id;
            $request->dropoffState = $request->delvr_state_id;

                $updateOrderAmount['shipping_amnt'] = ($this->hire->calculateshipping($request)>0?$this->hire->calculateshipping($request):0)*$paidItemsCount;
                $orderTotalAmount = $orderTotalAmount + $request->shipping_amnt;
                $updateOrderAmount['total_amnt'] = $orderTotalAmount - $offerAmount - $tssDiscountAmount;

            DB::table($DBTables['Pre_Orders'])
                ->where('order_reference_id', '=', $order_reference_id)
                ->update($updateOrderAmount);

            return true;
        }
    }

    public function checkOrderOfferValidity($offer_id = 0, $order_reference_id = '')
    {
        $responseOfferAry = array();
        if ((int)$offer_id > 0 && trim($order_reference_id) != '') {
            $DBTables = Config::get('constants.DbTables');

            $offerDetails = DB::table($DBTables['Offers'])
                ->where('id', '=', $offer_id)->get();

            if (count($offerDetails) > 0) {
                $offerAry = $offerDetails[0];
            } else {
                $offerAry = array();
            }
            $responseOfferAry = array();
            if (!empty($offerAry)) {
                $dtNow = date('Y-m-d H:i:s');
                $offerFrom = date('Y-m-d H:i:s', strtotime($offerAry->dt_from));
                $offerUpto = date('Y-m-d H:i:s', strtotime($offerAry->dt_upto));

                $updateOfferAry = array();
                $validOffer = false;
                if ((strtotime($offerFrom) <= strtotime($dtNow)) && (strtotime($offerUpto) > strtotime($dtNow))) {
                    $updateOfferAry['offer_Code'] = $offerAry->szCoupnCode;
                    $updateOfferAry['offer_type'] = $offerAry->offer_type;
                    $updateOfferAry['offer_percntg'] = $offerAry->offer_percntg;
                    $updateOfferAry['offer_amnt'] = (float)$offerAry->offer_amnt;
                    $validOffer = true;
                } else {
                    $updateOfferAry['offer_id'] = 0;
                    $updateOfferAry['offer_Code'] = '';
                    $updateOfferAry['offer_type'] = '';
                    $updateOfferAry['offer_percntg'] = '';
                    $updateOfferAry['offer_amnt'] = '';
                }

                DB::table($DBTables['Pre_Orders'])
                    ->where('order_reference_id', trim($order_reference_id))
                    ->update($updateOfferAry);

                if ($validOffer == true) {
                    $responseOfferAry = $offerAry;
                }
            }

        }
        return $responseOfferAry;
    }

    public function getCartDetails($order_reference_id = '')
    {
        $response = array();
        $DBTables = Config::get('constants.DbTables');

//        $this->checkProductAvailabiltyUpdatePrice($order_reference_id);

        if (trim($order_reference_id) != '') {
            $orderDetailsAry = DB::table($DBTables['Pre_Orders'])
                ->where('order_reference_id', '=', $order_reference_id)
                ->select('order_reference_id',
                    'dt_book_from',
                    'dt_book_upto',
                    'offer_Code',
                    'sub_total_amnt',
                    'offer_amnt',
                    'shipping_tax_percnt',
                    'shipping_amnt',
                    'insurance_amnt',
                    'news_letter_signup',
                    'signup_discount_amnt',
                    'total_amnt'
                )
                ->get();

            $parentProductAry = DB::table($DBTables['Pre_Orders_Products'])
                ->join($DBTables['Group_Products'], $DBTables['Group_Products'] . '.product_id', '=', $DBTables['Pre_Orders_Products'] . '.product_id')
                ->join($DBTables['Products'], $DBTables['Products'] . '.id', '=', $DBTables['Group_Products'] . '.parent_productid')
                ->where($DBTables['Pre_Orders_Products'] . '.order_reference_id', '=', $order_reference_id)
                ->select($DBTables['Products'] . '.name', $DBTables['Products'] . '.id as idParent')
                ->groupBy($DBTables['Group_Products'] . '.parent_productid')
                ->get();

            $productAry = array();
            if (count($parentProductAry) > 0) {
                $count = 0;
                foreach ($parentProductAry as $i => $parentProduct) {

//                    $childProductAry = DB::table( $DBTables['Pre_Orders_Products'] )
//                                        ->join( $DBTables['Group_Products'], $DBTables['Group_Products'].'.product_id', '=', $DBTables['Pre_Orders_Products'].'.product_id')
//                                        ->where( $DBTables['Group_Products'].'.parent_productid', '=', $parentProduct->idParent )
//                                        ->select( $DBTables['Pre_Orders_Products'].'product_id','COUNT('.$DBTables['Pre_Orders_Products'].'.id) as count' )
//                                        ->groupBy( $DBTables['Pre_Orders_Products'].'dtCreatedOn' )
//                                        ->get();
//
//                    if(count($childProductAry) >0){
//                        foreach($childProductAry as $childProducts){
//                            $productAry[$count]['name'] = $parentProduct->name;
//                            $productAry[$count]['quantity'] = $childProducts->count;
//                            $productAry[$count]->attributeAry = $this->getProductAttribute($childProducts->product_id);
//                            $productAry[$count]->isAvailable = $this->checkProductAvailabilty($parentProduct->idParent,$orderDetailsAry[0]->dt_book_from,$orderDetailsAry[0]->dt_book_upto,4,$productAry[$count]->attributeAry);
//
//                            $count++;
//                        }
//                    }

                }
            }
            $response['orderDetails'] = $orderDetailsAry;
            $response['productAry'] = $productAry;
        }

        return $response;
    }

    public function getShippingAmount($order_reference_id = '')
    {
        $DBTables = Config::get('constants.DbTables');
        $shippingAmount = 35.50;
        DB::table($DBTables['Pre_Orders'])
            ->where('order_reference_id', trim($order_reference_id))
            ->update(['shipping_amnt' => (float)$shippingAmount]);


        return $shippingAmount;
    }

    public function getProductRentPrice($product_id = 0, $hire_days = 0)
    {

        $sub_total_amnt = 0;
        if ((int)$product_id > 0 && (int)$hire_days > 0) {

            $DBTables = Config::get('constants.DbTables');

            $setId = DB::table($DBTables['Set_Pro_Map'])
                ->where('product_id', '=', $product_id)
                ->select('set_id')
                ->get();

            if (count($setId) > 0) {

                $set_id = (int)$setId[0]->set_id;

                if ((int)$set_id > 0) {
                    $day_rent = DB::table($DBTables['Rent'])
                        ->where('set_id', '=', $set_id)
                        ->where('days', '=', $hire_days)
                        ->select('rent')
                        ->get();
                    if (count($day_rent) > 0) {
                        $perUnitPrice = number_format($day_rent[0]->rent, 0);
                        $totalPrice = $perUnitPrice;
                        $sub_total_amnt = (float)$totalPrice;

                        return $sub_total_amnt;

                    } else {
                        $max_day_rent = DB::table($DBTables['Rent'])
                            ->where('set_id', '=', $set_id)
                            ->where('days', '<', $hire_days)
                            ->select(DB::raw('MAX(rent) as max_rent', 'MAX(days) as max_days'))
                            ->get();

                        if (count($max_day_rent) > 0) {
                            $day_count = (int)$max_day_rent[0]->max_days;
                            $previousDayRent = number_format($max_day_rent[0]->max_rent, 0);

                            $totalRent = $previousDayRent;
                            for ($i = $day_count; $i < $hire_days; $i++) {
                                $totalRent = $totalRent + 10;
                            }

                            $perUnitPrice = number_format($totalRent, 0);
                            $totalPrice = $perUnitPrice;
                            $sub_total_amnt = (float)$totalPrice;
                        }
                    }
                }
            }
        }

        return $sub_total_amnt;
    }

    public function addRemoveOrderInsurance(Request $request)
    {
        $orderData = $request->input();
        $response = array();
        $rules = array(
            'addInsurance' => 'required',
            'order_reference_id' => 'required'
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "ERROR";
            $response['errors'] = $this->formatValidationErrors($validator);
        } else {

            $DBTables = Config::get('constants.DbTables');

            $insuranceFlag = trim($orderData['addInsurance']);
            $order_reference_id = trim($orderData['order_reference_id']);

            if (trim($insuranceFlag) != '' && trim($order_reference_id) != '') {
                if (trim($insuranceFlag) == "NO") {
                    $insuranceAmount = 00.00;
                } else {
                    $insuranceAmount = 50.00;
                }

                DB::table($DBTables['Pre_Orders'])
                    ->where('order_reference_id', trim($order_reference_id))
                    ->update(['insurance_amnt' => (float)$insuranceAmount]);

                if ($this->updateOrderTotalPrice($order_reference_id, $request)) {
                    $response['status'] = "SUCCESS";
                    $response['message'] = "Order insurance successfully updated.";
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Something is wrong. While we update order price.";
                }
            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid values.";
            }
        }

        return $response;
    }

    public function applyOrderOffer(Request $request)
    {
        $orderData = $request->input();
        $response = array();
        $rules = array(
            'offerCode' => 'required',
            'order_reference_id' => 'required'
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "ERROR";
            $response['errors'] = $this->formatValidationErrors($validator);
        } else {

            $offerCode = trim($orderData['offerCode']);
            $order_reference_id = trim($orderData['order_reference_id']);

            if (trim($offerCode) != '' && trim($order_reference_id) != '') {
                $DBTables = Config::get('constants.DbTables');

                $offerDetails = DB::table($DBTables['Offers'])
                    ->where('szCoupnCode', '=', $offerCode)->get();

                if (count($offerDetails) > 0) {
                    $dtNow = date('Y-m-d');
                    $offerFrom = date('Y-m-d', strtotime($offerDetails[0]->dt_from));
                    $offerUpto = date('Y-m-d', strtotime($offerDetails[0]->dt_upto));

                    if ((strtotime($offerFrom) <= strtotime($dtNow)) && (strtotime($offerUpto) > strtotime($dtNow))) {

                        $isOneTime = (int)$offerDetails[0]->isOneTimeOffer;
                        $validUser = true;

                        if ((int)$isOneTime == 1) {

                            $validUserResponse = $this->checkValidOfferUser($order_reference_id);

                            if (trim($validUserResponse['status']) == 'ERROR') {
                                $validUser = false;
                                $response['status'] = "ERROR";
                                $response['errors'] = $validUserResponse['errors'];
                            }
                        }

                        if ($validUser == true) {
                            $offerUpdateAry = array();
                            $offerUpdateAry['offer_Code'] = $offerDetails[0]->szCoupnCode;
                            $offerUpdateAry['offer_id'] = $offerDetails[0]->id;
                            $offerUpdateAry['offer_type'] = $offerDetails[0]->offer_type;
                            $offerUpdateAry['offer_percntg'] = $offerDetails[0]->offer_percntg;
                            $offerUpdateAry['offer_amnt'] = $offerDetails[0]->offer_amnt;

                            DB::table($DBTables['Pre_Orders'])
                                ->where('order_reference_id', trim($order_reference_id))
                                ->update($offerUpdateAry);

                            if ($this->updateOrderTotalPrice($order_reference_id, $request)) {
                                $response['status'] = "SUCCESS";
                                $response['message'] = "Coupon code successfully applied.";
                            } else {
                                $response['status'] = "ERROR";
                                $response['errors'] = "Something is wrong. While we update order price.";
                            }

                        } else {
                            $response['status'] = "ERROR";
                            $response['errors'] = "This coupon code is not valid for this account.";
                        }

                    } else {
                        $response['status'] = "ERROR";
                        $response['errors'] = "This coupon code is expired.";
                    }

                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Invalid coupon code.";
                }

            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid values.";
            }
        }

        return $response;
    }

    public function checkValidOfferUser($order_reference_id = '')
    {

        $response = array();

        if (trim($order_reference_id) != '') {
            $DBTables = Config::get('constants.DbTables');

            $pre_order = DB::table($DBTables['Pre_Orders'])
                ->where('order_reference_id', '=', trim($order_reference_id))
                ->select('buyer_email')
                ->get();

            if (count($pre_order) > 0) {
                $buyerEmail = trim($pre_order[0]->buyer_email);
                if (trim($buyerEmail) != '') {
                    $old_order = DB::table($DBTables['Orders'])
                        ->where('user_email', '=', trim($buyerEmail))
                        ->select('id')
                        ->get();

                    if (count($old_order) > 0) {
                        $response['status'] = "ERROR";
                        $response['errors'] = "This is one time offer and not valid for exiting customer.";
                    } else {
                        $response['status'] = "SUCCESS";
                    }
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Buyer email not exist.Please add buyer eamil before apply coupon code.";
                }
            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid order reference id.";
            }

        } else {
            $response['status'] = "ERROR";
            $response['errors'] = "Order reference id is required.";
        }

        return $response;
    }

    public function removeOfferFromOrder(Request $request)
    {
        $response = array();
        $orderData = $request->input();
        $rules = array(
            'order_reference_id' => 'required'
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "ERROR";
            $response['errors'] = $this->formatValidationErrors($validator);
        } else {
            $order_reference_id = trim($orderData['order_reference_id']);

            if (trim($order_reference_id) != '') {
                $DBTables = Config::get('constants.DbTables');
                $offerUpdateAry = array();
                $offerUpdateAry['offer_Code'] = '';
                $offerUpdateAry['offer_id'] = 0;
                $offerUpdateAry['offer_type'] = 0;
                $offerUpdateAry['offer_percntg'] = 0;
                $offerUpdateAry['offer_amnt'] = 0;
                DB::table($DBTables['Pre_Orders'])
                    ->where('order_reference_id', trim($order_reference_id))
                    ->update($offerUpdateAry);

                if ($this->updateOrderTotalPrice($order_reference_id, $request)) {
                    $response['status'] = "SUCCESS";
                    $response['message'] = "Coupon code successfully removed.";
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Something is wrong. While we update order price.";
                }

            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid values.";
            }
        }

        return $response;
    }

    public function signinForNewsLetter(Request $request)
    {
        $response = array();
        $orderData = $request->input();
        $rules = array(
            'order_reference_id' => 'required'
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "ERROR";
            $response['errors'] = $this->formatValidationErrors($validator);
        } else {
            $order_reference_id = trim($orderData['order_reference_id']);

            if (trim($order_reference_id) != '') {
                $DBTables = Config::get('constants.DbTables');

                $pre_order = DB::table($DBTables['Pre_Orders'])
                    ->where('order_reference_id', '=', trim($order_reference_id))
                    ->select('buyer_email', 'buyer_first_name', 'buyer_last_name')
                    ->get();

                if (count($pre_order) > 0) {
                    $buyerEmail = trim($pre_order[0]->buyer_email);
                    if (trim($buyerEmail) != '') {
                        $old_subscription = DB::table($DBTables['Newsletter'])
                            ->where('email', '=', trim($buyerEmail))
                            ->select('id')
                            ->get();

                        if (count($old_subscription) > 0) {
                            $response['status'] = "ERROR";
                            $response['errors'] = "You are already subscribed for news letter.";
                        } else {
                            DB::table($DBTables['Pre_Orders'])
                                ->where('order_reference_id', trim($order_reference_id))
                                ->update(['news_letter_signup' => '1']);

                            if ($this->updateOrderTotalPrice($order_reference_id, $request)) {
                                $response['status'] = "SUCCESS";
                                $response['message'] = "You are successfully signup for news letter subscription..";
                            } else {
                                $response['status'] = "ERROR";
                                $response['errors'] = "Something is wrong. While we update newsletter subscription.";
                            }

                        }
                    } else {
                        $response['status'] = "ERROR";
                        $response['errors'] = "Buyer email not exist.Please add buyer eamil for signup subscription.";
                    }
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Invalid order reference id.";
                }


            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid values.";
            }
        }
    }

    public function removeNewsSignupFromOrder(Request $request)
    {
        $response = array();
        $orderData = $request->input();
        $rules = array(
            'order_reference_id' => 'required'
        );

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "ERROR";
            $response['errors'] = $this->formatValidationErrors($validator);
        } else {
            $order_reference_id = trim($orderData['order_reference_id']);

            if (trim($order_reference_id) != '') {
                $DBTables = Config::get('constants.DbTables');

                DB::table($DBTables['Pre_Orders'])
                    ->where('order_reference_id', trim($order_reference_id))
                    ->update(['news_letter_signup' => '0']);

                if ($this->updateOrderTotalPrice($order_reference_id, $request)) {
                    $response['status'] = "SUCCESS";
                    $response['message'] = "Newsletter subscription successfully removed from order.";
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Something is wrong. While we update order price.";
                }

            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid values.";
            }
        }

        return $response;
    }

    public function shipping()
    {
        $this->hire->setLang();
        $page1 = session()->get('page1');
        $page2 = session()->get('page2');
        if (isset($page1) && isset($page2) && isset($_COOKIE['order_reference_id']) && $page1 == '1' && $page2 == '1' && $_COOKIE['order_reference_id'] !== null) {
            session()->put('page3', '1');
            View::share('filter', 'payment');
            View::share('title', 'Delivery &amp; Payment');
            View::share('PageHeading', 'Delivery &amp; Payments');
            View::share('PageDescription1', 'Select where you want to have your clubs picked up and dropped off from. We deliver FREE to Metropolitan Melbourne.');
            View::share('PageDescription2', '');
//		$order_reference_id = session()->get( 'order_reference_id' );
            $order_reference_id = $_COOKIE['order_reference_id'];
            $countriesAry = $this->utility->getCountriesList();
            $statesAry = $this->utility->getStatesList();

            if (trim($order_reference_id) != '') {
                $DBTables = Config::get('constants.DbTables');
                /*$PreorderDetArr = $this->hire->getPreOrderDetails( $_COOKIE['order_reference_id'] );
                $createdOn      = date( 'Y-m-d H:i:s' );
                if ( count( $PreorderDetArr ) > 0 ) {
                    $offerProdId     = Config::get( 'constants.PromotionProductId' );
                    $hireDays        = (int) $PreorderDetArr[0]->hire_days;
                    if ( $hireDays > 7 ) {
                        $CheckOfferAdded = $this->hire->getOrderProdDetailWithProdIdAndAmount( $_COOKIE['order_reference_id'], $offerProdId, 0.00 );
                        if(count($CheckOfferAdded) == 0){
                            $insertAry                       = array();
                            $insertAry['order_reference_id'] = $_COOKIE['order_reference_id'];
                            $insertAry['pre_order_id']       = (int) $PreorderDetArr[0]->id;
                            $insertAry['product_id']         = $offerProdId;
                            $insertAry['dtCreatedOn']        = $createdOn;
                            $insertAry['quantity']           = 1;
                            $this->addProductInOrder( $insertAry );
                        }
                    }
                }*/
                $orderDetailsAry = DB::table($DBTables['Pre_Orders'])
                    ->where('order_reference_id', '=', $order_reference_id)
                    ->select('order_reference_id',
                        'dt_book_from',
                        'dt_book_upto',
                        'offer_Code',
                        'sub_total_amnt',
                        'offer_amnt',
                        'shipping_tax_percnt',
                        'shipping_amnt',
                        'insurance_amnt',
                        'news_letter_signup',
                        'signup_discount_amnt',
                        'total_amnt',
                        'tss'
                    )
                    ->get();

                if (count($orderDetailsAry) > 0) {
                    $orderDetails = $orderDetailsAry[0];
                    $cartDetailArr = $this->hire->getCartByRefId($_COOKIE['order_reference_id']);
                    $cartDetailArr = $this->hire->getCart($cartDetailArr);
                    $preOrderArr = $this->hire->getPreOrderDetails($_COOKIE['order_reference_id']);

                    $insurance = 0;
                    if (!empty($preOrderArr)) {
                        $insurance = $preOrderArr[0]->insurance_amnt;
                    }

                    $setCount = $this->hire->getCartSetCount($_COOKIE['order_reference_id']);
                    return view('pages.frontend.shipping', compact('orderDetails', 'countriesAry', 'statesAry', 'cartDetailArr', 'insurance', 'setCount'));
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Invalid order reference id.";
                }
            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Order reference id not found.";
            }
        } else {
            return redirect()->to('/');
        }

    }

    public function updateShippingAndPayment(Request $request)
    {
        $response = array();
        Input::merge(array_map('trim', Input::all()));
        $allInput = $request->input();
        $rules = array(
            'order_reference_id' => 'required',
            'dropoff_place' => 'required | numeric | min:1',
            'delvr_hotel_name' => 'required',
            'delvr_address' => 'required',
            'delvr_state_id' => 'required | numeric | min:1',
            'delvr_postal_code' => 'required',
            'pickup_place' => 'required | numeric | min:1',
            'pickup_hotel_name' => 'required',
            'pickup_address' => 'required',
            'pickup_state_id' => 'required | numeric | min:1',
            'pickup_postal_code' => 'required',
            'buyer_first_name' => 'required',
            'buyer_last_name' => 'required',
            'buyer_email' => 'required',
            'buyer_confirm_email' => 'required | same:buyer_email',
            'buyer_country' => 'required | numeric | min:1',
            'suburb' => 'required',
            'suburbpickup' => 'required',
            'phone_no_aus' => 'required',
            'iTerms' => 'required|min:1',
            'here_abt_us' => 'required',
        );
        $tssDiscount = false;
        if (isset($allInput['tss']) && $allInput['tss'] == 1) {
            $StatusArr = $this->hire->checkTssSubscription($allInput['buyer_email']);
            if (count($StatusArr) == 0) {
                Newsletter::subscribe($allInput['buyer_email']);
                $this->hire->SubscribeToTss($allInput['buyer_email']);
                $tssDiscount = true;
            } elseif ((strtotime($StatusArr[0]->created_at) <= strtotime(date('Y-m-d H:i:s'))) && (strtotime($StatusArr[0]->created_at) > strtotime(date('Y-m-d H:i:s', strtotime('-90 days'))))) {
                $tssDiscount = true;
            }

        }

        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->to($this->getRedirectUrl())
                ->withInput($request->input())
                ->withErrors($this->formatValidationErrors($validator), $this->errorBag());
        } else {
            $order_reference_id = trim($allInput['order_reference_id']);
            $offerCodeArr = $this->hire->getOfferDetails(date('Y-m-d'), $allInput['offer_code']);
            $allInput['offer_percntg'] = 0;
            $allInput['offer_amnt'] = 0;
            if (count($offerCodeArr) > 0) {
                $CheckOfferAppliedArr = $this->hire->checkOfferAppliedInPreOrder($allInput['offer_code'], $order_reference_id);
                if (count($CheckOfferAppliedArr) == 0) {
                    $this->hire->updateOfferCode($order_reference_id, $offerCodeArr[0]->id, $offerCodeArr[0]->szCoupnCode, $offerCodeArr[0]->offer_type, $offerCodeArr[0]->offer_percntg, $offerCodeArr[0]->offer_amnt);
                    if($offerCodeArr[0]->offer_type == '1'){
                        $allInput['offer_percntg'] = $offerCodeArr[0]->offer_percntg;
                    }else{
                        $allInput['offer_amnt'] = $offerCodeArr[0]->offer_amnt;
                    }
                    $allInput['offer_id'] = $offerCodeArr[0]->id;
                    $allInput['offer_Code'] = $offerCodeArr[0]->szCoupnCode;
                    $allInput['offer_type'] = $offerCodeArr[0]->offer_type;
                }
            }
            if (trim($order_reference_id) != '') {
                $DBTables = Config::get('constants.DbTables');
                unset($allInput['order_reference_id']);
                unset($allInput['buyer_confirm_email']);
                unset($allInput['iTerms']);
                unset($allInput['_token']);

                DB::table($DBTables['Pre_Orders'])
                    ->where('order_reference_id', trim($order_reference_id))
                    ->update($allInput);

                if ($this->updateOrderTotalPrice($order_reference_id, $request, $tssDiscount, $allInput['offer_percntg'])) {
                    return redirect()->to('/order-preview');
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Something is wrong. While we update order price.";
                }


            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid values.";
            }
        }

        return $response;
    }

    public function checkProductAvailabilty($parent_product_id = 0, $dt_book_from = '', $dt_book_upto = '', $extended_days = 4, $filterArr = array())
    {
        $servicingDays = Config::get('constants.stateServicingDays');
        $extended_days = $servicingDays[session()->get('states')];
        $CheckProdArr = $this->hire->getAvailChildProds($parent_product_id, $dt_book_from, $dt_book_upto, $extended_days, $filterArr);
        $isAvailable = 0;
        if (empty($CheckProdArr[0])) {
            $isAvailable = 1;
        }

        return $isAvailable;
    }

    public function removeProductFromCart(Request $request)
    {
        $response = array();
        Input::merge(array_map('trim', Input::except('product_idArr')));
        $allInput = $request->input();
        $rules = array(
            'order_reference_id' => 'required',
            'product_idArr' => 'required'
        );
        $validator = $this->getValidationFactory()->make($request->all(), $rules);

        if ($validator->fails()) {
            $response['status'] = "ERROR";
            $response['errors'] = $this->formatValidationErrors($validator);
        } else {
            $order_reference_id = trim($allInput['order_reference_id']);
            $product_idArr = $request->product_idArr;

            if (trim($order_reference_id) != '') {
                $DBTables = Config::get('constants.DbTables');
                /* print_r($product_idArr);
				 die;*/
                $delGift = false;
                if (!empty($product_idArr)) {
                    foreach ($product_idArr as $key => $product_id) {
                        $CheckProdType = $this->getOrderProductDetJoinWithProd($product_id, $order_reference_id);
                        DB::table($DBTables['Pre_Orders_Products'])
                            ->where('order_reference_id', '=', $order_reference_id)
                            ->where('product_id', '=', (int)$product_id)
                            ->delete();
                        if (count($CheckProdType) > 0) {
                            if ($CheckProdType[0]->product_type == 5) {
                                $delGift = true;
                            }
                        }
                        if ($delGift) {
                            $getFreeGiftsArr = $this->getFreeOrdersProds($order_reference_id);
                            if (count($getFreeGiftsArr) > 0) {
                                foreach ($getFreeGiftsArr as $freeGiftsProds) {
                                    if ($freeGiftsProds->quantity > 1) {
                                        $this->DecreaseFreeGiftsQuantity($freeGiftsProds, $order_reference_id);
                                    } elseif ($freeGiftsProds->quantity == 1) {
                                        DB::table($DBTables['Pre_Orders_Products'])
                                            ->where('order_reference_id', '=', $order_reference_id)
                                            ->where('product_id', '=', (int)$freeGiftsProds->product_id)
                                            ->where('sub_total_amnt', '=', 0)
                                            ->delete();
                                    }
                                }
                            }
                        }
                    }
                }

                if ($this->updateOrderTotalPrice($order_reference_id, $request)) {
                    $response['dataarr'] = $this->hire->getPreOrderDetails($order_reference_id);
                    $response['status'] = "SUCCESS";
                    $response['message'] = "Product successfully removed from cart.";
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Something is wrong. While we update order price.";
                }

            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid request.";
            }
        }

        return $response;
    }

    public function getOrderProductDetJoinWithProd($prodId, $order_reference_id)
    {
        $productAry = DB::table($this->DBTables ['Pre_Orders_Products'])
            ->join($this->DBTables ['Products'], $this->DBTables ['Products'] . '.id', '=', $this->DBTables ['Pre_Orders_Products'] . '.product_id')
            ->where([[$this->DBTables ['Pre_Orders_Products'] . '.order_reference_id', '=', $order_reference_id], [$this->DBTables ['Pre_Orders_Products'] . '.product_id', '=', $prodId]])
            ->select($this->DBTables ['Pre_Orders_Products'] . '.*',
                $this->DBTables ['Products'] . '.name',
                $this->DBTables ['Products'] . '.description',
                $this->DBTables ['Products'] . '.sku',
                $this->DBTables ['Products'] . '.product_type'
            )
            ->get();
        return $productAry;
    }

    public function orderPreview()
    {
        $this->hire->setLang();
        $page1 = session()->get('page1');
        $page2 = session()->get('page2');
        $page3 = session()->get('page3');
        if (isset($page1) && isset($page2) && isset($page3) && isset($_COOKIE['order_reference_id']) && $page1 == '1' && $page2 == '1' && $page3 == '1' && $_COOKIE['order_reference_id'] !== null) {
            View::share('filter', 'payment');
            View::share('title', 'Preview Order');
            View::share('Page', 'order-preview');
            View::share('PageHeading', 'Preview Order');
            View::share('PageDescription1', 'At The Sweet Spot Club Hire, we offer the latest to market clubs from the leading brands – Callaway and TaylorMade. We have designed our sets to cater for all levels of golfer, whether it be someone playing from scratch or someone just starting out. Hit the Sweet Spot with your next hire!');
            View::share('PageDescription2', '');
            $siteUrl = Config::get('constants.SiteUrl');
            $supportEmail = Config::get('constants.supportEmailLocal');
            $paymentSwitch = Config::get('constants.PaymentSwitch');
            $PaydollarConfig = Config::get('constants.PaydollarConfig');
            $NabConfig = Config::get('constants.NAB-Transact-Config');
            $order_reference_id = $_COOKIE['order_reference_id'];
            if (trim($order_reference_id) != '') {
                $cartDetailArr = $this->hire->getCartByRefId($_COOKIE['order_reference_id']);
                $cartDetailArr = $this->hire->getCart($cartDetailArr);
                $preOrderArr = $this->hire->getPreOrderDetails($_COOKIE['order_reference_id']);
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
                $Pickupstates = $PickupstatesAry[0]->name;

                $setCount = $this->hire->getCartSetCount($_COOKIE['order_reference_id']);
                return view('pages.frontend.order_preview', compact('orderDetails', 'country', 'Deliverystates', 'Pickupstates', 'cartDetailArr', 'insurance', 'siteUrl', 'PaydollarConfig', 'NabConfig', 'setCount', 'supportEmail', 'paymentSwitch'));

            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Order reference id not found.";
            }
        } else {
            return redirect()->to('/');
        }
    }

    public function thank_you(Mail $mandrill, Request $request)
    {
        if (isset($request->idStatus) && $request->idStatus == '0') {
            $idOrder = (int)$request->idOrder;
            $updateAry = array();
            $updateAry['payment_in_progress'] = 0;
            DB::table($this->DBTables['Pre_Orders'])->where('order_reference_id', $idOrder)->update($updateAry);
            return redirect()->to("/disputed_orders")
                ->with('success', 'Order cancelled successfully.');
        } else {
            $supportEmail = Config::get('constants.customerSupportEmail');
            $parm = Input::get();
            $PaymentRefId = (isset($parm['Ref']) ? $parm['Ref'] : (isset($parm['payment_reference']) ? $parm['payment_reference'] : (isset($request->idOrder) ? $request->idOrder : '')));
            View::share('title', 'Thank You!');
            View::share('PageDescription1', 'Thank you for hiring from the Sweet Spot Club Hire! We hope you enjoy your clubs and they help deliver you a great round or golf trip.');
            View::share('PageDescription2', 'We will aim to provide the best quality service for you and will be there for whatever you need along the way. Feel free to contact us via email at info@tssclubhire or via our social media pages @tssclubhire if you have any questions or concerns. We will be sending you information in regards to your hire closer to your initial hire date, with all the details you will require. Thank you again for choosing The Sweet Spot and we hope your next golf experience is a great one.');
            $FirstTime = '0';
            $discountErr = '0';
            if (!empty($PaymentRefId)) {
                $checkOrderExistArr = $this->checkOrderByPaymentRefId($PaymentRefId);
                if (count($checkOrderExistArr) > 0) {
                    $checkOrderExist = $checkOrderExistArr[0];
                    return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr'));
                } else {
                    $FirstTime = '1';
                    $xero = App::make('XeroPrivate');
                    $contact = App::make('XeroContact');
                    $invoice = App::make('XeroInvoice');
                    $tssDiscountPercentage = Config::get('constants.TssDiscount');
                    $DiscountList = Config::get('constants.Discount');
                    $order_reference_id = (isset($_COOKIE['order_reference_id']) && !empty($_COOKIE['order_reference_id']) ? $_COOKIE['order_reference_id'] : (isset($request->idOrder) ? $request->idOrder : ''));
//                $order_reference_id = '1513379759';
                    $PaydollarConfig = Config::get('constants.PaydollarConfig');
                    $orderDetailsAry = $this->hire->getPreOrderDetails($order_reference_id);
                    if (count($orderDetailsAry) > 0) {
                        $orderDetails = $orderDetailsAry[0];
                        $contact->setContactStatus('ACTIVE');
                        $contact->setName($orderDetails->buyer_first_name . ' ' . $orderDetails->buyer_last_name);
                        $contact->setFirstName($orderDetails->buyer_first_name);
                        $contact->setLastName($orderDetails->buyer_last_name);
                        $contact->setEmailAddress($orderDetails->buyer_email);
                        $contact->setDefaultCurrency('AUD');
                        $invoice->setContact($contact);
                        $invoice->setType('ACCREC');
                        $invoice->setDate(Carbon::now());
                        $invoice->setDueDate(Carbon::now()->addDays(4));
                        $invoice->setLineAmountType('Exclusive');
                        $invoice->setStatus('AUTHORISED');
                        $PreOrderProductsArr = $this->hire->getCartByRefId($order_reference_id, array(), $orderDetails->state_id);
                        $setCount = $this->hire->getCartSetCount($order_reference_id);
                        if (count($PreOrderProductsArr) > 0) {
                            $i = 0;
                            $discountFlag = true;
                            $disCount = 0;
                            foreach ($PreOrderProductsArr as $ProdArrkey => $OrderProducts) {

                                $line[$i] = App::make('XeroInvoiceLine');
                                if ($discountFlag) {
                                    /*if ($OrderProducts['setcount'] > 1 && $OrderProducts['setcount'] < 11) {
                                        $disCount = $DiscountList[$OrderProducts['setcount']];
                                    } elseif ($OrderProducts['setcount'] > 10) {
                                        $disCount = $DiscountList['11'];
                                    }
                                    if ($orderDetails->tss > 0) {
                                        $disCount = $disCount + $tssDiscountPercentage;
                                    }*/
                                    if ($setCount > 1 && $setCount < 11) {
                                        $disCount = $DiscountList[$setCount];
                                    } elseif ($setCount > 10) {
                                        $disCount = $DiscountList['11'];
                                    }
                                    if ($orderDetails->tss > 0) {
                                        $disCount = $disCount + $tssDiscountPercentage;
                                    }

                                    $discountFlag = false;
                                }
                                if ($disCount > 100) {
                                    $discountErr = '1';
                                    if (isset($request->adminFlag) && $request->adminFlag == '1') {
                                        return redirect()->to('/disputed_orders')
                                            ->with('success', 'Order created successfully');
                                    } else {
                                        return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr'));
                                    }
                                } else {
                                    $line[$i]->setDescription(strip_tags($OrderProducts['prod-description']));
                                    $line[$i]->setQuantity($OrderProducts['quantity']);
                                    $line[$i]->setUnitAmount($OrderProducts['price']);
                                    $line[$i]->setAccountCode(230);
                                    $line[$i]->setTaxType('NONE');
                                    $line[$i]->setDiscountRate($disCount);
                                    $invoice->addLineItem($line[$i]);
                                    $i++;
                                }
                            }
                            if ($orderDetailsAry[0]->offer_applied == 1) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Offer Code Applied - "' . $orderDetailsAry[0]->offer_Code . '"');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount('-' . $orderDetails->offer_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if ($orderDetailsAry[0]->insurance_amnt > 0) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Hire club set insurance fees.');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount($orderDetails->insurance_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if ($orderDetailsAry[0]->partner_discount_amnt > 0) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Partner Discount');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount(-$orderDetails->partner_discount_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            $line[$i] = App::make('XeroInvoiceLine');
                            $line[$i]->setDescription('Handling / Delivery Fee');
                            $line[$i]->setQuantity(1);
                            $line[$i]->setUnitAmount($orderDetails->shipping_amnt);
                            $line[$i]->setAccountCode(230);
                            $line[$i]->setTaxType('NONE');
                            $line[$i]->setDiscountRate(0);
                            $invoice->addLineItem($line[$i]);
                        }
                        $xero->save($invoice);
                        $invoicesArr = XeroPrivate::load('Accounting\\Invoice')->execute();
                        $totalInvoices = count($invoicesArr);
                        $line[$i]->setAccountCode('SALES');
                        $this->hire->updateInvoiceNo($invoicesArr[$totalInvoices - 1]->InvoiceNumber, $order_reference_id, $PaymentRefId);
                        /*Template Data For Mandrill START*/
                        $cartDetailArr = $this->hire->getCartByRefId($order_reference_id, array(), $orderDetails->state_id);
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
                        $Pickupstates = $PickupstatesAry[0]->name;

                        $templateData = '<table cellspacing="1" cellpadding="4" border="1">
				<thead>
				<tr>
					<th align="left">YOUR ORDER : ' . date('jS F Y', strtotime($orderDetails->dt_book_from)) . ' - ' . date('jS F Y', strtotime($orderDetails->dt_book_upto)) . '</th>
					<th align="left">QUANTITY</th>
					<th align="left">COST</th>
				</tr>
				</thead>
				<tbody>';
                        $total = 0;
                        if (!empty($cartDetailArr)) {
                            foreach ($cartDetailArr as $cartkey => $cartprods) {
                                $templateData .= '<tr>
					<td align="left">' . $cartprods['prod-name'] . '
						<br />';
                                if (!empty($cartprods['allAttribSet'])) {
                                    foreach ($cartprods['allAttribSet'] as $attrKey => $attributesSet) {
                                        if (count($attributesSet) > 0) {
                                            $totalAttrib = count($attributesSet);
                                            $i = 1;
                                            $templateData .= '-';
                                            foreach ($attributesSet as $attributes) {
                                                $templateData .= $attributes->value . ' ' . ($i != $totalAttrib ? ', ' : '');
                                                $i++;
                                            }
                                        }
                                        $templateData .= '<br >';
                                    }
                                }
                                $templateData .= '</td>
					<td align="left">' . $cartprods['quantity'] . '</td>
					<td align="left">$' . number_format($cartprods['price'] * ($cartprods['product_type'] == 5 ? $cartprods['quantity'] : 1), 2, '.', ',') . '</td>
				</tr>';
                                $total = $total + $cartprods['price'] * ($cartprods['product_type'] == 5 ? $cartprods['quantity'] : 1);
                            }
                        }
                        $templateData .= '</tbody>
			</table><br />
			<table cellspacing="1" cellpadding="1" border="1">
				<tbody>
				<tr>
					<td align="left"><strong>SUB TOTAL</strong></td>
					<td align="left">$' . number_format($total, 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>MULTI SET DISCOUNT</strong></td>
					<td align="left">$' . number_format($cartprods['Discount'], 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>PARTNER DISCOUNT</strong></td>
					<td align="left">$' . number_format(($orderDetails->partner_discount_amnt > 0 ? $orderDetails->partner_discount_amnt : 0.00), 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>INSURANCE</strong></td>
					<td align="left">$' . number_format($orderDetails->insurance_amnt, 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>HANDLING / DELIVERY FEE</strong></td>
					<td align="left">$' . number_format($orderDetails->shipping_amnt, 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>TSS DISCOUNT</strong></td>
					<td align="left"><b>- </b>$' . number_format($orderDetails->tss, 2, '.', ',') . '</td>
				</tr>
                <tr>
                    <td align="left"><strong>OFFER CODE</strong></td>
                    <td align="left">' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_Code : 'N/A') . '</td>
                </tr>
                <tr>
                    <td align="left"><strong>OFFER DISCOUNT</strong></td>
                    <td align="left"><b>- </b>$' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_amnt : '0.00') . '</td>
                </tr>
				<tr>
					<td align="left"><strong>TOTAL</strong></td>
					<td align="left">$' . number_format((number_format($total, 2, '.', '') + number_format($orderDetails->shipping_amnt, 2, '.', '') + number_format($orderDetails->insurance_amnt, 2, '.', '') - number_format($cartprods['Discount'] + $orderDetails->tss + ($orderDetails->partner_discount_amnt > 0 ? $orderDetails->partner_discount_amnt : 0.00), 2, '.', '') - number_format((!empty($orderDetails->offer_Code) ? $orderDetails->offer_amnt : 0.00), 2, '.', '')), 2, '.', ',') . '</td>
				</tr>
				</tbody>
			</table><br />
			<h2>Your Details</h2>
			<br />
			<table cellspacing="1" cellpadding="4" border="1">
			<tr>
			<th align="left">First Name</th>
			<td align="left">' . $orderDetails->buyer_first_name . '</td>
</tr>
<tr>
			<th align="left">Surname</th>
			<td align="left">' . $orderDetails->buyer_last_name . '</td>
</tr>
<tr>
			<th align="left">Email</th>
			<td align="left"><a href="mailto:' . $orderDetails->buyer_email . '">' . $orderDetails->buyer_email . '</a></td>
</tr>
<tr>
			<th align="left">Country of residence</th>
			<td align="left">' . $country . '</td>
</tr>
<tr>
			<th align="left">Contact Phone Number In Australia</th>
			<td align="left"><a href="tel:' . $orderDetails->phone_no_aus . '">' . $orderDetails->phone_no_aus . '</a></td>
</tr>
</table><br />
<table cellpadding="1" cellspacing="4" border="1">
<tr>
<th colspan="2" align="left">Delivery Details (Drop off)</th>
<th colspan="2" align="left">Delivery Details (Pick up)</th>
</tr>
<tr>
<th align="left">Name of hotel/golf course/accommodation</th>
<td align="left">' . $orderDetails->delvr_hotel_name . '</td>
<th align="left">Name of hotel/golf course/accommodation</th>
<td align="left">' . $orderDetails->pickup_hotel_name . '</td>
</tr>
<tr>
<th align="left">Address for clubs(street no,Street name)</th>
<td align="left">' . $orderDetails->delvr_address . '</td>
<th align="left">Address for clubs(street no,Street name)</th>
<td align="left">' . $orderDetails->pickup_address . '</td>
</tr>
<tr>
<th align="left">State</th>
<td align="left">' . $Deliverystates . '</td>
<th align="left">State</th>
<td align="left">' . $Pickupstates . '</td>
</tr>
<tr>
<th align="left">Post code</th>
<td align="left">' . $orderDetails->delvr_postal_code . '</td>
<th align="left">Post code</th>
<td align="left">' . $orderDetails->pickup_postal_code . '</td>
</tr>
<tr>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->suburb . '</td>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->suburbpickup . '</td>
</tr>
</table>';

                        /*Template Data For Mandrill END*/

                        $orderResponse = $this->createOrder($order_reference_id);

                        if (!empty($orderResponse) && trim($orderResponse['status']) == "SUCCESS") {
                            $checkOrderExistArr = $this->checkOrderByPaymentRefId($PaymentRefId);
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
<th align="left">' . ($checkOrderExist->payment_option == 1 ? 'Merchant ID' : ($checkOrderExist->payment_option == 2 ? 'Vendor ID' : '')) . '</th>
<td align="left">' . $checkOrderExist->merchant_email . '</td>
</tr>
<tr>
<th align="left">Amount Paid</th>
<td align="left">$' . number_format($checkOrderExist->paid_amnt, '2', '.', ',') . '</td>
</tr>
<tr>
<th align="left">Payment Gateway</th>
<td align="left">' . ($checkOrderExist->payment_option == 1 ? 'Pay Dollar' : ($checkOrderExist->payment_option == 2 ? 'NAB Transact' : 'N/A')) . '</td>
</tr>
<tr>
<th align="left">Status</th>
<td align="left">' . $checkOrderExist->payment_success_response . '</td>
</tr>
<tr>
<th align="left">Date/Time</th>
<td align="left">' . date('d-m-Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn)) . '</td>
</tr>
</table><br />';

                                $MandrillOrderData .= $templateData;

                                $mandrillDataArr = array();

                                $mandrillDataArr['username'] = $orderDetails->buyer_first_name;
                                $mandrillDataArr['useremail'] = $checkOrderExist->user_email;
                                $mandrillDataArr['orderDetail'] = $MandrillOrderData;
                                $mandrillDataArr['templateName'] = '1st email Customer Purchase';
                                $mandrillDataArr['htmlmessage'] = 'Thank you for your booking.';
                                $mandrillDataArr['subject'] = 'The Sweet Spot Club Hire - Order Details' . date('d-m-Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn));
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
                                        'content' => 'The Sweet Spot Club Hire'
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
                                        'subject' => 'The Sweet Spot Club Hire - Your order details.',
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
                                return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr'));
                            }
                        } else {
                            $response['status'] = "ERROR";
                            $response['errors'] = $orderResponse['errors'];
                        }
                    }
                }

            }
        }

    }

    public function thankyounew(Mail $mandrill, Request $request)
    {
        View::share('title', 'Thank You!');
        $FirstTime = '0';
        $discountErr = '0';
        $payerid = '';
        $supportEmail = Config::get('constants.customerSupportEmail');
        $checkOrderExist = array();
        $error = '';
        $errorFlag = 0;
        $order_reference_id = (isset($request->idOrder) ? $request->idOrder:(isset($_COOKIE['order_reference_id']) && !empty($_COOKIE['order_reference_id']) ? $_COOKIE['order_reference_id'] : ''));
        /*Stripe Payment*/
        Stripe::setApiKey(env('STRIPE_SECRET'));
        // Get the credit card details submitted by the form
        $input = Input::all();
        $token = Input::get('stripeToken');
        // Create the charge on Stripe's servers - this will charge the user's card
        if(!empty($token)){
            try {
                $this->hire->updatePaymentOptByParameter($order_reference_id,3);
                $charge = Charge::create(array(
                        "amount" => $input['amount'],
                        "currency" => "aud",
                        "card"  => $token,
                        "description" => $input['description'])
                );
                $transactionId     = $charge->balance_transaction;
                $payerid       = $charge->source->id;
                $status        = $charge->status;
                $log  = "Token: ".$token.PHP_EOL.
                    "transactionId: ".$transactionId.PHP_EOL.
                    "payerid: ".$payerid.PHP_EOL.
                    "status: ".$status.PHP_EOL.
                    "order reference id: ".$order_reference_id.PHP_EOL.
                    "time: ".time().PHP_EOL.
                "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('../couriers/stripe_log_post.txt', $log, FILE_APPEND);

                if(!empty($transactionId) && $status == 'succeeded'){
                    View::share('PageDescription1', 'Thank you for hiring from the Sweet Spot Club Hire! We hope you enjoy your clubs and they help deliver you a great round or golf trip.');
                    View::share('PageDescription2', 'We will aim to provide the best quality service for you and will be there for whatever you need along the way. Feel free to contact us via email at info@tssclubhire or via our social media pages @tssclubhire if you have any questions or concerns. We will be sending you information in regards to your hire closer to your initial hire date, with all the details you will require. Thank you again for choosing The Sweet Spot and we hope your next golf experience is a great one.');
                    $FirstTime = '1';
                    $xero = App::make('XeroPrivate');
                    $contact = App::make('XeroContact');
                    $invoice = App::make('XeroInvoice');
                    $tssDiscountPercentage = Config::get('constants.TssDiscount');
                    $DiscountList = Config::get('constants.Discount');
                    $orderDetailsAry = $this->hire->getPreOrderDetails($order_reference_id);

                    if (count($orderDetailsAry) > 0) {
                        $orderDetails = $orderDetailsAry[0];
                        $contact->setContactStatus('ACTIVE');
                        $contact->setName($orderDetails->buyer_first_name . ' ' . $orderDetails->buyer_last_name);
                        $contact->setFirstName($orderDetails->buyer_first_name);
                        $contact->setLastName($orderDetails->buyer_last_name);
                        $contact->setEmailAddress($orderDetails->buyer_email);
                        $contact->setDefaultCurrency('AUD');
                        $invoice->setContact($contact);
                        $invoice->setType('ACCREC');
                        $invoice->setDate(Carbon::now());
                        $invoice->setDueDate(Carbon::now()->addDays(4));
                        $invoice->setLineAmountType('Exclusive');
                        $invoice->setStatus('AUTHORISED');
                        $PreOrderProductsArr = $this->hire->getCartByRefId($order_reference_id, array(), $orderDetails->state_id);
                        $setCount = $this->hire->getCartSetCount($order_reference_id);
                        if (count($PreOrderProductsArr) > 0) {
                            $i = 0;
                            $discountFlag = true;
                            $disCount = 0;
                            foreach ($PreOrderProductsArr as $ProdArrkey => $OrderProducts) {

                                $line[$i] = App::make('XeroInvoiceLine');
                                if ($discountFlag) {
                                    /*if ($OrderProducts['setcount'] > 1 && $OrderProducts['setcount'] < 11) {
                                        $disCount = $DiscountList[$OrderProducts['setcount']];
                                    } elseif ($OrderProducts['setcount'] > 10) {
                                        $disCount = $DiscountList['11'];
                                    }
                                    if ($orderDetails->tss > 0) {
                                        $disCount = $disCount + $tssDiscountPercentage;
                                    }*/
                                    if ($setCount > 1 && $setCount < 11) {
                                        $disCount = $DiscountList[$setCount];
                                    } elseif ($setCount > 10) {
                                        $disCount = $DiscountList['11'];
                                    }
                                    if ($orderDetails->tss > 0) {
                                        $disCount = $disCount + $tssDiscountPercentage;
                                    }

                                    $discountFlag = false;
                                }
                                if ($disCount > 100) {
                                    $discountErr = '1';
                                    if (isset($request->adminFlag) && $request->adminFlag == '1') {
                                        return redirect()->to('/disputed_orders')
                                            ->with('success', 'Order created successfully');
                                    } else {
                                        return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr'));
                                    }
                                } else {
                                    $line[$i]->setDescription(strip_tags($OrderProducts['prod-description']));
                                    $line[$i]->setQuantity($OrderProducts['quantity']);
                                    $line[$i]->setUnitAmount($OrderProducts['price']);
                                    $line[$i]->setAccountCode(230);
                                    $line[$i]->setTaxType('NONE');
                                    $line[$i]->setDiscountRate($disCount);
                                    $invoice->addLineItem($line[$i]);
                                    $i++;
                                }
                            }
                            if ($orderDetailsAry[0]->offer_applied == 1) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Offer Code Applied - "' . $orderDetailsAry[0]->offer_Code . '"');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount('-' . $orderDetails->offer_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if ($orderDetailsAry[0]->insurance_amnt > 0) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Hire club set insurance fees.');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount($orderDetails->insurance_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            if ($orderDetailsAry[0]->partner_discount_amnt > 0) {
                                $line[$i] = App::make('XeroInvoiceLine');
                                $line[$i]->setDescription('Partner Discount');
                                $line[$i]->setQuantity(1);
                                $line[$i]->setUnitAmount(-$orderDetails->partner_discount_amnt);
                                $line[$i]->setAccountCode(230);
                                $line[$i]->setTaxType('NONE');
                                $line[$i]->setDiscountRate(0);
                                $invoice->addLineItem($line[$i]);
                                $i++;
                            }
                            $line[$i] = App::make('XeroInvoiceLine');
                            $line[$i]->setDescription('Handling / Delivery Fee');
                            $line[$i]->setQuantity(1);
                            $line[$i]->setUnitAmount($orderDetails->shipping_amnt);
                            $line[$i]->setAccountCode(230);
                            $line[$i]->setTaxType('NONE');
                            $line[$i]->setDiscountRate(0);
                            $invoice->addLineItem($line[$i]);
                        }
                        $xero->save($invoice);
                        $invoicesArr = XeroPrivate::load('Accounting\\Invoice')->execute();
                        $totalInvoices = count($invoicesArr);
                        $line[$i]->setAccountCode('SALES');
                        $this->hire->updateInvoiceNo($invoicesArr[$totalInvoices - 1]->InvoiceNumber, $order_reference_id, $transactionId);
                        /*Template Data For Mandrill START*/
                        $cartDetailArr = $this->hire->getCartByRefId($order_reference_id, array(), $orderDetails->state_id);
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
                        $Pickupstates = $PickupstatesAry[0]->name;

                        $templateData = '<table cellspacing="1" cellpadding="4" border="1">
				<thead>
				<tr>
					<th align="left">YOUR ORDER : ' . date('jS F Y', strtotime($orderDetails->dt_book_from)) . ' - ' . date('jS F Y', strtotime($orderDetails->dt_book_upto)) . '</th>
					<th align="left">QUANTITY</th>
					<th align="left">COST</th>
				</tr>
				</thead>
				<tbody>';
                        $total = 0;
                        if (!empty($cartDetailArr)) {
                            foreach ($cartDetailArr as $cartkey => $cartprods) {
                                $templateData .= '<tr>
					<td align="left">' . $cartprods['prod-name'] . '
						<br />';
                                if (!empty($cartprods['allAttribSet'])) {
                                    foreach ($cartprods['allAttribSet'] as $attrKey => $attributesSet) {
                                        if (count($attributesSet) > 0) {
                                            $totalAttrib = count($attributesSet);
                                            $i = 1;
                                            $templateData .= '-';
                                            foreach ($attributesSet as $attributes) {
                                                $templateData .= $attributes->value . ' ' . ($i != $totalAttrib ? ', ' : '');
                                                $i++;
                                            }
                                        }
                                        $templateData .= '<br >';
                                    }
                                }
                                $templateData .= '</td>
					<td align="left">' . $cartprods['quantity'] . '</td>
					<td align="left">$' . number_format($cartprods['price'] * ($cartprods['product_type'] == 5 ? $cartprods['quantity'] : 1), 2, '.', ',') . '</td>
				</tr>';
                                $total = $total + $cartprods['price'] * ($cartprods['product_type'] == 5 ? $cartprods['quantity'] : 1);
                            }
                        }
                        $templateData .= '</tbody>
			</table><br />
			<table cellspacing="1" cellpadding="1" border="1">
				<tbody>
				<tr>
					<td align="left"><strong>SUB TOTAL</strong></td>
					<td align="left">$' . number_format($total, 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>MULTI SET DISCOUNT</strong></td>
					<td align="left">$' . number_format($cartprods['Discount'], 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>PARTNER DISCOUNT</strong></td>
					<td align="left">$' . number_format(($orderDetails->partner_discount_amnt > 0 ? $orderDetails->partner_discount_amnt : 0.00), 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>INSURANCE</strong></td>
					<td align="left">$' . number_format($orderDetails->insurance_amnt, 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>HANDLING / DELIVERY FEE</strong></td>
					<td align="left">$' . number_format($orderDetails->shipping_amnt, 2, '.', ',') . '</td>
				</tr>
				<tr>
					<td align="left"><strong>TSS DISCOUNT</strong></td>
					<td align="left"><b>- </b>$' . number_format($orderDetails->tss, 2, '.', ',') . '</td>
				</tr>
                <tr>
                    <td align="left"><strong>OFFER CODE</strong></td>
                    <td align="left">' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_Code : 'N/A') . '</td>
                </tr>
                <tr>
                    <td align="left"><strong>OFFER DISCOUNT</strong></td>
                    <td align="left"><b>- </b>$' . (!empty($orderDetails->offer_Code) ? $orderDetails->offer_amnt : '0.00') . '</td>
                </tr>
				<tr>
					<td align="left"><strong>TOTAL</strong></td>
					<td align="left">$' . number_format((number_format($total, 2, '.', '') + number_format($orderDetails->shipping_amnt, 2, '.', '') + number_format($orderDetails->insurance_amnt, 2, '.', '') - number_format($cartprods['Discount'] + $orderDetails->tss + ($orderDetails->partner_discount_amnt > 0 ? $orderDetails->partner_discount_amnt : 0.00), 2, '.', '') - number_format((!empty($orderDetails->offer_Code) ? $orderDetails->offer_amnt : 0.00), 2, '.', '')), 2, '.', ',') . '</td>
				</tr>
				</tbody>
			</table><br />
			<h2>Your Details</h2>
			<br />
			<table cellspacing="1" cellpadding="4" border="1">
			<tr>
			<th align="left">First Name</th>
			<td align="left">' . $orderDetails->buyer_first_name . '</td>
</tr>
<tr>
			<th align="left">Surname</th>
			<td align="left">' . $orderDetails->buyer_last_name . '</td>
</tr>
<tr>
			<th align="left">Email</th>
			<td align="left"><a href="mailto:' . $orderDetails->buyer_email . '">' . $orderDetails->buyer_email . '</a></td>
</tr>
<tr>
			<th align="left">Country of residence</th>
			<td align="left">' . $country . '</td>
</tr>
<tr>
			<th align="left">Contact Phone Number In Australia</th>
			<td align="left"><a href="tel:' . $orderDetails->phone_no_aus . '">' . $orderDetails->phone_no_aus . '</a></td>
</tr>
</table><br />
<table cellpadding="1" cellspacing="4" border="1">
<tr>
<th colspan="2" align="left">Delivery Details (Drop off)</th>
<th colspan="2" align="left">Delivery Details (Pick up)</th>
</tr>
<tr>
<th align="left">Name of hotel/golf course/accommodation</th>
<td align="left">' . $orderDetails->delvr_hotel_name . '</td>
<th align="left">Name of hotel/golf course/accommodation</th>
<td align="left">' . $orderDetails->pickup_hotel_name . '</td>
</tr>
<tr>
<th align="left">Address for clubs(street no,Street name)</th>
<td align="left">' . $orderDetails->delvr_address . '</td>
<th align="left">Address for clubs(street no,Street name)</th>
<td align="left">' . $orderDetails->pickup_address . '</td>
</tr>
<tr>
<th align="left">State</th>
<td align="left">' . $Deliverystates . '</td>
<th align="left">State</th>
<td align="left">' . $Pickupstates . '</td>
</tr>
<tr>
<th align="left">Post code</th>
<td align="left">' . $orderDetails->delvr_postal_code . '</td>
<th align="left">Post code</th>
<td align="left">' . $orderDetails->pickup_postal_code . '</td>
</tr>
<tr>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->suburb . '</td>
<th align="left">Suburb </th>
<td align="left">' . $orderDetails->suburbpickup . '</td>
</tr>
</table>';

                        /*Template Data For Mandrill END*/

                        $orderResponse = $this->createOrder($order_reference_id,$payerid);

                        if (!empty($orderResponse) && trim($orderResponse['status']) == "SUCCESS") {
                            $checkOrderExistArr = $this->checkOrderByPaymentRefId($transactionId);
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
<td align="left">' . date('d-m-Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn)) . '</td>
</tr>
</table><br />';

                                $MandrillOrderData .= $templateData;

                                $mandrillDataArr = array();

                                $mandrillDataArr['username'] = $orderDetails->buyer_first_name;
                                $mandrillDataArr['useremail'] = $checkOrderExist->user_email;
                                $mandrillDataArr['orderDetail'] = $MandrillOrderData;
                                $mandrillDataArr['templateName'] = '1st email Customer Purchase';
                                $mandrillDataArr['htmlmessage'] = 'Thank you for your booking.';
                                $mandrillDataArr['subject'] = 'The Sweet Spot Club Hire - Order Details' . date('d-m-Y / h:i:s A', strtotime($checkOrderExist->dtCreatedOn));
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
                                        'content' => 'The Sweet Spot Club Hire'
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
                                        'subject' => 'The Sweet Spot Club Hire - Your order details.',
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
                                return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr','error','errorFlag'));
                            }
                        } else {
                            $response['status'] = "ERROR";
                            $response['errors'] = $orderResponse['errors'];
                            $error = $response['errors'];
                            $errorFlag = 1;
                            return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr','error','errorFlag'));
                        }
                    }else{
                        $error = 'Order does not exist.';
                        $errorFlag = 1;
                        return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr','error','errorFlag'));
                    }
                }else{
                    $error = 'Payment declined. Please try again later.';
                    $errorFlag = 1;
                    return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr','error','errorFlag'));
                }
                // if status="succeeded" do rest of the insert operation start
                // end
            } catch(Card $e) {
                $e_json = $e->getJsonBody();
                $error = $e_json['error'];
                // The card has been declined
                // redirect back to checkout page
                /*return Redirect::to('/')
                    ->with_input()
                    ->with('card_errors',$error);*/
                $errorFlag = 1;
                return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr','error','errorFlag'));
            }
        }else{
            $log  = "Token: No token generated.".PHP_EOL.
                    "Date Time: ".(date('d-m-Y h:i:s')).PHP_EOL.
                "-------------------------".PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents('../stripe_log_post.txt', $log, FILE_APPEND);

            $error = 'Access denied. Invalid access to the page.';
            $errorFlag = 2;
            return view('pages.frontend.thankyou', compact('checkOrderExist', 'FirstTime', 'discountErr','error','errorFlag'));

        }
        /*Payment End*/

    }

    /*public function createOrderByAdmin(Request $request){
        $this->thank_you()
    }*/
    public function checkOrderByPaymentRefId($transactionId)
    {
        $OrderDet = DB::table($this->DBTables['Orders'])
            ->where('payment_transaction_id', '=', $transactionId)
            ->get();
        return $OrderDet;
    }

    public function createOrder($reference_id = '',$payerid='')
    {
        $response = array();

        $order_reference_id = trim($reference_id);
        $xero = App::make('XeroPrivate');
        $invoice = App::make('XeroInvoice');
        if (trim($order_reference_id) != '') {
            $DBTables = Config::get('constants.DbTables');

            $orderDetailsAry = DB::table($DBTables['Pre_Orders'])
                ->where('order_reference_id', '=', $order_reference_id)
                ->get();
            if (count($orderDetailsAry) > 0) {
                $orderDetails = $orderDetailsAry[0];
            } else {
                $orderDetails = array();
            }

            $productAry = DB::table($DBTables['Pre_Orders_Products'])
                ->join($DBTables['Products'], $DBTables['Products'] . '.id', '=', $DBTables['Pre_Orders_Products'] . '.product_id')
                ->where($DBTables['Pre_Orders_Products'] . '.order_reference_id', '=', $order_reference_id)
                ->select($DBTables['Pre_Orders_Products'] . '.*',
                    $DBTables['Products'] . '.name',
                    $DBTables['Products'] . '.description',
                    $DBTables['Products'] . '.sku',
                    $DBTables['Products'] . '.product_type'
                )
                ->get();

            if (!empty($orderDetails)) {

                $paymentResponse = $this->makeOrderPayment($orderDetails,$payerid);
                if (trim($paymentResponse['status']) == "SUCCESS") {

                    $insertOrderAry = array();

                    $insertOrderAry['order_reference_id'] = trim($order_reference_id);

                    $partnerSession = session()->get("partner_credn");

                    if ($partnerSession) {

                        $partner = DB::table($DBTables['Partners'])
                            ->where('id', '=', (int)$partnerSession['idPartner'])
                            ->select('id', 'name', 'email')
                            ->get();

                        if (count($partner) > 0) {

                            $insertOrderAry['user_id'] = (int)$partner[0]->id;
                            $insertOrderAry['user_name'] = trim($partner[0]->name);
                            $insertOrderAry['user_email'] = trim($partner[0]->email);
                            $insertOrderAry['is_partner_user'] = 1;
                        }


                    } else {
                        if (trim($orderDetails->buyer_email) != '') {
                            $user = DB::table($DBTables['Users'])
                                ->where('email', '=', trim($orderDetails->buyer_email))
                                ->select('id', 'name', 'email')
                                ->get();
                            if (count($user) > 0) {
                                $insertOrderAry['user_id'] = (int)$user[0]->id;
                                $insertOrderAry['user_name'] = trim($user[0]->name);
                                $insertOrderAry['user_email'] = trim($user[0]->email);
                            } else {
                                $insertUser = array();
                                $insertUser['name'] = $orderDetails->buyer_first_name . " " . $orderDetails->buyer_last_name;
                                $insertUser['email'] = $orderDetails->buyer_email;
                                $insertUser['password'] = bcrypt($order_reference_id);
                                $insertUser['role'] = 1;
                                $insertUser['contact_no'] = '';
                                $insertUser['address'] = '';
                                $insertUser['zipcode'] = '';
                                $insertUser['state'] = '';
                                $insertUser['country'] = $orderDetails->buyer_country;

                                $id_user = DB::table($DBTables['Users'])->insertGetId($insertUser);

                                $insertOrderAry['user_id'] = (int)$id_user;
                                $insertOrderAry['user_name'] = trim($insertUser['name']);
                                $insertOrderAry['user_email'] = trim($orderDetails->buyer_email);
                            }
                        }
                    }

                    $insertOrderAry['dt_book_from'] = date('Y-m-d H:i:s', strtotime($orderDetails->dt_book_from));
                    $insertOrderAry['dt_book_upto'] = date('Y-m-d H:i:s', strtotime($orderDetails->dt_book_upto));
                    $insertOrderAry['hire_days'] = $orderDetails->hire_days;
                    $insertOrderAry['offer_id'] = $orderDetails->offer_id;

                    if ((int)$orderDetails->offer_id > 0) {
                        $offerAry = DB::table($DBTables['Offers'])
                            ->where('id', '=', $orderDetails->offer_id)
                            ->select('name', 'description', 'szCoupnCode', 'offer_type', 'offer_percntg')
                            ->get();

                        if (count($offerAry) > 0) {
                            $insertOrderAry['offer_name'] = $offerAry[0]->name;
                            $insertOrderAry['offer_description'] = $offerAry[0]->description;
                            $insertOrderAry['offer_Code'] = $offerAry[0]->szCoupnCode;
                            $insertOrderAry['offer_type'] = $offerAry[0]->offer_type;
                            $insertOrderAry['offer_percntg'] = $offerAry[0]->offer_percntg;
                        }

                    }

                    $insertOrderAry['partner_ref_key'] = trim($orderDetails->partner_ref_key);
                    $insertOrderAry['banner_ref_key'] = trim($orderDetails->banner_ref_key);
                    $insertOrderAry['partner_cmsn_percnt'] = (float)$orderDetails->partner_cmsn_percnt;
                    $insertOrderAry['partner_cmsn_amt'] = (float)$orderDetails->partner_cmsn_amt;
                    $insertOrderAry['partner_discount_percnt'] = (float)$orderDetails->partner_discount_percnt;
                    $insertOrderAry['partner_discount_amnt'] = (float)$orderDetails->partner_discount_amnt;
                    $insertOrderAry['sub_total_amnt'] = (float)$orderDetails->sub_total_amnt;
                    $insertOrderAry['offer_amnt'] = (float)$orderDetails->offer_amnt;
                    $insertOrderAry['shipping_tax_percnt'] = $orderDetails->shipping_tax_percnt;
                    $insertOrderAry['shipping_amnt'] = (float)$orderDetails->shipping_amnt;
                    $insertOrderAry['insurance_amnt'] = (float)$orderDetails->insurance_amnt;
                    $insertOrderAry['news_letter_signup'] = (int)$orderDetails->news_letter_signup;
                    $insertOrderAry['signup_discount_amnt'] = (float)$orderDetails->signup_discount_amnt;
                    $insertOrderAry['total_amnt'] = (float)$orderDetails->total_amnt;
                    $insertOrderAry['paid_amnt'] = (float)$paymentResponse['paidAmount'];
                    $insertOrderAry['order_status'] = 2;
                    $insertOrderAry['purchased_date'] = date('Y-m-d H:i:s', strtotime($orderDetails->dtCreatedOn));
                    $insertOrderAry['payment_date'] = date('Y-m-d H:i:s');
                    $insertOrderAry['delvr_first_name'] = $orderDetails->delvr_first_name;
                    $insertOrderAry['delvr_last_name'] = $orderDetails->delvr_last_name;
                    $insertOrderAry['delvr_email'] = $orderDetails->delvr_email;
                    $insertOrderAry['dropoff_place'] = $orderDetails->dropoff_place;
                    $insertOrderAry['delvr_hotel_name'] = $orderDetails->delvr_hotel_name;
                    $insertOrderAry['delvr_course_name'] = $orderDetails->delvr_course_name;
                    $insertOrderAry['delvr_phone_num'] = $orderDetails->delvr_phone_num;
                    $insertOrderAry['delvr_address'] = $orderDetails->delvr_address;
                    $insertOrderAry['delvr_state_id'] = $orderDetails->delvr_state_id;
                    $insertOrderAry['delvr_postal_code'] = $orderDetails->delvr_postal_code;
                    $insertOrderAry['delvr_country_id'] = $orderDetails->delvr_country_id;
                    $insertOrderAry['here_abt_us'] = $orderDetails->here_abt_us;
                    $insertOrderAry['delvr_date_time'] = date('Y-m-d H:i:s', strtotime($orderDetails->delvr_date_time));
                    $insertOrderAry['is_same_pickup_addrs'] = (int)$orderDetails->is_same_pickup_addrs;
                    $insertOrderAry['pickup_place'] = $orderDetails->pickup_place;
                    $insertOrderAry['pickup_hotel_name'] = $orderDetails->pickup_hotel_name;
                    $insertOrderAry['pickup_course_name'] = $orderDetails->pickup_course_name;
                    $insertOrderAry['pickup_address'] = $orderDetails->pickup_address;
                    $insertOrderAry['pickup_state_id'] = $orderDetails->pickup_state_id;
                    $insertOrderAry['pickup_postal_code'] = $orderDetails->pickup_postal_code;
                    $insertOrderAry['pickup_country_id'] = $orderDetails->pickup_country_id;
                    $insertOrderAry['pickup_date_time'] = date('Y-m-d H:i:s', strtotime($orderDetails->pickup_date_time));
                    $insertOrderAry['additional_notes'] = $orderDetails->additional_notes;
                    $insertOrderAry['payment_method'] = 1;
                    $insertOrderAry['payment_invoice'] = '';
                    $insertOrderAry['merchant_email'] = $paymentResponse['merchant_email'];
                    $insertOrderAry['buyer_first_name'] = $orderDetails->buyer_first_name;
                    $insertOrderAry['buyer_last_name'] = $orderDetails->buyer_last_name;
                    $insertOrderAry['buyer_email'] = $orderDetails->buyer_email;
                    $insertOrderAry['buyer_country'] = $orderDetails->buyer_country;
                    $insertOrderAry['suburb'] = $orderDetails->suburb;
                    $insertOrderAry['suburbpickup'] = $orderDetails->suburbpickup;
                    $insertOrderAry['phone_no_aus'] = $orderDetails->phone_no_aus;
                    $insertOrderAry['buyer_request_ip'] = '';
                    $insertOrderAry['payment_success_response'] = $paymentResponse['payment_response'];
                    $insertOrderAry['payment_transaction_id'] = $paymentResponse['transaction_id'];
                    $insertOrderAry['dtCreatedOn'] = date('Y-m-d H:i:s');
                    $insertOrderAry['tss'] = $orderDetails->tss;
                    $insertOrderAry['invoice_no'] = $orderDetails->invoice_no;
                    $insertOrderAry['state_id'] = $orderDetails->state_id;
                    $insertOrderAry['payment_option'] = $orderDetails->payment_option;

                    $id_Order = DB::table($DBTables['Orders'])->insertGetId($insertOrderAry);
                    /*$invoice = $xero->loadByGUID('Accounting\\Invoice', $orderDetails->invoice_no);

					$invoice->setStatus('PAID');

					$xero->save($invoice);*/

                    // Insert Products in order and update Inventory
                    if (count($productAry) > 0) {
                        foreach ($productAry as $product) {
                            $insertProAry = array();
                            $insertProInventryAry = array();

                            $insertProAry['user_id'] = (int)$insertOrderAry['user_id'];
                            $insertProAry['order_id'] = (int)$id_Order;
                            $insertProAry['product_id'] = (int)$product->product_id;
                            $insertProAry['product_name'] = $product->name;
                            $insertProAry['product_description'] = $product->description;
                            $insertProAry['product_sku'] = $product->sku;
                            $insertProAry['quantity'] = (int)$product->quantity;

                            if ((int)$product->product_type == 1 || $product->product_type == 2) {
                                $insertProAry['is_sale_product'] = 1;
                            } else {
                                $insertProAry['is_sale_product'] = 0;
                            }

                            $insertProAry['sub_total_amnt'] = (float)$product->sub_total_amnt;
                            $insertProAry['product_attributes'] = $this->getProductAttribute($product->product_id, true);

                            $id_Order_Product = DB::table($DBTables['Orders_Products'])->insertGetId($insertProAry);

                            if ((int)$insertProAry['is_sale_product'] == 0) {
                                $insertProInventryAry['user_id'] = (int)$insertOrderAry['user_id'];
                                $insertProInventryAry['order_id'] = (int)$id_Order;
                                $insertProInventryAry['product_id'] = (int)$product->product_id;
                                $insertProInventryAry['booked_quantity'] = 1;
                                $insertProInventryAry['dt_booked_from'] = $insertOrderAry['dt_book_from'];
                                $insertProInventryAry['dt_booked_upto'] = $insertOrderAry['dt_book_upto'];
                                $insertProInventryAry['dt_delivery'] = $insertOrderAry['delvr_date_time'];
                                $insertProInventryAry['dt_pickup'] = $insertOrderAry['pickup_date_time'];
                                $insertProInventryAry['is_active'] = '1';

                                $id_Booking_Inventory = DB::table($DBTables['Booked_Products'])->insertGetId($insertProInventryAry);
                            } else {
                                $productDetails = DB::table($DBTables['Products'])
                                    ->where('id', '=', $product->product_id)
                                    ->select('quantity')
                                    ->get();

                                if (count($productDetails) > 0) {
                                    $previousQuantity = (int)$productDetails[0]->quantity;
                                    $newQuantity = $previousQuantity - $insertProAry['quantity'];

                                    DB::table($DBTables['Products'])
                                        ->where('id', trim($product->product_id))
                                        ->update(['quantity' => (int)$newQuantity]);
                                }
                            }
                        }
                    }

                    if ((int)$orderDetails->news_letter_signup == 1) {
                        $insertNewsLetterSubs = array();
                        $insertNewsLetterSubs['name'] = $insertOrderAry['user_name'];
                        $insertNewsLetterSubs['email'] = $orderDetails->buyer_email;
                        $insertNewsLetterSubs['iActive'] = '1';

                        $id_news_letter = DB::table($DBTables['Newsletter'])->insertGetId($insertNewsLetterSubs);
                    }

                    DB::table($DBTables['Pre_Orders'])->where('order_reference_id', '=', $order_reference_id)->delete();
                    DB::table($DBTables['Pre_Orders_Products'])->where('order_reference_id', '=', $order_reference_id)->delete();
                    session()->forget('order_reference_id');
                    $time = -(time() + (86400 * 10));
                    setcookie('order_reference_id', null, -$time, "/");
                    setcookie('TSS_PARTNER', null, -$time, "/");
                    $response['status'] = "SUCCESS";
                    $response['message'] = "Thankyou, Your order successfully placed.";

                } else {
                    $failureResponse = array();
                    $failureResponse['payment_transaction_id'] = trim($paymentResponse['transaction_id']);
                    $failureResponse['payment_failure_response'] = $paymentResponse['payment_response'];

                    DB::table($DBTables['Pre_Orders'])
                        ->where('order_reference_id', trim($order_reference_id))
                        ->update($failureResponse);

                    $response['status'] = "ERROR";
                    $response['errors'] = "Payment failed.";
                }

            } else {
                $response['status'] = "ERROR";
                $response['errors'] = "Invalid order reference id.";
            }
        } else {
            $response['status'] = "ERROR";
            $response['errors'] = "Invalid values.";
        }

        return $response;
    }

    public function makeOrderPayment($orderDetails = array(),$payerid='')
    {

        $response = array();
        $PaydollarConfig = Config::get('constants.PaydollarConfig');
        $NabConfig = Config::get('constants.NAB-Transact-Config');
        if (!empty($orderDetails)) {
            $response['status'] = "SUCCESS";
            $response['paidAmount'] = (float)$orderDetails->total_amnt;
            $response['merchant_email'] = (!empty($payerid)?$payerid:($orderDetails->payment_option == 1 ? $PaydollarConfig['merchantId'] : ($orderDetails->payment_option == 2 ? $NabConfig['vendorid'] : '')));
            $response['payment_response'] = "Payment successfully done.";
            $response['transaction_id'] = $orderDetails->payment_transaction_id;
        } else {
            $response['status'] = "ERROR";
            $response['errors'] = "Invalid values.";
        }

        return $response;
    }

    public function getProductAttribute($product_id = 0, $saveFlag = false)
    {

        if ($product_id > 0) {

            $attributeAry = array();
            $saveString = '';
            $savedAttributes = DB::table('product_attrib_map')
                ->join('attribute_vals', 'attribute_vals.id', '=', 'product_attrib_map.attrib_val_id')
                ->join('attributes', 'attributes.id', '=', 'attribute_vals.attrib_id')
                ->select('product_attrib_map.attrib_val_id', 'attribute_vals.value', 'attributes.attrib_name', 'attributes.id')->where('product_attrib_map.prod_id', '=', $product_id)
                ->orderBy('product_attrib_map.id', 'ASC')
                ->get();

            if (count($savedAttributes) > 0) {
                foreach ($savedAttributes as $key => $attributeData) {
                    $attributeAry[$attributeData->attrib_name] = $attributeData->value;
                    $saveString .= $attributeData->attrib_name . ":" . $attributeData->value . ";";
                }
            }

            if ($saveFlag == true) {
                return $saveString;
            } else {
                return $attributeAry;
            }

        }
    }

    public function decreaseQtyFromCart(Request $request)
    {
        $response = array();
        $prodArr = $request->product_idArr;
        DB::table($this->DBTables['Pre_Orders_Products'])
            ->where([
                ['order_reference_id', '=', $request->order_reference_id],
                ['product_id', '=', $prodArr[0]]
            ])->update([
                'quantity' => DB::raw('quantity-1')
            ]);
        if ($this->updateOrderTotalPrice($request->order_reference_id, $request)) {
            $response['status'] = "SUCCESS";
            $response['message'] = "Cart updated successfully.";
        } else {
            $response['status'] = "ERROR";
            $response['errors'] = "Invalid request.";
        }

        return $response;
    }

    public function increaseQtyToCart(Request $request)
    {
        $response = array();
        $prodArr = $request->product_idArr;
        $prodDetArr = $this->getProductDetailsById($prodArr[0]);
        if (count($prodDetArr) > 0) {
            $reqQty = $request->quantity + 1;
            if ($prodDetArr->quantity >= $reqQty) {
                DB::table($this->DBTables['Pre_Orders_Products'])
                    ->where([
                        ['order_reference_id', '=', $request->order_reference_id],
                        ['product_id', '=', $prodArr[0]]
                    ])->update([
                        'quantity' => DB::raw('quantity+1')
                    ]);
                if ($this->updateOrderTotalPrice($request->order_reference_id, $request)) {
                    $response['status'] = "SUCCESS";
                    $response['message'] = "Cart updated successfully.";
                } else {
                    $response['status'] = "ERROR";
                    $response['errors'] = "Invalid request.";
                }
            } else {
                $response['status'] = "ERROR";
                $response['message'] = "No more product is available. Please try after some time.";;
            }
        } else {
            $response['status'] = "ERROR";
            $response['errors'] = "Invalid request.";
        }

        return $response;
    }
}