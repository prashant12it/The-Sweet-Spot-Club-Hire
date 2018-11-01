@extends('layouts.dashboard')

@section('content')
<section id="content">
    <div class="panel">
        <div class="panel mb25 mt5">
            <div class="panel-heading">
                <span class="panel-title hidden-xs">
                    <strong> Order ID</strong> #{{$orderDetails->order_reference_id}}
                    &nbsp;&nbsp;&nbsp;&nbsp;</span>
                <ul class="nav panel-tabs-border panel-tabs">
                    <li class="active">
                        <a href="#customer_tab" data-toggle="tab" aria-expanded="false">Customer</a>
                    </li>
                    <li class="">
                        <a href="#product_tab" data-toggle="tab" aria-expanded="true">Products</a>
                    </li>
                    <li class="">
                        <a href="#payment_tab" data-toggle="tab" aria-expanded="false">Payment</a>
                    </li>
                    <li class="">
                        <a href="#shipping_tab" data-toggle="tab" aria-expanded="false">Shipping</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body p25 pb5">
                <div class="tab-content pn br-n admin-form">
                    
                    <!--START CUSTOMER DETAILS DIV-->
                    <div id="customer_tab" class="tab-pane active">

                        <div class="panel">
                            <div class="panel-heading zero-padding">
                                        <span class="panel-icon">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                        </span>
                                <span class="panel-title"> Details</span>
                            </div>
                            <div class="panel-body pn">
                                <table class="table order-details-table">
                                    <thead class="hidden">
                                    <tr>
                                        <th width="100%">Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <strong>Customer Name : </strong>{{$orderDetails->user_name}}<br/>
                                            <strong>Customer Email : </strong>{{$orderDetails->user_email}}<br/>
                                            <strong>Customer Phone No. : </strong>{{$orderDetails->user_phone}}<br/>
                                            @if(!empty($orderDetails->here_abt_us))
                                                <strong>Where did you hear about us ? : </strong>
                                                {{$orderDetails->here_abt_us}}<br/>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <!--END CUSTOMER DEATILS DIV-->
                    
                    <!--START PRODUCT DETAILS DIV-->
                    <div id="product_tab" class="tab-pane">

                                <div class="panel">
                                    <div class="panel-heading zero-padding">
                                        <span class="panel-icon">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                        </span>
                                        <span class="panel-title"> Product Details</span>
                                    </div>
                                    <div class="panel-body pn">
                                        <table class="table order-details-table">
                                            <thead class="hidden">
                                            <tr>
                                                <th width="100%">Product Details</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if ($orderProductAry->count() > 0)
                                                @foreach ($orderProductAry as $orderProduct)
                                            <tr>
                                                <td>
                                                    <strong>Bag Title : </strong>{{$orderProduct->bag_title}}<br/>
                                                    <strong>Size : </strong>{{$orderProduct->product_name}}<br/>
                                                    <strong>Price : </strong>{{$orderProduct->sub_total_amnt_out + $orderProduct->sub_total_amt_ret}}<br/>
                                                </td>
                                            </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>No Product found.
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                    </div>
                    <!--END PRODUCT DETAILS DIV-->
                    
                    <!--START PAYMENT DETAILS DIV-->
                    <div id="payment_tab" class="tab-pane">
                        <div class="panel">
                            <div class="panel-body pn">
                                <table class="table order-details-table">
                                    <thead>
                                        <tr class="hidden">
                                            <th width="50%">Payment Details</th>
                                            <th width="50%">Buyer Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="50%">
                                                <strong>Invoice : </strong>{{$orderDetails->invoice_no}}<br/><br/>
                                                <strong>Transaction ID : </strong>{{$orderDetails->payment_transaction_id}}<br/><br/>
                                                <strong>Paid Date : </strong><?php echo date('M d Y',strtotime($orderDetails->payment_date));?><br/><br/>
                                                <strong>Buyer Name : </strong>{{$orderDetails->user_name}}<br/><br/>
                                                <strong>Buyer Email : </strong>{{$orderDetails->user_email}}<br/><br/>
                                                <strong>Payment Gateway : </strong>{{($orderDetails->payment_option == 1?'Pay Dollar':($orderDetails->payment_option == 2?'NAB Transact':'Stripe'))}}<br/><br/>
                                                <strong>{{($orderDetails->payment_option == 3? 'Payer ID':($orderDetails->payment_option == 1?'Merchant Id':($orderDetails->payment_option == 2?'Vendor Id':'N/A')))}} : </strong>{{$orderDetails->merchant_email}}<br/><br/>

                                            </td>
                                            <td width="50%">
                                                <strong>Outgoing Shipping Courier Charges($) : </strong>{{number_format(($orderDetails->outgoing_shipment==2?20:0),2)}}<br/><br/>
                                                <strong>Multiset Discount($) : </strong>{{number_format(($orderDetails->multiset_discount),2)}}<br/><br/>
                                                @if(!empty($orderDetails->return_region))
                                                <strong>Return Shipping Courier Charges($) : </strong>{{number_format(($orderDetails->return_shipment==2?20:0),2)}}<br/><br/>
                                                @endif
                                                <strong>Sub Total Amount($) : </strong>{{number_format($orderDetails->sub_total_amnt,2)}}<br/><br/>
                                                @if(trim($orderDetails->offer_name)!= '')
                                                <strong>Offer : </strong>{{$orderDetails->offer_name}}<br/><br/>
                                                <strong>Offer Code: </strong>{{$orderDetails->offer_Code}}<br/><br/>
                                                <strong>Offer Amount($) : </strong>{{number_format($orderDetails->offer_amnt,2)}}<br/><br/>
                                                @endif
                                                <strong>Paid Amount($) : </strong>{{number_format($orderDetails->paid_amnt ,2)}}<br/><br/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--END PAYMENT DETAILS DIV-->
                    
                    <!--START SHIPPING DETAILS DIV-->
                    <div id="shipping_tab" class="tab-pane">
                        <div class="panel">
                            <div class="panel-body pn">
                                <table class="table order-details-table">
                                    <thead>
                                        <tr>
                                            <th width="50%">Pickup Details</th>
                                            <th width="50%">Destination Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="50%">
                                                <strong>Region of pickup : </strong>{{$orderDetails->pickup_region}}<br/>
                                                <strong>Company Name : </strong>{{$orderDetails->pickup_company_name}}<br/>
                                                <strong>Contact Name : </strong>{{$orderDetails->pickup_contact_name}}<br/>
                                                <strong>Contact Phone No. : </strong>{{$orderDetails->pickup_phone_num}}<br/>
                                                <strong>Address : </strong>{{$orderDetails->pickup_address}}<br/>
                                                <strong>Suburb : </strong>{{$orderDetails->pickup_suburb}}<br/>
                                                <strong>Postcode : </strong>{{$orderDetails->pickup_postal_code}}<br/>
                                                <strong>Pickup collection date : </strong>{{date('jS M Y',strtotime($orderDetails->pickup_date))}}<br/>
                                                <strong>Pickup collection note : </strong>{{$orderDetails->pickup_delivery_note}}<br/>
                                            </td>
                                            <td width="50%">
                                                <strong>Region of drop off : </strong>{{$orderDetails->destination_region}}<br/>
                                                <strong>Company Name : </strong>{{$orderDetails->destination_company_name}}<br/>
                                                <strong>Contact Name : </strong>{{$orderDetails->destination_contact_name}}<br/>
                                                <strong>Contact Phone No. : </strong>{{$orderDetails->destination_phone_num}}<br/>
                                                <strong>Address : </strong>{{$orderDetails->destination_address}}<br/>
                                                <strong>Suburb : </strong>{{$orderDetails->destination_suburb}}<br/>
                                                <strong>Postcode : </strong>{{$orderDetails->destination_postal_code}}<br/>
                                                <strong>Destination delivery note : </strong>{{$orderDetails->destination_note}}<br/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                @if(!empty($orderDetails->return_region))
                                    <h2 style="margin-left: 10px">Retuen Shipping</h2>
                                    <table class="table order-details-table">
                                        <thead>
                                        <tr><th colspan="2"></th> </tr>
                                        <tr>
                                            <th width="50%">Pickup Details</th>
                                            <th width="50%">Destination Details</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td width="50%">
                                                <strong>Region of pickup : </strong>{{$orderDetails->return_region}}<br/>
                                                <strong>Company Name : </strong>{{$orderDetails->return_company_name}}<br/>
                                                <strong>Contact Name : </strong>{{$orderDetails->return_contact_name}}<br/>
                                                <strong>Contact Phone No. : </strong>{{$orderDetails->return_phone_num}}<br/>
                                                <strong>Address : </strong>{{$orderDetails->return_address}}<br/>
                                                <strong>Suburb : </strong>{{$orderDetails->return_suburb}}<br/>
                                                <strong>Postcode : </strong>{{$orderDetails->return_postal_code}}<br/>
                                                <strong>Pickup collection date : </strong>{{date('jS M Y',strtotime($orderDetails->return_date))}}<br/>
                                                <strong>Pickup collection note : </strong>{{$orderDetails->return_collection_note}}<br/>
                                            </td>
                                            <td width="50%">
                                                <strong>Region of drop off : </strong>{{$orderDetails->return_d_region}}<br/>
                                                <strong>Company Name : </strong>{{$orderDetails->return_d_company_name}}<br/>
                                                <strong>Contact Name : </strong>{{$orderDetails->return_d_contact_name}}<br/>
                                                <strong>Contact Phone No. : </strong>{{$orderDetails->return_d_phone_num}}<br/>
                                                <strong>Address : </strong>{{$orderDetails->return_d_address}}<br/>
                                                <strong>Suburb : </strong>{{$orderDetails->return_d_suburb}}<br/>
                                                <strong>Postcode : </strong>{{$orderDetails->return_d_postal_code}}<br/>
                                                <strong>Destination delivery note : </strong>{{$orderDetails->return_d_note}}<br/>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--END SHIPPING DETAILS DIV-->
                    
                    
                </div>
            </div>
            
            <div class="panel-footer clearfix">
                <div class="col-md-10"></div>
                <div class="col-md-2"><a href="{{ url('/club_courier_orders') }}" class="button form-control btn-info pull-right"><i class="fa fa-reply" aria-hidden="true"></i> Back to Orders</a></div>
            </div>
        </div>
    </div>

</section>
@endsection
