@extends('layouts.dashboard')

@section('content')
    <section id="content">
        <div class="panel">
            <div class="panel mb25 mt5">
                <div class="panel-heading">
                <span class="panel-title hidden-xs">
                    <strong> Order Reference ID</strong> #{{$orderDetailsData->order_reference_id}}
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>(From :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_from))}}&nbsp;&nbsp;<strong>To :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto))}}<strong>&nbsp;)</strong>
                </span>
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
                                <div class="panel-body pn">
                                    <table class="table order-details-table">
                                        <thead>
                                        <tr class="hidden">
                                            <th width="100%">Customer</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <strong>Name : </strong>{{$orderDetailsData->buyer_first_name.' '.$orderDetailsData->buyer_last_name}}<br/>
                                                <strong>Email : </strong>{{$orderDetailsData->buyer_email}}<br/>
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
                            @if ($orderProductAry->count() > 0)
                                @foreach ($orderProductAry as $orderProduct)
                                    @if(!empty($orderProduct->name))
                                    <div class="panel">
                                        <div class="panel-heading zero-padding">
                                        <span class="panel-icon">
                                            <i class="fa fa-cubes" aria-hidden="true"></i>
                                        </span>
                                            <span class="panel-title"> {{$orderProduct->name}}</span>
                                        </div>
                                        <div class="panel-body pn">
                                            <table class="table order-details-table">
                                                <thead>
                                                <tr class="hidden">
                                                    <th width="25%">Product SKU</th>
                                                    <th width="20%">Product Quantity</th>
                                                    <th width="25%">Product Total Price</th>
                                                    <th width="30%">Attributes</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td width="25%">
                                                        <strong>SKU : </strong>{{$orderProduct->sku}}
                                                    </td>
                                                    <td width="20%">
                                                        <strong>Quantity : </strong>{{$orderProduct->quantity}}
                                                    </td>
                                                    <td width="25%">
                                                        <strong>Total Price($) : </strong>{{number_format($orderProduct->sub_total_amnt,2)}}
                                                    </td>
                                                    <td width="30%">
                                                        <strong>Attributes :</strong><br/>
                                                        @if(!empty($orderProduct->attrib_arr))
                                                            @foreach($orderProduct->attrib_arr as $attrivVal)
                                                            {{$attrivVal->attrib_name.': '.$attrivVal->value}}<br />
                                                            @endforeach
                                                            @endif
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            @else
                                <h3>No Product found.</h3>
                            @endif
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
                                                <strong>Buyer Name : </strong>{{$orderDetailsData->buyer_first_name ." ". $orderDetailsData->buyer_last_name}}<br/><br/>
                                                <strong>Buyer Email : </strong>{{$orderDetailsData->buyer_email}}<br/><br/>
                                                <strong>Buyer Country : </strong>{{$orderDetailsData->buyer_country_name}}<br/><br/>
                                                <strong>Payment Gateway : </strong>{{($orderDetailsData->payment_option == 1?'Pay Dollar':($orderDetailsData->payment_option == 2?'NAB Transact':'N/A'))}}<br/><br/>
                                                @if(trim($orderDetailsData->partner_ref_key) != '')
                                                    <strong>Partner Name : </strong>{{$orderDetailsData->partner_name}}<br/><br/>
                                                    <strong>Partner Email : </strong>{{$orderDetailsData->partner_email}}<br/><br/>
                                                    <strong>Partner Commission % : </strong>{{number_format($orderDetailsData->partner_cmsn_percnt,2)}}<br/><br/>
                                                    <strong>Partner Commission ($) : </strong>{{number_format($orderDetailsData->partner_cmsn_amt,2)}}<br/><br/>
                                                @endif
                                            </td>
                                            <td width="50%">
                                                <strong>Sub Total Amount($) : </strong>{{number_format($orderDetailsData->sub_total_amnt,2)}}<br/><br/>
                                                @if(trim($orderDetailsData->offer_Code)!= '')
                                                    <strong>Offer Code: </strong>{{$orderDetailsData->offer_Code}}<br/><br/>
                                                    <strong>Offer Amount($) : </strong>{{number_format($orderDetailsData->offer_amnt,2)}}<br/><br/>
                                                @endif
                                                <strong>Shipping Tax % : </strong>{{number_format($orderDetailsData->shipping_tax_percnt,2)}}<br/><br/>
                                                <strong>Shipping Amount($) : </strong>{{number_format($orderDetailsData->shipping_amnt,2)}}<br/><br/>
                                                <strong>Insurance Amount($) : </strong>{{number_format($orderDetailsData->insurance_amnt,2)}}<br/><br/>
                                                @if($orderDetailsData->signup_discount_amnt>0)
                                                    <strong>News Letter Discount($) : </strong>{{number_format($orderDetailsData->signup_discount_amnt,2)}}<br/><br/>
                                                @endif
                                                <strong>TSS Discount($) : </strong>{{number_format($orderDetailsData->tss,2)}}<br/><br/>
                                                @if((int)$orderDetailsData->partner_discount_percnt >0)
                                                    <strong>Partner Discount % : </strong>{{number_format($orderDetailsData->partner_discount_percnt,2)}}<br/><br/>
                                                    <strong>Partner Discount Amount($) : </strong>{{number_format($orderDetailsData->partner_discount_amnt,2)}}<br/><br/>
                                                @endif
                                                <strong>Total Amount($) : </strong>{{number_format($orderDetailsData->total_amnt ,2)}}<br/><br/>
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
                                            <th width="50%">Delivery(Dropoff)</th>
                                            <th width="50%">Pickup</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td width="50%">
                                            <!--                                                <strong>Delivery Date : </strong><?php echo date('M d Y H:i',strtotime($orderDetailsData->delvr_date_time));?><br/>-->
                                            <!--<strong>Delivery First Name : </strong>{{$orderDetailsData->delvr_first_name}}<br/>-->
                                            <!--<strong>Delivery Last Name : </strong>{{$orderDetailsData->delvr_last_name}}<br/>-->
                                            <!--<strong>Delivery Email : </strong>{{$orderDetailsData->delvr_email}}<br/>-->
                                                {{!(!empty($orderDetailsData->dropoff_place) && $orderDetailsData->dropoff_place == '1'?'<strong>Hotel</strong>':($orderDetailsData->dropoff_place == '2'?'<strong>Business</strong>':($orderDetailsData->dropoff_place == '3'?'<strong>Golf Course</strong>':'')))}}
                                                <strong>Delivery Hotel/Course Name : </strong>{{$orderDetailsData->delvr_hotel_name}}<br/>
                                            <!--<strong>Delivery Course Name : </strong>{{$orderDetailsData->delvr_course_name}}<br/>-->
                                            <!--<strong>Delivery Phone : </strong>{{$orderDetailsData->delvr_phone_num}}<br/>-->
                                                <strong>Delivery Address : </strong>{{$orderDetailsData->delvr_address}}<br/>
                                                {{--<strong>Delivery State : </strong>Roseburg<br/>--}}
                                                <strong>Delivery Postal Code : </strong>{{$orderDetailsData->delvr_postal_code}}<br/>
                                                <!--<strong>Delivery Country : </strong>USA<br/>-->
                                            </td>
                                            <td width="50%">
                                            <!--<strong>Pickup Date : </strong><?php echo date('M d Y H:i',strtotime($orderDetailsData->pickup_date_time));?><br/>-->
                                                {{!(!empty($orderDetailsData->pickup_place) && $orderDetailsData->pickup_place == '1'?'<strong>Hotel</strong>':($orderDetailsData->pickup_place == '2'?'<strong>Business</strong>':($orderDetailsData->pickup_place == '3'?'<strong>Golf Course</strong>':'')))}}
                                                <strong>Pickup Hotel/Course Name : </strong>{{$orderDetailsData->pickup_hotel_name}}<br/>
                                            <!--<strong>Pickup Course Name : </strong>{{$orderDetailsData->pickup_course_name}}<br/>-->
                                                <strong>Pickup Address : </strong>{{$orderDetailsData->pickup_address}}<br/>
                                                {{--<strong>Pickup State : </strong>Roseburg<br/>--}}
                                                <strong>Pickup Postal Code : </strong>{{$orderDetailsData->pickup_postal_code}}<br/>
                                                <!--<strong>Pickup Country : </strong>USA<br/>-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <strong>Additional Notes : </strong><br/>
                                                {{$orderDetailsData->additional_notes}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--END SHIPPING DETAILS DIV-->


                    </div>
                </div>

                <div class="panel-footer clearfix">
                    <div class="col-md-10"></div>
                    <div class="col-md-2"><a href="{{ url('/disputed_orders') }}" class="button form-control btn-info pull-right"><i class="fa fa-reply" aria-hidden="true"></i> Back to Orders</a></div>
                </div>
            </div>
        </div>
    </section>
@endsection
