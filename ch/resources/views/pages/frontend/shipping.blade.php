@extends('layouts.frontend_shipping')

@section('content')

@include('includes.frontend.steps')
    <section class="yellow-bg">
        <div class="container">
            <div class="cart-value">
                <table id="CartItems" class="table table-striped GridMain" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr class="GridHeader">
                            <th>YOUR ORDER : {{date('jS F Y',strtotime(session()->get('fromDate')))}} - {{date('jS F Y',strtotime(session()->get('toDate')))}}</th>
                            <th>QUANTITY</th>
                            <th>COST</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $total = 0;?>
                    @if(!empty($cartDetailArr))
                    @foreach($cartDetailArr as $cartkey => $cartprods)
                    <tr class="GridRow">
                        <td data-title="YOUR ORDER : {{date('jS F Y',strtotime(session()->get('fromDate')))}} - {{date('jS F Y',strtotime(session()->get('toDate')))}}">{{$cartprods['prod-name']}}
                            <br />
                            @if(!empty($cartprods['allAttribSet']))
                                @foreach($cartprods['allAttribSet'] as $attrKey => $attributesSet)
                            @if(count($cartprods['attributes'])>0)
                            <?php $totalAttrib = count($attributesSet);
                            $i=1;?>
                            -
                            @foreach($attributesSet as $attributes)
                            {{$attributes->value}}{{($i != $totalAttrib?', ':'')}}
                            <?php $i++;?>
                            @endforeach
                            @endif
                                    <br>
                                @endforeach
                            @endif
                            </td>
                        <td data-title="QUANTITY">{{$cartprods['quantity']}}</td>
                        <td data-title="COST">${{number_format($cartprods['price']*($cartprods['product_type']==5?$cartprods['quantity']:1),2,'.',',')}}</td>
                    </tr>
                    <?php $total = $total + $cartprods['price']*($cartprods['product_type']==5?$cartprods['quantity']:1); ?>
                    @endforeach
                    @endif
                        <!--<tr class="GridRow">
                            <td>Insurance</td>
                            <td>{{($insurance >0?'1':'0')}}</td>
                            <td>${{number_format($orderDetails->insurance_amnt,2,'.',',')}}</td>
                        </tr>-->
<!--                    --><?php //$total = $total + $orderDetails->insurance_amnt; ?>
                    </tbody>
                </table>
                <table id="CartItems" class="table table-striped GridMain" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr class="GridRow">
                            <td class="hidden-xs"></td>
                            <td><strong>SUB TOTAL</strong></td>
                            <td id="view-subtotal">${{number_format($total,2,'.',',')}}</td>
                            <input type="hidden" id="subtotal" name="subtotal" value="{{number_format($total,2,'.','')}}">
                        </tr>
                        <tr class="GridRow">
                            <td class="hidden-xs"></td>
                            <td><strong>MULTI SET DISCOUNT</strong></td>
                            <td>${{number_format($cartprods['Discount'],2,'.',',')}}
                                <input type="hidden" id="msd" name="msd" value="{{number_format($cartprods['Discount'],2,'.',',')}}"></td></td>
                        </tr>
                        <tr class="GridRow">
                            <td class="hidden-xs"></td>
                            <td><strong>PARTNER DISCOUNT</strong></td>
                            <td>${{number_format(($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00),2,'.',',')}}
                                <input type="hidden" id="pdis" name="pdis" value="{{number_format(($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00),2,'.',',')}}"></td></td>
                        </tr>
                        <tr class="GridRow">
                            <td class="hidden-xs"></td>
                            <td><strong>INSURANCE</strong></td>
                            <td>${{number_format($orderDetails->insurance_amnt,2,'.',',')}}
                                <input type="hidden" id="insu" name="insu" value="{{number_format($orderDetails->insurance_amnt,2,'.',',')}}"></td>
                            <input type="hidden" id="insurance" name="insurance" value="{{number_format($orderDetails->insurance_amnt,2,'.','')}}">
                        </tr>
                        <tr class="GridRow">
                            <td class="hidden-xs"></td>
                            <td><strong>HANDLING / DELIVERY FEE</strong></td>
                            <td id="handling-price">$0</td>
                        </tr>
