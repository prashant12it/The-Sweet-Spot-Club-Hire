@extends('layouts.clubcourier')

@section('content')
    <section id="ccbooking-banner">
        <div class="overlap-black-shadow"></div>
        <div class="hire-caption">
            <h3>Club Courier Booking Summary</h3>
        </div>
    </section>
    <section id="details">
        <div class="container">
            <form method="POST" action="" disabled name="addShipping" autocomplete="off">
            <h3><span>Personal Details</span></h3>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Name</label>
                        <input type="text" name="ccp_name" value="{{$orderDetails->user_name}}"
                               required="required" disabled placeholder="Name"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Email Address</label>
                        <input type="email" name="ccp_email" value="{{$orderDetails->user_email}}"
                               required="required" disabled placeholder="Email"/>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Phone No.</label>
                        <input type="tel" name="ccp_phone" value="{{$orderDetails->user_phone}}"
                               required="required" disabled placeholder="Phone No."/>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="not-req">Where did you hear about us ...?</label>
                        <input type="text" name="here_abt_us" disabled id="here_abt_us" value="{{(!empty($orderDetails->here_abt_us)?$orderDetails->here_abt_us:'N/A')}}"
                               placeholder="Here about us"/>
                    </div>
                </div>
                <h3><span>Pickup Details</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>Region of pickup</label>
                        <input type="text" name="ccp_pickup_region" value="{{$orderDetails->pickup_region}}"
                               required="required" disabled placeholder="Pickup region"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" disabled type="checkbox" {{ ($orderDetails->pickup_place == 1 ? 'checked':'') }} name="place-pickup" value="1" >Business
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" disabled type="checkbox" {{ ($orderDetails->pickup_place == 2 ? 'checked':'') }} name="place-pickup" value="2" >Hotel / Resort
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" disabled type="checkbox" {{ ($orderDetails->pickup_place == 3 ? 'checked':'') }} name="place-pickup" value="3" >Golf Course
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" disabled type="checkbox" {{ ($orderDetails->pickup_place == 4 ? 'checked':'') }} name="place-pickup" value="4" >Residential
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Company Name</label>
                        <input type="text" name="ccp_company_name" disabled id="ccp_company_name" value="{{$orderDetails->pickup_company_name}}" placeholder="Company Name"/>

                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Name</label>
                        <input type="text" name="ccp_contact_name" id="ccp_contact_name" value="{{$orderDetails->pickup_contact_name}}"
                               required="required" disabled placeholder="Contact Name"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Phone No.</label>
                        <input type="tel" name="ccp_conatct_phone" id="ccp_conatct_phone" value="{{$orderDetails->pickup_phone_num}}"
                               required="required" disabled placeholder="Contact Phone No."/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Address</label>
                        <input type="text" name="ccp_address" id="ccp_address" disabled value="{{$orderDetails->pickup_address}}"
                               placeholder="Address"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Suburb</label>
                        <input type="text" name="ccp_suburb" id="ccp_suburb" disabled value="{{$orderDetails->pickup_suburb}}"
                               required="required" placeholder="Suburb"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Postcode</label>
                        <input type="tel" name="ccp_postcode" id="ccp_postcode" disabled value="{{$orderDetails->pickup_postal_code}}"
                               required="required" placeholder="Postcode"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8">
                        <textarea type="text" class="form-control" disabled name="ccp_collection_notes"
                                  placeholder="Collection notes">{{$orderDetails->pickup_delivery_note}}</textarea>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Pickup collection date</label>
                        <input type="text" name="ccp_date" id="fromDate" disabled value="{{date('jS M Y',strtotime($orderDetails->pickup_date))}}"
                               required="required" placeholder="Pickup collection date"/>
                    </div>
                </div>
                <h3><span>Destination Details</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>Region of drop off</label>
                        <input type="text" name="ccd_dropoff_region" value="{{$orderDetails->destination_region}}"
                               required="required" disabled placeholder="Drop off region"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" disabled type="checkbox" {{ ($orderDetails->destination_place == 1 ? 'checked':'') }} name="place-dropoff" value="1" >Business
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" disabled type="checkbox" {{ ($orderDetails->destination_place == 2 ? 'checked':'') }} name="place-dropoff" value="2" >Hotel / Resort
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" disabled type="checkbox" {{ ($orderDetails->destination_place == 3 ? 'checked':'') }} name="place-dropoff" value="3" >Golf Course
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" disabled type="checkbox" {{ ($orderDetails->destination_place == 4 ? 'checked':'') }} name="place-dropoff" value="4" >Residential
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Company Name</label>
                        <input type="text" name="ccd_company_name" id="ccd_company_name" disabled value="{{$orderDetails->destination_company_name}}" placeholder="Company Name"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Name</label>
                        <input type="text" name="ccd_contact_name" id="ccd_contact_name" value="{{$orderDetails->destination_contact_name}}"
                               required="required" disabled placeholder="Contact Name"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Phone No.</label>
                        <input type="tel" name="ccd_conatct_phone" disabled id="ccd_conatct_phone" value="{{$orderDetails->destination_phone_num}}"
                               required="required" placeholder="Contact Phone No."/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Address</label>
                        <input type="text" name="ccd_address" disabled id="ccd_address" value="{{$orderDetails->destination_address}}"
                               placeholder="Address"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Suburb</label>
                        <input type="text" name="ccd_suburb" disabled id="ccd_suburb" value="{{$orderDetails->destination_suburb}}"
                               required="required" placeholder="Suburb"/>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Postcode</label>
                        <input type="tel" name="ccd_postcode" id="ccd_postcode" disabled value="{{$orderDetails->destination_postal_code}}"
                               required="required" placeholder="Postcode"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8">
                        <textarea type="text" class="form-control" disabled id="ccd_collection_notes" name="ccd_collection_notes"
                                  placeholder="Delivery notes">{{$orderDetails->destination_note}}</textarea>
                    </div>
                </div>
            @if(count($bagArr)>0)
                <h3><span>Bag Details</span></h3>
                @foreach($bagArr as $key => $bag)
                <div class="row" id="bag1">
                    <div class="form-group col-sm-6">
                        <label>Bag title</label>
                        <input type="text" name="bagTitle{{$key}}" disabled value="{{$bag->bag_title}}" placeholder="Bag title" required="required"/>
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Bag type/size</label>
                        <input type="text" name="bagType{{$key}}" disabled value="{{$bag->product_name}}" placeholder="Bag type" required="required"/>
                    </div>
                </div>
                @endforeach
            @endif
                <h3><span>Shipping Option</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <button id="oneway" class="btn btn-info frontend-primary-btn col-md-5" value="1" type="button" disabled>{{($orderDetails->return_region?'Return':'One Way')}}</button>
                    </div>
                </div>
            @if($orderDetails->return_region)
                <div>
                    <h3><span>Pickup Details</span></h3>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Region of pickup</label>
                            <input type="text" name="retccp_pickup_region" value="{{$orderDetails->return_region}}"
                                   required="required" disabled placeholder="Pickup region"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" disabled type="checkbox" {{ ($orderDetails->return_place == 1 ? 'checked':'') }} name="place-ret-pickup" value="1" >Business
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" disabled type="checkbox" {{ ($orderDetails->return_place == 2 ? 'checked':'') }} name="place-ret-pickup" value="2" >Hotel / Resort
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" disabled type="checkbox" {{ ($orderDetails->return_place == 3 ? 'checked':'') }} name="place-ret-pickup" value="3" >Golf Course
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" disabled type="checkbox" {{ ($orderDetails->return_place == 4 ? 'checked':'') }} name="place-ret-pickup" value="4" >Residential
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company Name</label>
                            <input type="text" disabled name="retccp_company_name" id="retccp_company_name" value="{{$orderDetails->return_company_name}}" placeholder="Company Name"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Name</label>
                            <input type="text" name="retccp_contact_name" disabled id="retccp_contact_name" value="{{$orderDetails->return_contact_name}}"
                                   required="required" placeholder="Contact Name"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Phone No.</label>
                            <input type="tel" name="retccp_conatct_phone" disabled id="retccp_conatct_phone" value="{{$orderDetails->return_phone_num}}"
                                   required="required" placeholder="Contact Phone No."/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Address</label>
                            <input type="text" name="retccp_address" id="retccp_address" disabled value="{{$orderDetails->return_address}}"
                                   placeholder="Address"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Suburb</label>
                            <input type="text" name="retccp_suburb" disabled id="retccp_suburb" value="{{$orderDetails->return_suburb}}"
                                   required="required" placeholder="Suburb"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Postcode</label>
                            <input type="tel" name="retccp_postcode" id="retccp_postcode" disabled value="{{$orderDetails->return_postal_code}}"
                                   required="required" placeholder="Postcode"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-8">
                        <textarea type="text" class="form-control" disabled id="retccp_collection_notes" name="retccp_collection_notes"
                                  placeholder="Collection notes">{{$orderDetails->return_collection_note}}</textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Pickup collection date</label>
                            <input type="text" name="retccp_date" id="retfromDate" disabled value="{{date('jS M Y',strtotime($orderDetails->return_date))}}"
                                   required="required" placeholder="Pickup collection date"/>
                        </div>
                    </div>
                    <h3><span>Destination Details</span></h3>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Region of drop off</label>
                            <input type="text" name="retccd_dropoff_region" value="{{$orderDetails->return_d_region}}"
                                   required="required" disabled placeholder="Drop off region"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" disabled type="checkbox" {{ ($orderDetails->return_d_place == 1 ? 'checked':'') }} name="place-ret-dropoff" value="1" >Business
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" disabled type="checkbox" {{ ($orderDetails->return_d_place == 2 ? 'checked':'') }} name="place-ret-dropoff" value="2" >Hotel / Resort
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" disabled type="checkbox" {{ ($orderDetails->return_d_place == 3 ? 'checked':'') }} name="place-ret-dropoff" value="3" >Golf Course
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" disabled type="checkbox" {{ ($orderDetails->return_d_place == 4 ? 'checked':'') }} name="place-ret-dropoff" value="4" >Residential
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company Name</label>
                            <input type="text" disabled name="retccd_company_name" id="retccd_company_name" value="{{$orderDetails->return_d_company_name}}" placeholder="Company Name"/>

                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Name</label>
                            <input type="text" name="retccd_contact_name" id="retccd_contact_name" value="{{$orderDetails->return_d_contact_name}}"
                                   required="required" disabled placeholder="Contact Name"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Phone No.</label>
                            <input type="tel" name="retccd_conatct_phone" disabled id="retccd_conatct_phone" value="{{$orderDetails->return_d_phone_num}}"
                                   required="required" placeholder="Contact Phone No."/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Address</label>
                            <input type="text" disabled name="retccd_address" id="retccd_address" value="{{$orderDetails->return_d_address}}"
                                   placeholder="Address"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Suburb</label>
                            <input type="text" name="retccd_suburb" disabled id="retccd_suburb" value="{{$orderDetails->return_d_suburb}}"
                                   required="required" placeholder="Suburb"/>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Postcode</label>
                            <input type="tel" name="retccd_postcode" disabled id="retccd_postcode" value="{{$orderDetails->return_d_postal_code}}"
                                   required="required" placeholder="Postcode"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-8">
                        <textarea type="text" class="form-control" disabled name="retccd_collection_notes"
                                  placeholder="Delivery notes">{{$orderDetails->return_d_note}}</textarea>
                        </div>
                    </div>
                </div>
            @endif
                <h3><span>Calculate Quote</span></h3>
                <div class="row" id="calculateQuote">
                    <div class="form-group col-sm-6">
                        <h4><b>Outgoing Shipment</b></h4>
                        <div class="radio">
                            <label><input disabled type="radio" name="outshipment" value="1" {{($orderDetails->outgoing_shipment == 1?'checked':'')}}>Standard courier ${{(number_format($outShipPrice,2,'.',''))}}</label>
                        </div>
                        <div class="radio">
                            <label><input disabled type="radio" name="outshipment" value="2" {{($orderDetails->outgoing_shipment == 2?'checked':'')}}>Express courier ${{(number_format($outShipPrice + 20,2,'.',''))}}</label>
                        </div>
                        <div id="onewayshiptime">
                            Estimated shipment time: {{($orderDetails->transit_days_out)}} business days
                        </div>
                    </div>
                    @if($orderDetails->return_region)
                    <div class="form-group col-sm-6 retShippment">
                        <h4><b>Return Shipment</b></h4>
                        <div class="radio">
                            <label><input disabled type="radio" name="returnshipment" value="1" {{($orderDetails->return_shipment == 1?'checked':'')}}>Standard courier ${{(number_format($retShipPrice,2,'.',''))}}</label>
                        </div>
                        <div class="radio">
                            <label><input disabled type="radio" name="returnshipment" value="2" {{($orderDetails->return_shipment == 2?'checked':'')}}>Express courier ${{(number_format($retShipPrice + 20,2,'.',''))}}</label>
                        </div>
                        <div id="returnshiptime">
                            Estimated shipment time: {{($orderDetails->transit_days_ret)}} business days
                        </div>
                    </div>
                        @endif
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label>Voucher Code</label>
                        <input type="text" name="voucher_code" disabled id="voucher_code" value="{{$orderDetails->offer_Code}}"
                          placeholder="Voucher Code"/>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Voucher Discount</label>
                        <input type="text" name="voucher_discount" disabled id="voucher_discount" value="{{(number_format($orderDetails->offer_amnt,2,'.',''))}}"
                               placeholder="Voucher Discount"/>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Multiset Discount</label>
                        <input type="text" name="multiset_discount" disabled id="multiset_discount" value="{{(number_format($orderDetails->multiset_discount,2,'.',''))}}"
                               placeholder="Multiset Discount"/>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Total amount to be paid</label>
                        <input type="text" name="total-amount" disabled id="total-amount" value="${{(number_format($orderDetails->sub_total_amnt,2,'.',''))}} - ${{(number_format($orderDetails->offer_amnt,2,'.',''))}} - ${{(number_format($orderDetails->multiset_discount,2,'.',''))}} = ${{(number_format($orderDetails->sub_total_amnt - $orderDetails->offer_amnt - $orderDetails->multiset_discount,2,'.',''))}}"
                               placeholder="Total Amount"/>
                    </div>
                </div>
                {{--<div class="submit-btn">
                    <input type="submit" disabled value="Order Now"/>
                </div>--}}
            </form>
            <div class="submit-btn" id="cc-stripe-pay-btn">
                {{--<input type="button" onclick="showPayOpt('{{$paymentSwitch}}');" value="Confirm and Proceed to Payment"/>--}}
                {{--<input type="button" onclick="showPayOptStripe();" value="Confirm and Proceed to Payment"/>--}}

                <div class="row">
                    <div class="col-md-9">
                        <button type="button" class="stripe-button-el" onclick="gotopage('{{url('clubcourier/booking')}}')">
                            <span style="display: block; min-height: 30px;">Modify</span></button>
                    </div>
                    <div class="col-md-3">
                <?php
                $log  = "Offer discount: ".($orderDetails->offer_amnt).PHP_EOL.
                    "order_reference: ".($orderDetails->order_reference_id).PHP_EOL.
                    "Name: ".$orderDetails->user_name.PHP_EOL.
                    "E-mail: ".$orderDetails->user_email.PHP_EOL.
                    "Sub Total: ".($orderDetails->sub_total_amnt).PHP_EOL.
                    "Total: ".($orderDetails->total_amnt).PHP_EOL.
                    "Date Time: ".(date('d-m-Y h:i:s')).PHP_EOL.
                    "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('../stripe_log_check.txt', $log, FILE_APPEND);
                ?>
                <form action="{{ URL::to('/clubcourier/thankyou')}}" method="POST">

                    <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="pk_live_NJvVMdwtbtGVAyB6orFniC8k"
                            data-amount="{{number_format($orderDetails->total_amnt,2,'.','')*100}}"
                            data-name="The Sweet Spot Club Courier"
                            data-description="TSS Club Courier booking Charges."
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-email="{{$orderDetails->user_email}}"
                            data-label="Confirm and proceed to payment"
                            data-currency="AUD"
                            data-locale="auto">
                    </script>
                    <input type="hidden" name="amount" value="{{number_format($orderDetails->total_amnt,2,'.','')*100}}" />
                    <input type="hidden" name="order_reference_id" value="{{$orderDetails->order_reference_id}}" />
                    <input type="hidden" name="description" value="TSS Club Courier booking Charges." />
                </form>
                </div>
                </div>
            </div>
        </div>
    </section>
@endsection