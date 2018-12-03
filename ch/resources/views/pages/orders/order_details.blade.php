@extends('layouts.dashboard')

@section('content')
<section id="content">
    <div class="panel">
        <div class="panel mb25 mt5">
            <div class="panel-heading">
                <span class="panel-title hidden-xs">
                    <strong> Order ID</strong> #{{$orderDetailsData->id}} 
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
                        {{--<div class="row">
                            <div class="col-md-6">
                                <strong>Booking: (From :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_from))}}&nbsp;&nbsp;<strong>To :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto))}}<strong>&nbsp;)</strong>
                            </div>
                            <div class="col-md-6">
                                <strong>Transit/Cleaning: (From :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto))}}&nbsp;&nbsp;<strong>To :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto. ' +' . $extendedDays . ' days'))}}<strong>&nbsp;)</strong>
                            </div>
                        </div>--}}
                        <div class="panel">
                            <div class="panel-heading zero-padding">
                                        <span class="panel-icon">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        </span>
                                <span class="panel-title"> Club set unavailability</span>
                            </div>
                            <div class="panel-body pn">
                                <table class="table order-details-table">
                                    <thead>
                                    <tr>
                                        <th>Hire time </th>
                                        <th>Transit/Cleaned time</th>
                                        <th>Total Block off</th>
                                    </tr>
                                    <tr>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><strong>From :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_from))}}&nbsp;&nbsp;<strong>To :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto))}}</td>
                                        <td><strong>From :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto. ' + 1 day'))}}&nbsp;&nbsp;<strong>To :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto. ' +' . $extendedDays . ' days'))}}</td>
                                        <td><strong>From :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_from))}}&nbsp;&nbsp;<strong>To :&nbsp;</strong>{{date('M d Y',strtotime($orderDetailsData->dt_book_upto. ' +' . $extendedDays . ' days'))}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
                                            <strong>{{( (int)$orderDetailsData->is_partner_user == 1?'Partner':'Customer')}} ID : </strong>{{$orderDetailsData->user_id}}<br/>
                                            <strong>{{( (int)$orderDetailsData->is_partner_user == 1?'Partner':'Customer')}} Name : </strong>{{$orderDetailsData->user_name}}<br/>
                                            <strong>{{( (int)$orderDetailsData->is_partner_user == 1?'Partner':'Customer')}} Email : </strong>{{$orderDetailsData->user_email}}<br/>
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
                                <div class="panel">
                                    <div class="panel-heading zero-padding">
                                        <span class="panel-icon">
                                            <i class="fa fa-cubes" aria-hidden="true"></i>
                                        </span>
                                        <span class="panel-title"> {{$orderProduct->product_name}}</span>
                                        @if($orderProduct->is_sale_product != 1 )
                                            <button class="btn btn-success pull-right" style="margin-top: 5px; margin-right: 5px" onclick="changeProd({{$orderProduct->product_id}});">Change Product</button>
                                            @endif
                                    </div>
                                    <div class="panel-body pn">
                                        <table class="table order-details-table">
                                            <thead>
                                                <tr class="hidden">
                                                    <th width="20%">Product SKU</th>
                                                    <th width="15%">Product Category</th>
                                                    <th width="15%">Product Quantity</th>
                                                    <th width="20%">Product Total Price</th>
                                                    <th width="30%">Attributes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td width="10%">
                                                        <strong>SKU  </strong>{{$orderProduct->product_sku}}
                                                    </td>
                                                    <td width="10%">
                                                        <strong>Category  </strong>{{($orderProduct->is_sale_product == 1 ? "Sale":"Rent")}}
                                                    </td>
                                                    <td width="5%">
                                                        <strong>Quantity  </strong>{{$orderProduct->quantity}}
                                                    </td>
                                                    <td width="15%">
                                                        <strong>Total Price($)  </strong><br />{{number_format($orderProduct->sub_total_amnt,2)}}
                                                    </td>
                                                    <td width="20%">
                                                        <strong>Attributes </strong><br/>
                                                        <?php
                                                            $attributeAry = explode(";", $orderProduct->product_attributes);
                                                            $attribList = '';
                                                            if(!empty($attributeAry[0])){
                                                                for($i = 0;$i<5;$i++){
                                                                    $attribList .= str_replace(":", " - ", $attributeAry[$i]).", ";
                                                                }
                                                                $attribList = substr(trim($attribList),0,-1);
                                                                echo str_replace(", ", ",<br />", $attribList);
                                                            }
                                                        ?>
                                                    </td>
                                                    <td width="40%">
                                                        <strong>Block off - </strong>{{date('d/m/Y',strtotime($orderDetailsData->dt_book_from))}}&nbsp;&nbsp;To :&nbsp;{{date('d/m/Y',strtotime($orderDetailsData->dt_book_upto. ' +' . $extendedDays . ' days'))}}<br />
                                                        <strong>Hire time - </strong>{{date('d/m/Y',strtotime($orderDetailsData->dt_book_from))}}&nbsp;&nbsp;To :&nbsp;{{date('d/m/Y',strtotime($orderDetailsData->dt_book_upto))}}<br />
                                                        <strong>Transit/Cleaned time - </strong>{{date('d/m/Y',strtotime($orderDetailsData->dt_book_upto. ' + 1 day'))}}&nbsp;&nbsp;To :&nbsp;{{date('d/m/Y',strtotime($orderDetailsData->dt_book_upto. ' +' . $extendedDays . ' days'))}}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
                                                <strong>Invoice : </strong>{{$orderDetailsData->invoice_no}}<br/><br/>
                                                <strong>Transaction ID : </strong>{{$orderDetailsData->payment_transaction_id}}<br/><br/>
                                                <strong>Paid Date : </strong><?php echo date('M d Y',strtotime($orderDetailsData->payment_date));?><br/><br/>
                                                <strong>Buyer Name : </strong>{{$orderDetailsData->buyer_first_name ." ". $orderDetailsData->buyer_last_name}}<br/><br/>
                                                <strong>Buyer Email : </strong>{{$orderDetailsData->buyer_email}}<br/><br/>
                                                <strong>Buyer Country : </strong>{{$orderDetailsData->buyer_country_name}}<br/><br/>
                                                <strong>Payment Gateway : </strong>{{($orderDetailsData->payment_option == 1?'Pay Dollar':($orderDetailsData->payment_option == 2?'NAB Transact':'Stripe'))}}<br/><br/>
                                                @if(trim($orderDetailsData->partner_ref_key) != '')
                                                <strong>Partner Name : </strong>{{$orderDetailsData->partner_name}}<br/><br/>
                                                <strong>Partner Email : </strong>{{$orderDetailsData->partner_email}}<br/><br/>
                                                <strong>Partner Commission % : </strong>{{number_format($orderDetailsData->partner_cmsn_percnt,2)}}<br/><br/>
                                                <strong>Partner Commission ($) : </strong>{{number_format($orderDetailsData->partner_cmsn_amt,2)}}<br/><br/>
                                                @endif
                                            </td>
                                            <td width="50%">
                                                <strong>Sub Total Amount($) : </strong>{{number_format($orderDetailsData->sub_total_amnt,2)}} (Exclusive of shipping amount)<br/><br/>
                                                @if(trim($orderDetailsData->offer_name)!= '')
                                                <strong>Offer : </strong>{{$orderDetailsData->offer_name}}<br/><br/>
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
                                                <strong>Paid Amount($) : </strong>{{number_format($orderDetailsData->paid_amnt ,2)}}<br/><br/>
                                                <strong>{{($orderDetailsData->payment_option == 3? 'Payer ID':($orderDetailsData->payment_option == 1?'Merchant Id':($orderDetailsData->payment_option == 2?'Vendor Id':'N/A')))}} : </strong>{{$orderDetailsData->merchant_email}}<br/><br/>
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
                                        @if(!empty($orderDetailsData->here_abt_us))
                                            <tr>
                                                <td colspan="2">
                                                    <strong>Where did you hear about us ? : </strong>
                                                    {{$orderDetailsData->here_abt_us}}
                                                </td>
                                            </tr>
                                        @endif
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
                <div class="col-md-2"><a href="{{ url('/admin_orders') }}" class="button form-control btn-info pull-right"><i class="fa fa-reply" aria-hidden="true"></i> Back to Orders</a></div>
            </div>
        </div>
    </div>

    <div id="popupbs">
        <div class="modal fade" id="infomodal" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                        <h4 class="modal-title">Change Product</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-offset-2 col-sm-6">
                                    <label for="sel1">Choose Product</label>
                                    <select class="form-control" id="change_prod">
                                        <option>Choose Product</option>
                                    </select>
                                    <input type="hidden" name="oldprodid" id="oldprodid" value="0" />
                                </div>
                                <div class="col-sm-2">
                                    <br style="margin-bottom: 5px" />
                                    <button type="button" class="btn btn-info col-md-12 frontend-primary-btn" onclick="changeSelectedProduct({{$orderDetailsData->id}});">Change</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Cancel</button>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade" id="msgmodal" role="dialog">
            <div class="modal-dialog modal-sm">

                <!-- Modal content-->
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                        <h4 class="modal-title">Change Product Info</h4>
                    </div>
                    <div class="modal-body">
                        <p id="info-msg"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="msg-mod-btn-scs" onclick="redirectToUrl();" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Okay</button>
                        <button type="button" id="msg-mod-btn-fail" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Okay</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</section>
@endsection