<!--                        --><?php //$total = $total + $orderDetails->shipping_amnt; ?>
                        <tr class="GridRow">
                            <td class="hidden-xs"></td>
                            <td><strong>TOTAL</strong></td>
                            <td id="view-totalprice">${{number_format((number_format($cartprods['subtotal'],2,'.','') + number_format($cartprods['insurance'],2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00),2,'.','')),2,'.',',')}}</td>
                            <input type="hidden" id="totalprice" name="totalprice" value="{{number_format((number_format($cartprods['subtotal'],2,'.','') + number_format($cartprods['shipping'],2,'.','') + number_format($cartprods['insurance'],2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00),2,'.','')),2,'.','')}}">
                        </tr>
                        <tr class="GridRow">
                            <td class="hidden-xs"></td>
                            <td style="vertical-align: top"><input type="text" id="offer-code" name="offer-code" value="{{old('offer-code')}}" class="form-control" placeholder="I have an offer code."/></td>
                            <td><button type="button" class="btn frontend-primary-btn btn-info col-md-4" id="get-offer" onclick="getOfferCodeDiscount();" value="Apply">Apply</button>
                            <span class="col-md-8" id="off-amount"></span> </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="details">
        <div class="container">
            <form method="POST" action="{{ url('/add_shipping') }}" name="addShipping" autocomplete="off">
                {{ csrf_field() }}
                <input type="hidden" name="order_reference_id" value="{{$orderDetails->order_reference_id}}"/>
                <input type="hidden" name="offer_code" id="offer" value="" />
                <input type="hidden" id="handling" name="shipping_amnt" value="0">
                <h3><span>Your Details</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>First Name ...</label>
                        <input type="text" name="buyer_first_name" value="{{old('buyer_first_name')}}" required="required"/>
                        @if ($errors->has('buyer_first_name'))
                        <span class="help-block err">
                            <strong>{{ $errors->first('buyer_first_name') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Surname ...</label>
                        <input type="text" name="buyer_last_name" value="{{old('buyer_last_name')}}" required="required"/>
                        @if ($errors->has('buyer_last_name'))
                        <span class="help-block err">
                            <strong>{{ $errors->first('buyer_last_name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>E-mail ...</label>
                        <input type="text" name="buyer_email" value="{{old('buyer_email')}}" required="required"/>
                        @if ($errors->has('buyer_email'))
                        <span class="help-block err">
                            <strong>{{ $errors->first('buyer_email') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Confirm Email ...</label>
                        <input type="text" name="buyer_confirm_email" value="{{old('buyer_confirm_email')}}" required="required"/>
                        @if ($errors->has('buyer_confirm_email'))
                        <span class="help-block err">
                            <strong>{{ $errors->first('buyer_confirm_email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>Country of residence ...</label>
                        <select name="buyer_country" required="required">
<!--                            <option value="0">Select Country</option>-->
                            <!--<option value="13">Australia</option>-->
                            @if ($countriesAry->count() > 0)
                                @foreach ($countriesAry as $country)
                                    <option value="{{$country->id}}" {{(old('buyer_country') == $country->id ? "selected=selected":($country->id == '13'?"selected=selected":""))}}>{{$country->name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('buyer_country'))
                        <span class="help-block err">
                            <strong>{{ $errors->first('buyer_country') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Contact Phone Number In Australia ...</label>
                        <input type="tel" minlength="10" maxlength="10" name="phone_no_aus" value="{{old('phone_no_aus')}}" required="required"/>
                        @if ($errors->has('phone_no_aus'))
                        <span class="help-block err">
                            <strong>{{ $errors->first('phone_no_aus') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-offset-6 col-sm-6">
                        <label>Where did you hear about us ...?</label>
                        <select name="here_abt_us" id="here_abt_us" required="required">
                            <option value="">Select</option>
                            <option value="Google" {{(old('here_abt_us') == 'Google' ? "selected=selected":"")}}>Google</option>
                            <option value="Family/Friend" {{(old('here_abt_us') == 'Family/Friend' ? "selected=selected":"")}}>Family/Friend</option>
                            <option value="Facebook" {{(old('here_abt_us') == 'Facebook' ? "selected=selected":"")}}>Facebook</option>
                            <option value="Instagram" {{(old('here_abt_us') == 'Instagram' ? "selected=selected":"")}}>Instagram</option>
                            <option value="Other" {{(old('here_abt_us') == 'Other' ? "selected=selected":"")}}>Other</option>
                        </select>
                        @if ($errors->has('here_abt_us'))
                        <span class="help-block err">
                                    <strong>{{ $errors->first('here_abt_us') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-sm-6">
                        <h3><span>Your Delivery Details (Drop off)</span></h3>
                        <h5>Note: We don't deliver to residential addresses.</h5>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <input class="check-box place-dropoff" type="checkbox" {{ (old("dropoff_place") == 1 ? 'checked':'') }} name="dropoff_place" id="hotel-res-dropoff" value="1" onclick="checkPlace('dropoff',this);">Hotel / Resort
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <input class="check-box place-dropoff" type="checkbox" {{ (old("dropoff_place") == 2 ? 'checked':'') }} name="dropoff_place" id="business-place-dropoff" value="2" onclick="checkPlace('dropoff',this);">Business
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <input class="check-box place-dropoff" type="checkbox" {{ (old("dropoff_place") == 3 ? 'checked':'') }} name="dropoff_place" id="golf-course-dropoff" value="3" onclick="checkPlace('dropoff',this);">Golf Course
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Name of hotel/golf course/accommodation to be delivered to ...</label>
                                <input type="text" name="delvr_hotel_name" onkeyup="pickupDetails('delvr_hotel_name','pickup_hotel_name');" id="delvr_hotel_name" value="{{old('delvr_hotel_name')}}" style="background-image:none" required="required">
                                @if ($errors->has('delvr_hotel_name'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('delvr_hotel_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Address for clubs to be delivered to (street no,Street name) ...</label>
                                <input type="text" name="delvr_address" onkeyup="pickupDetails('delvr_address','pickup_address');" id="delvr_address" value="{{old('delvr_address')}}"  required="required"/>
                                @if ($errors->has('delvr_address'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('delvr_address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>State of delivery address ...</label>
                                <select name="delvr_state_id" id="delvr_state_id" required="required" onchange="pickupDetails('delvr_state_id','pickup_state_id');" >
                                    <option value="0">Select State</option>
                                        @if ($statesAry->count() > 0)
                                            @foreach ($statesAry as $state)
                                                @if($state->id != 271)
                                                <option value="{{$state->id}}" {{(old('delvr_state_id') == $state->id ? "selected=selected":"")}}>{{$state->name}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                </select>
                                @if ($errors->has('delvr_state_id'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('delvr_state_id') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Post code of delivery address ...</label>
                                <input type="text" name="delvr_postal_code" onkeyup="pickupDetails('delvr_postal_code','pickup_postal_code');" onblur="calculateShipping({{$setCount}});" id="delvr_postal_code" value="{{old('delvr_postal_code')}}"required="required"/>
                                @if ($errors->has('delvr_postal_code'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('delvr_postal_code') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Suburb ...</label>
                                <input type="text" name="suburb" onkeyup="pickupDetails('suburb','suburbpickup');" id="suburb" value="{{old('suburb')}}" required="required"/>
                                @if ($errors->has('suburb'))
                                <span class="help-block err">
                            <strong>{{ $errors->first('suburb') }}</strong>
                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3><span>Your Delivery Details (Pick up)</span></h3>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input class="check-box" type="checkbox" name="is_same_pickup_addrs" id="is_same_pickup_addrs" value="1"  {{ (!empty(old('buyer_first_name')) && old('is_same_pickup_addrs') != 1 ? "":"checked") }} onclick="samePickupAddress('{{$setCount}}');">Same as delivery address
                                @if ($errors->has('is_same_pickup_addrs'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('is_same_pickup_addrs') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row {{ (!empty(old('buyer_first_name')) && old('is_same_pickup_addrs') != 1 ? '':'hide') }} pickup">
                            <div class="form-group col-sm-12">
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <input class="check-box place-pickup" type="checkbox" {{ (old("pickup_place") == 1 ? 'checked':'') }} name="pickup_place" id="hotel-res-pickup" value="1" onclick="checkPlace('pickup',this);">Hotel / Resort
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <input class="check-box place-pickup" type="checkbox" {{ (old("pickup_place") == 2 ? 'checked':'') }} name="pickup_place" id="business-place-pickup" value="2" onclick="checkPlace('pickup',this);">Business
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <input class="check-box place-pickup" type="checkbox" {{ (old("pickup_place") == 3 ? 'checked':'') }} name="pickup_place" id="golf-course-pickup" value="3" onclick="checkPlace('pickup',this);">Golf Course
                                </div>
                            </div>
                        </div>
                        <div class="row {{ (!empty(old('buyer_first_name')) && old('is_same_pickup_addrs') != 1 ? '':'hide') }} pickup">
                            <div class="form-group col-sm-12">
                                <label>Name of hotel/golf course/accommodation to be picked up from ...</label>
                                <input type="text" name="pickup_hotel_name" id="pickup_hotel_name" value="{{old('pickup_hotel_name')}}" style="background-image:none" required="required">
                                @if ($errors->has('pickup_hotel_name'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('pickup_hotel_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row {{ (!empty(old('buyer_first_name')) && old('is_same_pickup_addrs') != 1 ? '':'hide') }} pickup">
                            <div class="form-group col-sm-12">
                                <label>Address for clubs to be picked up from (street no,Street name) ...</label>
                                <input type="text" name="pickup_address" id="pickup_address"  value="{{old('pickup_address')}}" required="required"/>
                                @if ($errors->has('pickup_address'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('pickup_address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row {{ (!empty(old('buyer_first_name')) && old('is_same_pickup_addrs') != 1 ? '':'hide') }} pickup">
                            <div class="form-group col-sm-6">
                                <label>State of delivery address ...</label>
                                <select name="pickup_state_id" required="required" id="pickup_state_id">
                                    <option value="0">Select State</option>
                                        @if ($statesAry->count() > 0)
                                            @foreach ($statesAry as $state)
                                    @if($state->id != 271)
                                                <option value="{{$state->id}}" {{(old('pickup_state_id') == $state->id ? "selected=selected":"")}}>{{$state->name}}</option>
                                    @endif
                                            @endforeach
                                        @endif
                                </select>
                                @if ($errors->has('pickup_state_id'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('pickup_state_id') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Post code of pick up address ...</label>
                                <input type="text" name="pickup_postal_code" onblur="calculateShipping({{$setCount}});" id="pickup_postal_code" value="{{old('pickup_postal_code')}}" required="required"/>
                                @if ($errors->has('pickup_postal_code'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('pickup_postal_code') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row {{ (!empty(old('buyer_first_name')) && old('is_same_pickup_addrs') != 1 ? '':'hide') }} pickup">
                            <div class="form-group col-sm-6">
                                <label>Suburb ...</label>
                                <input type="text" name="suburbpickup" id="suburbpickup" value="{{old('suburbpickup')}}" required="required"/>
                                @if ($errors->has('suburbpickup'))
                                <span class="help-block err">
                            <strong>{{ $errors->first('suburbpickup') }}</strong>
                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input class="check-box" type="checkbox" name="tss" value="1" id="tss" {{ (old("tss") == 1 ? "checked=checked":"") }} >I want to sign up to the TSS email list to get amazing promotions and news on upcoming news and events
                                @if ($errors->has('tss'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('tss') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <input class="check-box" type="checkbox" name="iTerms" value="1" id="iTerms" required="required"  {{ (old("is_same_pickup_addrs") == 1 ? "checked=checked":"") }} ><a target="_blank" href="{{url('../terms-of-service/')}}">Accept Terms and Conditions</a>
                                @if ($errors->has('iTerms'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('iTerms') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="submit-btn">
                    <input type="submit" value="Submit"/>
                </div>
            </form>
            <hr/>
        </div>
        <div id="popupbs">
	<div class="modal fade" id="infomodal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						&times;
					</button>
					<h4 class="modal-title">Handling and Delivery Info</h4>
				</div>
				<div class="modal-body">
					<p id="info-msg"></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Okay
					</button>
				</div>
			</div>

		</div>
	</div>

</div>
    </section>
@endsection