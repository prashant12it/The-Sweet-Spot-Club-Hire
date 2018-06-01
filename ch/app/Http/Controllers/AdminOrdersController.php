<?php

namespace App\Http\Controllers;

use App;
use App\Orders;
use Config;
use DB;
use Illuminate\Http\Request;
use View;

class AdminOrdersController extends Controller
{
    public function __construct()
    {
        $this->DBTables = Config::get('constants.DbTables');
        DB::enableQueryLog();
    }

    public function index(Request $request)
    {
        View::share('title', 'Orders Listing');
        session()->flash('searchOrder', null);
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        $statusAry = $this->getOrderStatus();
        $ordersAry = Orders::orderBy('id', 'DESC')->where('order_status', '>', 1)->paginate($rowsPerPage);
        return view('pages.orders.orders_listing', compact('ordersAry', 'statusAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function courier($mail=false,$pickup=false)
    {
        if(!$mail) {
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=courier.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
        }
        View::share('title', 'Courier');
        $yesterday = date('Y-m-d',strtotime('-1 day'));
//        $yesterday = date('Y-m-d',strtotime('2017-11-01'));
        if($pickup){
            $ordersAry = Orders::where('dt_book_upto','Like','%'.$yesterday.'%')->orderBy('id', 'DESC')->get();
        }else{
            $ordersAry = Orders::where('dtCreatedOn','Like','%'.$yesterday.'%')->orderBy('id', 'DESC')->get();
        }

        $getProdCountArr = DB::table($this->DBTables['Orders_Products'])
            ->select(DB::raw('count(*) as recount'))
            ->groupBy('order_id')
            ->get();
        $prodCount = array();
        foreach ($getProdCountArr as $prods) {
            array_push($prodCount, $prods->recount);
        }
        if(count($prodCount)>0){
            $maxProdCount = max($prodCount);
        }else{
            $maxProdCount = 0;
        }

        $list = array("Order Reference ID", "Order Date", "Customer", "Amount($)", "Customer Email", "Transaction ID", "Paid Date", "Buyer Name", "Buyer Email", "Buyer Country", "Mobile No.", "Delivery Hotel/Course Name",
            "Delivery Address", "Delivery State", "Delivery Country", "Delivery Postal Code", "Delivery Suburb", "Pickup Hotel/Course Name", "Pickup Address", "Pickup State", "Pickup Country", "Pickup Postal Code", "Pickup Suburb", "Date of Delivery (-1 dayfrom first day of hire)",
            "date of pickup (+1 day from last day of hire)");
        for ($i = 1; $i <= $maxProdCount; $i++) {
            array_push($list, 'Product ' . $i);
        }
        if($mail){
            $file = fopen('php://temp', 'w+');
        }else{
            $file = fopen('php://output', 'w');
        }
        fputcsv($file, $list);
        if (count($ordersAry) > 0) {
            foreach ($ordersAry as $orders) {
                $BuyerCountry = $this->getCountryByID($orders->buyer_country);
                $deliveryState = $this->getStateByID($orders->delvr_state_id);
                $pickupState = $this->getStateByID($orders->pickup_state_id);
                $deliveryCountry = $this->getCountryByID(($orders->delvr_country_id > 0 ? $orders->delvr_country_id : 13));
                $pickupCountry = $this->getCountryByID(($orders->pickup_country_id > 0 ? $orders->pickup_country_id : 13));;
                $rec = array($orders->order_reference_id, date('d/m/Y', strtotime($orders->dtCreatedOn)), $orders->user_name, number_format($orders->total_amnt, 2),
                    $orders->user_email, $orders->payment_transaction_id, date('d/m/Y', strtotime($orders->payment_date)), $orders->buyer_first_name, $orders->buyer_email,
                    $BuyerCountry->name, $orders->phone_no_aus, $orders->delvr_hotel_name, $orders->delvr_address, $deliveryState->name, $deliveryCountry->name, $orders->delvr_postal_code, $orders->suburb, $orders->pickup_hotel_name,
                    $orders->pickup_address, $pickupState->name, $pickupCountry->name, $orders->pickup_postal_code, $orders->suburbpickup, date('d/m/Y', strtotime($orders->dt_book_from . ' -1 day ')),
                    date('d/m/Y', strtotime($orders->dt_book_upto . ' +1 day ')));
                $GetOrderedProductsArr = $this->getProductsByOrderID($orders->id);
                if (count($GetOrderedProductsArr) > 0) {
                    foreach ($GetOrderedProductsArr as $orderedProds) {
                        array_push($rec, 'SKU: ' . $orderedProds->product_sku . ', Quantity: ' . $orderedProds->quantity);
                    }
                }
                fputcsv($file, $rec);
            }
        }else{
            $mail = false;
        }
if($mail){
    // Place stream pointer at beginning
    rewind($file);

    // Return the data
    return stream_get_contents($file);
}else{
    exit();
}

    }

    public function disputedOrders(Request $request)
    {
        View::share('title', 'Disputed Orders Listing');
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        $ordersAry = DB::table($this->DBTables['Pre_Orders'])
            ->where(function ($query) {
                $query->where('payment_option','=', 1)
                    ->orWhere('payment_option', '=', 2);
            })->where(function ($query) {
                $query->where('payment_in_progress','=', 1);
            })->orderBy('id','DESC')
            ->get();
        return view('pages.orders.disputed_orders_listing', compact('ordersAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function getProductsByOrderID($orderId)
    {
        $OrderedProducts = DB::table($this->DBTables['Orders_Products'])
            ->where('order_id', '=', $orderId)
            ->get();
        return $OrderedProducts;
    }

    public function getStateByID($id)
    {
        $DBTables = Config::get('constants.DbTables');
        $state = DB::table($DBTables['States'])
            ->where('id', '=', $id)
            ->get();
        return $state[0];
    }

    public function getCountryByID($id)
    {
        $DBTables = Config::get('constants.DbTables');
        $countries = DB::table($DBTables['Countries'])
            ->where('id', '=', $id)
            ->get();
        return $countries[0];
    }

    public function show($idOrder = 0)
    {
        View::share('title', 'Order Details');

        if ((int)$idOrder > 0) {
            $DBTables = Config::get('constants.DbTables');
            $t_ord = $DBTables['Orders'];
            $t_ord_pro = $DBTables['Orders_Products'];

            $orderDetails = DB::table($t_ord)
                ->join($DBTables['Countries'], $DBTables['Countries'] . '.id', '=', $t_ord . '.buyer_country')
                ->where($t_ord . '.order_status', '>', 1)
                ->where($t_ord . '.id', '=', $idOrder)
                ->select($t_ord . '.*', $DBTables['Countries'] . '.name as buyer_country_name')
                ->get();

            $orderProductAry = DB::table($t_ord_pro)
                ->where('order_id', '=', $idOrder)
                ->select(
                    'product_id',
                    'is_sale_product',
                    'product_name',
                    'product_description',
                    'product_sku',
                    'quantity',
                    'sub_total_amnt',
                    'product_attributes'
                )
                ->get();
            $servicingDays = Config::get('constants.stateServicingDays');
            if (count($orderDetails) > 0) {
                if (trim($orderDetails[0]->partner_ref_key) != '') {
                    $partner = DB::table($DBTables['Partners'])
                        ->where('reference_id', '=', trim($orderDetails[0]->partner_ref_key))
                        ->select('name', 'email')
                        ->get();
                    if (count($partner) > 0) {
                        $orderDetails[0]->partner_name = $partner[0]->name;
                        $orderDetails[0]->partner_email = $partner[0]->email;
                    }

                }
                $extendedDays = $servicingDays[$orderDetails[0]->state_id];
            }
            $orderDetailsData = $orderDetails[0];
            return view('pages.orders.order_details', compact('orderDetailsData', 'orderProductAry','extendedDays'));
        } else {
            return redirect()->to("/admin_orders");
        }
    }

    public function viewDisputedOrders($idOrder = 0){
        View::share('title', 'Disputed Order Details');
        if ((int)$idOrder > 0) {
            $DBTables = Config::get('constants.DbTables');
            $t_ord = $DBTables['Pre_Orders'];
            $t_ord_pro = $DBTables['Pre_Orders_Products'];

            $orderDetails = DB::table($t_ord)
                ->join($DBTables['Countries'], $DBTables['Countries'] . '.id', '=', $t_ord . '.buyer_country')
                ->where($t_ord . '.payment_in_progress', '=', 1)
                ->where($t_ord . '.id', '=', $idOrder)
                ->select($t_ord . '.*', $DBTables['Countries'] . '.name as buyer_country_name')
                ->get();

            $orderProductAry = DB::table($t_ord_pro)
                ->where('pre_order_id', '=', $idOrder)
                ->get();
//            dd($orderedProductAry);
            if (count($orderProductAry) > 0) {
                $i = 0;
                foreach ($orderProductAry as $key => $ordProds){
                    $prodDet = $this->getProductDetailsById($ordProds->product_id);
                    if(count($prodDet)>0){
                        $orderProductAry[$key]->name = $prodDet->name;
                        $orderProductAry[$key]->description = $prodDet->description;
                        $orderProductAry[$key]->sku = $prodDet->sku;
                        $orderProductAry[$key]->product_type = $prodDet->product_type;
                        if(count($prodDet)>0 && $prodDet->product_type == 5){
                            $attribDets = $this->getProdAttribsByProdId($ordProds->product_id);
                            $orderProductAry[$key]->attrib_arr = $attribDets;
                        }else{
                            $orderProductAry[$key]->attrib_arr = '';
                        }
                    }
                }
            }
//            dd($orderProductAry);
            if (count($orderDetails) > 0) {
                if (trim($orderDetails[0]->partner_ref_key) != '') {
                    $partner = DB::table($DBTables['Partners'])
                        ->where('reference_id', '=', trim($orderDetails[0]->partner_ref_key))
                        ->select('name', 'email')
                        ->get();
                    if (count($partner) > 0) {
                        $orderDetails[0]->partner_name = $partner[0]->name;
                        $orderDetails[0]->partner_email = $partner[0]->email;
                    }

                }
            }
            $orderDetailsData = $orderDetails[0];
            return view('pages.orders.disputed_order_details', compact('orderDetailsData', 'orderProductAry'));
        } else {
            return redirect()->to("/disputed_orders");
        }
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
    public function getOrderStatus()
    {
        $DBTables = Config::get('constants.DbTables');
        return DB::table($DBTables['Order_Status'])->orderBy('id', 'ASC')->get();
    }

    public function searchOrder(Request $request)
    {
        View::share('title', 'Order Search');
        $DBTables = Config::get('constants.DbTables');
        $statusAry = $this->getOrderStatus();
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');

        $previousSearchAry = session('searchOrder');
        if ($previousSearchAry && !$request->searchAry) {
            $request->searchAry = $previousSearchAry;
        }

        if ($request->searchAry) {
            session()->flash('searchOrder', $request->searchAry); // Store it as flash data.

            $whereAry = array();
            $whereJoinAry = array();

            if (trim($request->searchAry['filter_status']) != '') {
                $whereAry[0] = array('order_status', '=', (int)$request->searchAry['filter_status']);
                $whereJoinAry[0] = array($DBTables['Orders'] . '.order_status', '=', (int)$request->searchAry['filter_status']);
            }

            if (trim($request->searchAry['filter_date']) != '') {
                $orderFromDate = date('Y-m-d 00:00:00', strtotime($request->searchAry['filter_date']));
                $orderToDate = date('Y-m-d 23:59:59', strtotime($request->searchAry['filter_date']));

                $whereAry[2] = array('payment_date', '>=', $orderFromDate);
                $whereJoinAry[2] = array($DBTables['Orders'] . '.payment_date', '>=', $orderFromDate);
                $whereAry[3] = array('payment_date', '<=', $orderToDate);
                $whereJoinAry[3] = array($DBTables['Orders'] . '.payment_date', '<=', $orderToDate);

            }

            if (trim($request->searchAry['filter_user']) != '') {
                if (trim($request->searchAry['filter_product_type']) != '') {
                    $whereJoinAry[2] = array($DBTables['Orders_Products'] . '.is_sale_product', '=', (int)$request->searchAry['filter_product_type']);
                    $ordersAry = DB::table($DBTables['Orders'])
                        ->join($DBTables['Orders_Products'], $DBTables['Orders_Products'] . '.order_id', '=', $DBTables['Orders'] . '.id')
                        ->where($whereJoinAry)
                        ->where($DBTables['Orders'] . '.user_name', 'like', "%" . trim($request->searchAry['filter_user']) . "%")
                        ->orWhere($DBTables['Orders'] . '.user_email', 'like', "%" . trim($request->searchAry['filter_user']) . "%")
                        ->orderBy($DBTables['Orders'] . '.id', 'DESC')
                        ->groupBy($DBTables['Orders_Products'] . '.order_id')
                        ->paginate($rowsPerPage);
                } else {
                    $ordersAry = DB::table($DBTables['Orders'])
                        ->where($whereAry)
                        ->where('user_name', 'like', "%" . trim($request->searchAry['filter_user']) . "%")
                        ->orWhere('user_email', 'like', "%" . trim($request->searchAry['filter_user']) . "%")
                        ->orderBy('id', 'DESC')
                        ->paginate($rowsPerPage);
                }
            } else {
                /*if (trim($request->searchAry['filter_product_type']) != '') {
                    $whereJoinAry[2] = array($DBTables['Orders_Products'] . '.is_sale_product', '=', (int)$request->searchAry['filter_product_type']);
                    $ordersAry = DB::table($DBTables['Orders'])
                        ->join($DBTables['Orders_Products'], $DBTables['Orders_Products'] . '.order_id', '=', $DBTables['Orders'] . '.id')
                        ->where($whereJoinAry)
                        ->orderBy($DBTables['Orders'] . '.id', 'DESC')
                        ->groupBy($DBTables['Orders_Products'] . '.order_id')
                        ->paginate($rowsPerPage);
                } else {*/
                    $ordersAry = DB::table($DBTables['Orders'])
                        ->where($whereAry)
                        ->orderBy('id', 'DESC')
                        ->paginate($rowsPerPage);
//                }
            }


        } else {
            $ordersAry = Orders::orderBy('id', 'DESC')->where('order_status', '>', 1)->paginate($rowsPerPage);
        }

        return view('pages.orders.orders_listing', compact('ordersAry', 'statusAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function updateOrderStatus(Request $request)
    {
        $DBTables = Config::get('constants.DbTables');

        $idOrder = (int)$request->idOrder;
        $idStatus = (int)$request->idStatus;
        $cancelDescription = $request->cancelDescription;
        $updateAry = array();
        $updateAry['order_status'] = (int)$idStatus;
        if (trim($cancelDescription) != '') {
            $updateAry['cancelDescription'] = trim($cancelDescription);
        }

        if ($idOrder > 0 && $idStatus > 0) {
            DB::table($DBTables['Booked_Products'])->where('order_id', '=', $idOrder)->delete();
            DB::table($DBTables['Orders'])->where('id', $idOrder)->update($updateAry);
            return redirect()->to("/admin_orders")
                ->with('success', 'Order status successfully updated.');
        } else {
            return redirect()->to("/admin_orders")
                ->with('error', 'Something is wrong, status not updated successfully.');
        }
    }

    public function getParentProductByChildId($ChildProdId)
    {
        $ParentProdArr = DB::table($this->DBTables['Group_Products'] . ' as GP')
            ->join($this->DBTables['Products'] . ' as P', 'GP.parent_productid', '=', 'P.id')
            ->where('GP.product_id', '=', (int)$ChildProdId)
            ->select('P.*')
            ->get();

        return $ParentProdArr;
    }

    public function getBookedChildProducts($parentProdId)
    {
        $ChildProdArr = DB::table($this->DBTables['Group_Products'] . ' as GP')
            ->join($this->DBTables['Products'] . ' as P', 'GP.product_id', '=', 'P.id')
            ->join($this->DBTables['Booked_Products'] . ' as BP', 'BP.product_id', '=', 'P.id')
            ->where('GP.parent_productid', '=', (int)$parentProdId)
            ->whereDate('BP.dt_booked_upto', '>=', date('Y-m-d'))
            ->select('P.*')
            ->get();

        return $ChildProdArr;
    }

    public function getUnorderedProds(Request $request){
        $parProdId = $this->getParentProductByChildId($request->prodid);
        $bookedChilds = $this->getBookedChildProducts($parProdId[0]->id);
        $bookedProdsArr = array();
        if(count($bookedChilds)>0){
            foreach ($bookedChilds as $childProds){
                array_push($bookedProdsArr,$childProds->id);
            }
        }
        $ChildProdArr = DB::table($this->DBTables['Group_Products'] . ' as GP')
            ->join($this->DBTables['Products'] . ' as P', 'GP.product_id', '=', 'P.id')
            ->where('GP.parent_productid', '=', (int)$parProdId[0]->id)
            ->whereNotIn('P.id', $bookedProdsArr)
            ->select('P.*')
            ->get();
        $validProdsArr = array();
        if(count($ChildProdArr)>0){
            $i = 0;
            foreach ($ChildProdArr as $prodList){
               $validProdsArr[$i]['id'] = $prodList->id;
               $validProdsArr[$i]['name'] = $prodList->name;
               $i++;
            }
        }
        return $validProdsArr;
    }

    public function changeOrderItem(Request $request){
        $result = DB::table($this->DBTables['Booked_Products'])
            ->where([['order_id', '=', $request->orderid], ['product_id', '=', $request->oldprodid]])
            ->update(['product_id' => $request->productid]);
        if($result){
            $prodDet = $this->getProductDetailsById($request->productid);
            $result1 = DB::table($this->DBTables['Orders_Products'])
                ->where([['order_id', '=', $request->orderid], ['product_id', '=', $request->oldprodid]])
                ->update(['product_id' => $request->productid,'product_name' =>$prodDet->name,
                    'product_description' =>$prodDet->description,'product_sku' =>$prodDet->sku]);
            if($result1){
                die('SUCCESS');
            }
        }
    }

}
