<?php

namespace App\Http\Controllers;

use App;
use App\ClubcourierOrders;
use Config;
use DB;
use Illuminate\Http\Request;
use View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminCCOrdersController extends Controller
{
    public function __construct()
    {
        $this->DBTables = Config::get('constants.DbTables');
        DB::enableQueryLog();
    }

    public function index(Request $request)
    {
        View::share('title', 'Club Courier Orders Listing');
        session()->flash('searchOrder', null);
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        $statusAry = $this->getOrderStatus();
        $ordersAry = ClubcourierOrders::orderBy('id', 'DESC')->where('order_status', '>', 1)->paginate($rowsPerPage);
        return view('pages.ccorders.orders_listing', compact('ordersAry', 'statusAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function courier($mail = false)
    {
        if (!$mail) {
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=clubcourier.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
        }
        View::share('title', 'Club Courier');
        $tomorrow = date('Y-m-d', strtotime('+1 days'));
        $OneWayPickupOrdersAry = ClubcourierOrders::where([['pickup_date', 'Like', '%' . $tomorrow . '%'], ['order_status', '=', 2]])->orderBy('id', 'DESC')->get();

        $ReturnPickupOrdersAry = ClubcourierOrders::where([['return_date', 'Like', '%' . $tomorrow . '%'], ['order_status', '=', 2]])->orderBy('id', 'DESC')->get();

        $getProdCountArr = DB::table($this->DBTables['CCOrders_Products'])
            ->select(DB::raw('count(*) as recount'))
            ->groupBy('order_id')
            ->get();
        $prodCount = array();
        foreach ($getProdCountArr as $prods) {
            array_push($prodCount, $prods->recount);
        }
        if (count($prodCount) > 0) {
            $maxProdCount = max($prodCount);
        } else {
            $maxProdCount = 0;
        }

        $list = array("Order Reference ID", "Order Date", "Customer", "Total Order Amount($)", "Customer Email", "Transaction ID", "Paid Date", "Buyer Name", "Buyer Email", "Mobile No.",
            "Region of Pickup",
            "Pickup Company Name",
            "Pickup Contact Name",
            "Pickup Contact Phone No.",
            "Pickup Address",
            "Pickup Suburb",
            "Pickup Postcode",
            "Pickup collection date",
            "Collection Note",
            "Region of Drop Off",
            "Destination Company Name",
            "Destination Contact Name",
            "Destination Contact Phone No.",
            "Destination Address",
            "Destination Suburb",
            "Destination Postcode",
            "Delivery Note",
            "Courier Option");
        for ($i = 1; $i <= $maxProdCount; $i++) {
            array_push($list, 'Product ' . $i);
        }
        if ($mail) {
            $file = fopen('php://temp', 'w+');
        } else {
            $file = fopen('php://output', 'w');
        }
        fputcsv($file, $list);
        if (count($OneWayPickupOrdersAry) > 0) {
            foreach ($OneWayPickupOrdersAry as $orders) {
                $rec = array($orders->order_reference_id, date('d/m/Y', strtotime($orders->dtCreatedOn)), $orders->user_name, number_format($orders->total_amnt, 2),
                    $orders->user_email, $orders->payment_transaction_id, date('d/m/Y', strtotime($orders->payment_date)), $orders->user_name, $orders->user_email,
                    $orders->user_phone, $orders->pickup_region, $orders->pickup_company_name,
                    $orders->pickup_contact_name, $orders->pickup_phone_num, $orders->pickup_address,
                    $orders->pickup_suburb, $orders->pickup_postal_code,
                    date('d/m/Y', strtotime($orders->pickup_date)), $orders->pickup_delivery_note,
                    $orders->destination_region, $orders->destination_company_name,
                    $orders->destination_contact_name, $orders->destination_phone_num, $orders->destination_address,
                    $orders->destination_suburb, $orders->destination_postal_code, $orders->destination_note, ($orders->outgoing_shipment == 2 ? 'Express ($20 additional charge)' : 'Standard'));
                $GetOrderedProductsArr = $this->getProductsByOrderID($orders->id);
                if (count($GetOrderedProductsArr) > 0) {
                    foreach ($GetOrderedProductsArr as $orderedProds) {
                        array_push($rec, 'Bag Title: ' . $orderedProds->bag_title . ', Size: ' . $orderedProds->product_name . ', Price: $' . $orderedProds->sub_total_amnt_out);
                    }
                }
                fputcsv($file, $rec);
            }
        }
        if (count($ReturnPickupOrdersAry) > 0) {
            foreach ($ReturnPickupOrdersAry as $orders) {
                $rec = array($orders->order_reference_id, date('d/m/Y', strtotime($orders->dtCreatedOn)), $orders->user_name, number_format($orders->total_amnt, 2),
                    $orders->user_email, $orders->payment_transaction_id, date('d/m/Y', strtotime($orders->payment_date)), $orders->user_name, $orders->user_email,
                    $orders->user_phone, $orders->return_region, $orders->return_company_name,
                    $orders->return_contact_name, $orders->return_phone_num, $orders->return_address,
                    $orders->return_suburb, $orders->return_postal_code,
                    date('d/m/Y', strtotime($orders->return_date)), $orders->return_collection_note,
                    $orders->return_d_region, $orders->return_d_company_name,
                    $orders->return_d_contact_name, $orders->return_d_phone_num, $orders->return_d_address,
                    $orders->return_d_suburb, $orders->return_d_postal_code, $orders->return_d_note, ($orders->return_shipment == 2 ? 'Express ($20 additional charge)' : 'Standard'));
                $GetOrderedProductsArr = $this->getProductsByOrderID($orders->id);
                if (count($GetOrderedProductsArr) > 0) {
                    foreach ($GetOrderedProductsArr as $orderedProds) {
                        array_push($rec, 'Bag Title: ' . $orderedProds->bag_title . ', Size: ' . $orderedProds->product_name . ', Price: $' . $orderedProds->sub_total_amt_ret);
                    }
                }
                fputcsv($file, $rec);
            }
        }
        if (count($OneWayPickupOrdersAry) <= 0 && count($ReturnPickupOrdersAry) <= 0) {
            $mail = false;
        }
        if ($mail) {
            // Place stream pointer at beginning
            rewind($file);

            // Return the data
            return stream_get_contents($file);
        } else {
            exit();
        }

    }

    public function PickupMailCron(){

        $mail = new PHPMailer(true);
        $fromEmail = Config::get('constants.supportEmailProduction');
        $attach = $this->courier(true);
        if($attach){
            $handle = fopen (public_path('../pickup-data.csv'), "w+");
            fclose($handle);
            file_get_contents(public_path('../pickup-data.csv'));
            file_put_contents(public_path('../pickup-data.csv'),$attach);
            file_put_contents('../couriers/clubcourier-mail-'.date('d-m-Y-h-i-s').'.csv', $attach, FILE_APPEND);
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
                $mail->Subject = 'TSS Club Courier - Pickup Club Courier Booking Details '.date('d/m/Y',strtotime('+1 day'));
                $mail->MsgHTML('Dear sir/mam <br /><p>Please find attached The Sweet Spot Club Courier details for those bookings whose pickup is on '.date('d/m/Y',strtotime('+1 day')).'</p>');
                $mail->addAttachment(public_path('../pickup-data.csv'));
                $mail->addAddress('info@tssclubhire.com' ,'Info');
                $mail->addAddress('hello@lucasarthur.net.au' ,'Luca');
                /*$mail->addAddress('prashant@whiz-solutions.com' ,'Info');
                $mail->addAddress('prashant12it@gmail.com' ,'Luca');*/
                $mail->send();
            }catch(Exception $e){
                dd($e);
            }
        }
    }

    public function disputedOrders(Request $request)
    {
        View::share('title', 'Club Courier Disputed Orders Listing');
        $rowsPerPage = Config::get('constants.PaginationRowsPerPage');
        $ordersAry = DB::table($this->DBTables['CCOrders'])
            ->where(function ($query) {
                $query->where('payment_option', '=', 3)
                    ->orWhere('payment_option', '=', 2)
                    ->orWhere('payment_option', '=', 1);
            })->where(function ($query) {
                $query->where('order_status', '=', 5);
            })->orderBy('id', 'DESC')
            ->get();
        return view('pages.ccorders.disputed_orders_listing', compact('ordersAry'))
            ->with('i', ($request->input('page', 1) - 1) * $rowsPerPage);
    }

    public function getProductsByOrderID($orderId)
    {
        $OrderedProducts = DB::table($this->DBTables['CCOrders_Products'])
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
        View::share('title', 'Club Courier Order Details');

        if ((int)$idOrder > 0) {
            $DBTables = Config::get('constants.DbTables');
            $t_ord = $DBTables['CCOrders'];
            $t_ord_pro = $DBTables['CCOrders_Products'];

            $orderDetailsArr = DB::table($t_ord)
                ->where('order_status', '>', 1)
                ->where('order_status', '<', 5)
                ->where('id', '=', $idOrder)
                ->get();
            $orderDetails = $orderDetailsArr[0];
            $orderProductAry = DB::table($t_ord_pro)
                ->where('order_id', '=', $idOrder)
                ->select(
                    'product_name',
                    'bag_title',
                    'quantity',
                    'sub_total_amnt_out',
                    'sub_total_amt_ret'
                )
                ->get();
            return view('pages.ccorders.order_details', compact('orderDetails', 'orderProductAry'));
        } else {
            return redirect()->to("/club_courier_orders");
        }
    }

    public function viewDisputedOrders($idOrder = 0)
    {
        View::share('title', 'Club Courier Disputed Order Details');
        if ((int)$idOrder > 0) {
            $DBTables = Config::get('constants.DbTables');
            $t_ord = $DBTables['CCOrders'];
            $t_ord_pro = $DBTables['CCOrders_Products'];

            $orderDetailsArr = DB::table($t_ord)
                ->where('order_status', '=', 5)
                ->where('id', '=', $idOrder)
                ->get();

            $orderDetails = $orderDetailsArr[0];
            $orderProductAry = DB::table($t_ord_pro)
                ->where('order_id', '=', $idOrder)
                ->get();
//
            return view('pages.ccorders.disputed_order_details', compact('orderDetails', 'orderProductAry'));
        } else {
            return redirect()->to("/club_courier_disputed_orders");
        }
    }

    public function getOrderStatus()
    {
        $DBTables = Config::get('constants.DbTables');
        return DB::table($DBTables['Order_Status'])->orderBy('id', 'ASC')->get();
    }

    public function searchOrder(Request $request)
    {
        View::share('title', 'Club Courier Order Search');
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
                $whereJoinAry[0] = array($DBTables['CCOrders'] . '.order_status', '=', (int)$request->searchAry['filter_status']);
            }

            if (trim($request->searchAry['filter_date']) != '') {
                $orderFromDate = date('Y-m-d 00:00:00', strtotime($request->searchAry['filter_date']));
                $orderToDate = date('Y-m-d 23:59:59', strtotime($request->searchAry['filter_date']));

                $whereAry[2] = array('payment_date', '>=', $orderFromDate);
                $whereJoinAry[2] = array($DBTables['CCOrders'] . '.payment_date', '>=', $orderFromDate);
                $whereAry[3] = array('payment_date', '<=', $orderToDate);
                $whereJoinAry[3] = array($DBTables['CCOrders'] . '.payment_date', '<=', $orderToDate);

            }

            if (trim($request->searchAry['filter_user']) != '') {
                $ordersAry = DB::table($DBTables['CCOrders'])
                    ->where($whereAry)
                    ->where('user_name', 'like', "%" . trim($request->searchAry['filter_user']) . "%")
                    ->orderBy('id', 'DESC')
                    ->paginate($rowsPerPage);
            } else {
                $ordersAry = DB::table($DBTables['CCOrders'])
                    ->where($whereAry)
                    ->orderBy('id', 'DESC')
                    ->paginate($rowsPerPage);
            }


        } else {
            $ordersAry = ClubcourierOrders::orderBy('id', 'DESC')->where([['order_status', '>', 1], ['order_status', '<', 5]])->paginate($rowsPerPage);
        }

        return view('pages.ccorders.orders_listing', compact('ordersAry', 'statusAry'))
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
            DB::table($DBTables['CCOrders'])->where('id', $idOrder)->update($updateAry);
            return redirect()->to("/club_courier_orders")
                ->with('success', 'Order status successfully updated.');
        } else {
            return redirect()->to("/club_courier_orders")
                ->with('error', 'Something is wrong, status not updated successfully.');
        }
    }

}
