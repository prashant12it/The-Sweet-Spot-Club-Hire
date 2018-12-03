@extends('layouts.clubcourier')

@section('content')
    <section id="ccbooking-banner">
        <div class="overlap-black-shadow"></div>
        <div class="hire-caption">
            <h3>Club Courier Booking</h3>
        </div>
    </section>
    <section id="details">
        <div class="container">
            <form method="POST" action="{{ url('/clubcourier/courier_booking') }}" name="addShipping" autocomplete="off">
                <h3><span>Personal Details</span></h3>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label>Name</label>
                        <input type="text" name="ccp_name" value="{{(old('ccp_name')?old('ccp_name'):(isset($orderDetails) && $orderDetails->user_name?$orderDetails->user_name:''))}}"
                               required="required" placeholder="Name"/>
                        @if ($errors->has('ccp_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-3">
                        <label>Email Address</label>
                        <input type="email" name="ccp_email" value="{{(old('ccp_email')?old('ccp_email'):(isset($orderDetails) && $orderDetails->user_email?$orderDetails->user_email:''))}}"
                               required="required" placeholder="Email"/>
                        @if ($errors->has('ccp_email'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_email') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-3">
                        <label>Phone No.</label>
                        <input type="tel" name="ccp_phone" value="{{(old('ccp_phone')?old('ccp_phone'):(isset($orderDetails) && $orderDetails->user_phone?$orderDetails->user_phone:''))}}"
                               required="required" placeholder="Phone No."/>
                        @if ($errors->has('ccp_phone'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_phone') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-3">
                        <label class="not-req">Where did you hear about us ...?</label>
                        <select name="here_abt_us" id="here_abt_us">
                            <option value="">Select</option>
                            <option value="Google" {{(old('here_abt_us') == 'Google' ? "selected=selected":(isset($orderDetails) && $orderDetails->here_abt_us == 'Google'?'selected="selected"':''))}}>Google</option>
                            <option value="Family/Friend" {{(old('here_abt_us') == 'Family/Friend' ? "selected=selected":(isset($orderDetails) && $orderDetails->here_abt_us == 'Family/Friend'?'selected="selected"':''))}}>Family/Friend</option>
                            <option value="Facebook" {{(old('here_abt_us') == 'Facebook' ? "selected=selected":(isset($orderDetails) && $orderDetails->here_abt_us == 'Facebook'?'selected="selected"':''))}}>Facebook</option>
                            <option value="Instagram" {{(old('here_abt_us') == 'Instagram' ? "selected=selected":(isset($orderDetails) && $orderDetails->here_abt_us == 'Instagram'?'selected="selected"':''))}}>Instagram</option>
                            <option value="Other" {{(old('here_abt_us') == 'Other' ? "selected=selected":(isset($orderDetails) && $orderDetails->here_abt_us == 'Other'?'selected="selected"':''))}}>Other</option>
                        </select>
                        @if ($errors->has('here_abt_us'))
                            <span class="help-block err">
                                    <strong>{{ $errors->first('here_abt_us') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
                <h3><span>Pickup Details</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6 col-xs-10" style="padding-right: 0px">
                        <label>Region of pickup</label>
                        <select name="ccp_pickup_region" id="ccp_pickup_region" onchange="calculateTransitDays('1')" required="required">
                            <option value="">Select drop off</option>
                            <option value="" disabled>Victoria</option>
                            <option value="7" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Melbourne CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Melbourne CBD/Metro</option>
                            <option value="6" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Outer Melbourne'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Melbourne</option>
                            <option value="" disabled>South Australia</option>
                            <option value="1" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Adelaide CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Adelaide CBD/Metro</option>
                            <option value="13" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Outer Adelaide'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Adelaide</option>
                            <option value="" disabled>New South Wales</option>
                            <option value="5" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Sydney CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Sydney CBD/Metro</option>
                            <option value="4" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Outer Sydney'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Sydney</option>
                            <option value="" disabled>Queensland</option>
                            <option value="3" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Brisbane/Gold Coast Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Brisbane/Gold Coast Metro</option>
                            <option value="2" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Outer Brisbane'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Brisbane</option>
                            <option value="12" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Sunshine Coast'?'selected="selected"':'')}}>&nbsp; &nbsp;Sunshine Coast</option>
                            <option value="" disabled>Tasmania</option>
                            <option value="9" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Hobart CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart CBD/Metro</option>
                            <option value="14" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Hobart Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart Outer</option>
                            <option value="8" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Bridport'?'selected="selected"':'')}}>&nbsp; &nbsp;Bridport</option>
                            <option value="" disabled>Western Australia</option>
                            <option value="11" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Perth CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth CBD/Metro</option>
                            <option value="10" {{(isset($orderDetails) && $orderDetails->pickup_region == 'Perth Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth Outer</option>
                        </select>
                        @if ($errors->has('ccp_pickup_region'))
                            <span class="help-block err">
                            <strong>{{ $errors->first('ccp_pickup_region') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group col-sm-2 col-xs-2" style="padding-left: 0px">
                        <button type="button" class="btn" id="pickupInfoShow" onclick="showPickupInfo();" style="margin-top: 30px; margin-left: 10px">
                            <i class="fa fa-info"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" type="checkbox" {{ (old("place-pickup") == 1 || (isset($orderDetails) && $orderDetails->pickup_place == 1)? 'checked':'') }} name="place-pickup" id="business-pickup" value="1" onclick="checkPlace('pickup',this);">Business
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" type="checkbox" {{ (old("place-pickup") == 2 || (isset($orderDetails) && $orderDetails->pickup_place == 2) ? 'checked':'') }} name="place-pickup" id="hotel-pickup" value="2" onclick="checkPlace('pickup',this);">Hotel / Resort
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" type="checkbox" {{ (old("place-pickup") == 3 || (isset($orderDetails) && $orderDetails->pickup_place == 3) ? 'checked':'') }} name="place-pickup" id="golf-pickup" value="3" onclick="checkPlace('pickup',this);">Golf Course
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-pickup" type="checkbox" {{ (old("place-pickup") == 4 || (isset($orderDetails) && $orderDetails->pickup_place == 4) ? 'checked':'') }} name="place-pickup" id="res-pickup" value="4" onclick="checkPlace('pickup',this);">Residential
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Company Name</label>
                        <input type="text" name="ccp_company_name" id="ccp_company_name" value="{{(old('ccp_company_name')?old('ccp_company_name'):(isset($orderDetails) && $orderDetails->pickup_company_name?$orderDetails->pickup_company_name:''))}}" placeholder="Company Name"/>
                        @if ($errors->has('ccp_company_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_company_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Name</label>
                        <input type="text" name="ccp_contact_name" id="ccp_contact_name" value="{{(old('ccp_contact_name')?old('ccp_contact_name'):(isset($orderDetails) && $orderDetails->pickup_contact_name?$orderDetails->pickup_contact_name:''))}}"
                               required="required" placeholder="Contact Name"/>
                        @if ($errors->has('ccp_contact_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_contact_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Phone No.</label>
                        <input type="tel" name="ccp_conatct_phone" id="ccp_conatct_phone" value="{{(old('ccp_conatct_phone')?old('ccp_conatct_phone'):(isset($orderDetails) && $orderDetails->pickup_phone_num?$orderDetails->pickup_phone_num:''))}}"
                               required="required" placeholder="Contact Phone No."/>
                        @if ($errors->has('ccp_conatct_phone'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_conatct_phone') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Address</label>
                        <input type="text" name="ccp_address" id="ccp_address" value="{{(old('ccp_address')?old('ccp_address'):(isset($orderDetails) && $orderDetails->pickup_address?$orderDetails->pickup_address:''))}}"
                               placeholder="Address"/>
                        @if ($errors->has('ccp_address'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_address') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Suburb</label>
                        <input type="text" name="ccp_suburb" id="ccp_suburb" value="{{(old('ccp_suburb')?old('ccp_suburb'):(isset($orderDetails) && $orderDetails->pickup_suburb?$orderDetails->pickup_suburb:''))}}"
                               required="required" placeholder="Suburb"/>
                        @if ($errors->has('ccp_suburb'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_suburb') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Postcode</label>
                        <input type="tel" name="ccp_postcode" id="ccp_postcode" value="{{(old('ccp_postcode')?old('ccp_postcode'):(isset($orderDetails) && $orderDetails->pickup_postal_code?$orderDetails->pickup_postal_code:''))}}"
                               required="required" placeholder="Postcode"/>
                        @if ($errors->has('ccp_postcode'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_postcode') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8">
                        <textarea type="text" class="form-control" name="ccp_collection_notes"
                                  placeholder="Collection notes">{{(old('ccp_collection_notes')?old('ccp_collection_notes'):(isset($orderDetails) && $orderDetails->pickup_delivery_note?$orderDetails->pickup_delivery_note:''))}}</textarea>
                        @if ($errors->has('ccp_collection_notes'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_collection_notes') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Pickup collection date</label>
                        <input id="fromDate" class="hasDatepicker date datepicker" data-provide="datepicker"  type="text" name="ccp_date" value="{{(old('ccp_date')?old('ccp_date'):(isset($orderDetails) && $orderDetails->pickup_date?date('m/d/Y',strtotime($orderDetails->pickup_date)):''))}}"
                               required="required" placeholder="Pickup collection date"/>
                        @if ($errors->has('ccp_date'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_date') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <h3><span>Destination Details</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>Region of drop off</label>
                        <select name="ccd_dropoff_region" id="ccd_dropoff_region" onchange="calculateTransitDays('1')" required="required">
                            <option value="">Select drop off</option>
                            <option value="" disabled>Victoria</option>
                            <option value="7" {{(isset($orderDetails) && $orderDetails->destination_region == 'Melbourne CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Melbourne CBD/Metro</option>
                            <option value="6" {{(isset($orderDetails) && $orderDetails->destination_region == 'Outer Melbourne'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Melbourne</option>
                            <option value="" disabled>South Australia</option>
                            <option value="1" {{(isset($orderDetails) && $orderDetails->destination_region == 'Adelaide CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Adelaide CBD/Metro</option>
                            <option value="13" {{(isset($orderDetails) && $orderDetails->destination_region == 'Outer Adelaide'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Adelaide</option>
                            <option value="" disabled>New South Wales</option>
                            <option value="5" {{(isset($orderDetails) && $orderDetails->destination_region == 'Sydney CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Sydney CBD/Metro</option>
                            <option value="4" {{(isset($orderDetails) && $orderDetails->destination_region == 'Outer Sydney'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Sydney</option>
                            <option value="" disabled>Queensland</option>
                            <option value="3" {{(isset($orderDetails) && $orderDetails->destination_region == 'Brisbane/Gold Coast Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Brisbane/Gold Coast Metro</option>
                            <option value="2" {{(isset($orderDetails) && $orderDetails->destination_region == 'Outer Brisbane'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Brisbane</option>
                            <option value="12" {{(isset($orderDetails) && $orderDetails->destination_region == 'Sunshine Coast'?'selected="selected"':'')}}>&nbsp; &nbsp;Sunshine Coast</option>
                            <option value="" disabled>Tasmania</option>
                            <option value="9" {{(isset($orderDetails) && $orderDetails->destination_region == 'Hobart CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart CBD/Metro</option>
                            <option value="14" {{(isset($orderDetails) && $orderDetails->destination_region == 'Hobart Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart Outer</option>
                            <option value="8" {{(isset($orderDetails) && $orderDetails->destination_region == 'Bridport'?'selected="selected"':'')}}>&nbsp; &nbsp;Bridport</option>
                            <option value="" disabled>Western Australia</option>
                            <option value="11" {{(isset($orderDetails) && $orderDetails->destination_region == 'Perth CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth CBD/Metro</option>
                            <option value="10" {{(isset($orderDetails) && $orderDetails->destination_region == 'Perth Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth Outer</option>
                            {{--@if ($countriesAry->count() > 0)
                                @foreach ($countriesAry as $country)
                                    <option value="{{$country->id}}" {{(old('buyer_country') == $country->id ? "selected=selected":($country->id == '13'?"selected=selected":""))}}>{{$country->name}}</option>
                                @endforeach
                            @endif--}}
                        </select>
                        @if ($errors->has('ccd_dropoff_region'))
                            <span class="help-block err">
                            <strong>{{ $errors->first('ccd_dropoff_region') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" type="checkbox" {{ (old("place-dropoff") == 1 || (isset($orderDetails) && $orderDetails->destination_place == 1)? 'checked':'') }} name="place-dropoff" id="business-dropoff" value="1" onclick="checkPlace('dropoff',this);">Business
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" type="checkbox" {{ (old("place-dropoff") == 2 || (isset($orderDetails) && $orderDetails->destination_place == 2)? 'checked':'') }} name="place-dropoff" id="hotel-dropoff" value="2" onclick="checkPlace('dropoff',this);">Hotel / Resort
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" type="checkbox" {{ (old("place-dropoff") == 3 || (isset($orderDetails) && $orderDetails->destination_place == 3) ? 'checked':'') }} name="place-dropoff" id="golf-dropoff" value="3" onclick="checkPlace('dropoff',this);">Golf Course
                    </div>
                    <div class="form-group col-md-3">
                        <input class="check-box place-dropoff" type="checkbox" {{ (old("place-dropoff") == 4 || (isset($orderDetails) && $orderDetails->destination_place == 4) ? 'checked':'') }} name="place-dropoff" id="res-dropoff" value="4" onclick="checkPlace('dropoff',this);">Residential
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Company Name</label>
                        <input type="text" name="ccd_company_name" id="ccd_company_name" value="{{(old('ccd_company_name')?old('ccd_company_name'):(isset($orderDetails) && $orderDetails->destination_company_name?$orderDetails->destination_company_name:''))}}" placeholder="Company Name"/>
                        @if ($errors->has('ccd_company_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_company_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Name</label>
                        <input type="text" name="ccd_contact_name" id="ccd_contact_name" value="{{(old('ccd_contact_name')?old('ccd_contact_name'):(isset($orderDetails) && $orderDetails->destination_contact_name?$orderDetails->destination_contact_name:''))}}"
                               required="required" placeholder="Contact Name"/>
                        @if ($errors->has('ccd_contact_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_contact_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Phone No.</label>
                        <input type="tel" name="ccd_conatct_phone" id="ccd_conatct_phone" value="{{(old('ccd_conatct_phone')?old('ccd_conatct_phone'):(isset($orderDetails) && $orderDetails->destination_phone_num?$orderDetails->destination_phone_num:''))}}"
                               required="required" placeholder="Contact Phone No."/>
                        @if ($errors->has('ccd_conatct_phone'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_conatct_phone') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Address</label>
                        <input type="text" name="ccd_address" id="ccd_address" value="{{(old('ccd_address')?old('ccd_address'):(isset($orderDetails) && $orderDetails->destination_address?$orderDetails->destination_address:''))}}"
                               placeholder="Address"/>
                        @if ($errors->has('ccd_address'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_address') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Suburb</label>
                        <input type="text" name="ccd_suburb" id="ccd_suburb" value="{{(old('ccd_suburb')?old('ccd_suburb'):(isset($orderDetails) && $orderDetails->destination_suburb?$orderDetails->destination_suburb:''))}}"
                               required="required" placeholder="Suburb"/>
                        @if ($errors->has('ccd_suburb'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_suburb') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Postcode</label>
                        <input type="tel" name="ccd_postcode" id="ccd_postcode" value="{{(old('ccd_postcode')?old('ccd_postcode'):(isset($orderDetails) && $orderDetails->destination_postal_code?$orderDetails->destination_postal_code:''))}}"
                               required="required" placeholder="Postcode"/>
                        @if ($errors->has('ccd_postcode'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_postcode') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8">
                        <textarea type="text" class="form-control" id="ccd_collection_notes" name="ccd_collection_notes"
                                  placeholder="Delivery notes">{{(old('ccd_collection_notes')?old('ccd_collection_notes'):(isset($orderDetails) && $orderDetails->destination_note?$orderDetails->destination_note:''))}}</textarea>
                        @if ($errors->has('ccd_collection_notes'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_collection_notes') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <h3><span>Bag Details</span></h3>
                @if(isset($bagArr) && count($bagArr)>0)
                    @foreach($bagArr as $key => $bag)
                    <div class="row" id="bag{{$key+1}}">
                        <div class="form-group col-sm-5">
                            <label>Bag title</label>
                            <input type="text" readonly name="bagTitle{{$key+1}}" value="{{$bag->bag_title}}" placeholder="Bag title" required="required"/>
                            @if ($errors->has('bagTitle'.$key+1))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('bagTitle'.$key+1) }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-sm-5">
                            <label>Bag type/size</label>
                            <select name="bagType{{$key+1}}" required="required">
                                <option value="3"{{($bag->product_name == 'Small Bag i.e. stand bag (30 x 30 x 123cm)'?'selected="selected"':'')}}>Small Bag i.e. stand bag (30 x 30 x 123cm)</option>
                                <option value="1"{{($bag->product_name == 'Standard Bag i.e. cart bag (30 x 35 x 123cm)'?'selected="selected"':'')}}>Standard Bag i.e. cart bag (30 x 35 x 123cm)</option>
                                <option value="2"{{($bag->product_name == 'Large Bag i.e. staff bag (35 x 40 x 123cm)'?'selected="selected"':'')}}>Large Bag i.e. staff bag (35 x 40 x 123cm)</option>
                            </select>
                            @if ($errors->has('bagType'.$key+1))
                                <span class="help-block err">
                            <strong>{{ $errors->first('bagType'.$key+1) }}</strong>
                        </span>
                            @endif
                        </div>
                        @if ($key>0)
                        <div class="form-group col-sm-2">
                            <button class="btn btn-danger frontend-primary-btn col-md-8 btn-with-label" type="button" onclick="removeBag('{{$key+1}}')">Remove bag</button>
                        </div>
                            @endif
                    </div>
                    @endforeach
                    @else
                    <div class="row" id="bag1">
                        <div class="form-group col-sm-5">
                            <label>Bag title</label>
                            <input type="text" readonly name="bagTitle1" value="Bag 1" placeholder="Bag title" required="required"/>
                            @if ($errors->has('bagTitle1'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('bagTitle1') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-sm-5">
                            <label>Bag type/size</label>
                            <select name="bagType1" required="required">
                                <option value="3">Small Bag i.e. stand bag (30 x 30 x 123cm)</option>
                                <option value="1">Standard Bag i.e. cart bag (30 x 35 x 123cm)</option>
                                <option value="2">Large Bag i.e. staff bag (35 x 40 x 123cm)</option>
                            </select>
                            @if ($errors->has('bagType1'))
                                <span class="help-block err">
                            <strong>{{ $errors->first('bagType1') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    @endif
                <div class="row" id="add-bag">
                    <input type="hidden" name="bagcount" id="bagcount" value="{{(isset($bagArr) && count($bagArr)>0?count($bagArr):'1')}}" />
                    <input type="hidden" name="exactbagcount" id="exactbagcount" value="{{(isset($bagArr) && count($bagArr)>0?count($bagArr):'1')}}" />
                    <div class="form-group col-sm-6">
                        <button class="btn btn-info frontend-primary-btn col-md-4" type="button" onclick="addAnotherBag()">Add another bag</button>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <b>IMPORTANT:</b> Please ensure you select the correct bag size for your shipment to avoid extra charges.
                    </div>
                </div>
                <h3><span>Shipping Option</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <input type="hidden" name="shipOpt" id="shipOpt" value="{{(isset($orderDetails) && !empty($orderDetails->return_region)?'2':'1')}}" />
                        <button id="oneway" class="btn btn-info frontend-primary-btn {{(isset($orderDetails) && !empty($orderDetails->return_region)?'opt-inactive':'')}} col-md-5" value="1" type="button" onclick="onewayship()">One Way</button>
                        <button id="returnship" class="btn btn-info frontend-primary-btn {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'opt-inactive')}} col-md-5" value="2" style="margin-left: 10px" type="button" onclick="returnShip()">Return</button>
                    </div>
                </div>
                <div id="return-form" {{(isset($orderDetails) && !empty($orderDetails->return_region)?'style=display:block':'')}}>
                    <h3><span>Pickup Details</span></h3>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <input class="check-box" type="checkbox" name="is_same_pickup_addrs" id="is_same_pickup_addrs" value="1"  {{ (old('is_same_pickup_addrs') != 1 ? "":"checked") }} onclick="samePickupAddress();">
                            Tick this box if pickup details will be the same as the initial destination.
                            @if ($errors->has('is_same_pickup_addrs'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('is_same_pickup_addrs') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Region of pickup</label>
                            <select name="retccp_pickup_region" id="retccp_pickup_region" onchange="calculateTransitDays('2')" class="retele" {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} required="required">
                                <option value="">Select drop off</option>
                                <option value="" disabled>Victoria</option>
                                <option value="7" {{(isset($orderDetails) && $orderDetails->return_region == 'Melbourne CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Melbourne CBD/Metro</option>
                                <option value="6" {{(isset($orderDetails) && $orderDetails->return_region == 'Outer Melbourne'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Melbourne</option>
                                <option value="" disabled>South Australia</option>
                                <option value="1" {{(isset($orderDetails) && $orderDetails->return_region == 'Adelaide CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Adelaide CBD/Metro</option>
                                <option value="13" {{(isset($orderDetails) && $orderDetails->return_region == 'Outer Adelaide'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Adelaide</option>
                                <option value="" disabled>New South Wales</option>
                                <option value="5" {{(isset($orderDetails) && $orderDetails->return_region == 'Sydney CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Sydney CBD/Metro</option>
                                <option value="4" {{(isset($orderDetails) && $orderDetails->return_region == 'Outer Sydney'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Sydney</option>
                                <option value="" disabled>Queensland</option>
                                <option value="3" {{(isset($orderDetails) && $orderDetails->return_region == 'Brisbane/Gold Coast Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Brisbane/Gold Coast Metro</option>
                                <option value="2" {{(isset($orderDetails) && $orderDetails->return_region == 'Outer Brisbane'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Brisbane</option>
                                <option value="12" {{(isset($orderDetails) && $orderDetails->return_region == 'Sunshine Coast'?'selected="selected"':'')}}>&nbsp; &nbsp;Sunshine Coast</option>
                                <option value="" disabled>Tasmania</option>
                                <option value="9" {{(isset($orderDetails) && $orderDetails->return_region == 'Hobart CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart CBD/Metro</option>
                                <option value="14" {{(isset($orderDetails) && $orderDetails->return_region == 'Hobart Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart Outer</option>
                                <option value="8" {{(isset($orderDetails) && $orderDetails->return_region == 'Bridport'?'selected="selected"':'')}}>&nbsp; &nbsp;Bridport</option>
                                <option value="" disabled>Western Australia</option>
                                <option value="11" {{(isset($orderDetails) && $orderDetails->return_region == 'Perth CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth CBD/Metro</option>
                                <option value="10" {{(isset($orderDetails) && $orderDetails->return_region == 'Perth Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth Outer</option>

                            </select>
                            @if ($errors->has('retccp_pickup_region'))
                                <span class="help-block err">
                            <strong>{{ $errors->first('retccp_pickup_region') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" type="checkbox" {{ (old("place-ret-pickup") == 1 || (isset($orderDetails) && $orderDetails->return_place == 1) ? 'checked':'') }} name="place-ret-pickup" id="business-ret-pickup" value="1" onclick="checkPlace('ret-pickup',this);">Business
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" type="checkbox" {{ (old("place-ret-pickup") == 2 || (isset($orderDetails) && $orderDetails->return_place == 2) ? 'checked':'') }} name="place-ret-pickup" id="hotel-ret-pickup" value="2" onclick="checkPlace('ret-pickup',this);">Hotel / Resort
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" type="checkbox" {{ (old("place-ret-pickup") == 3 || (isset($orderDetails) && $orderDetails->return_place == 3) ? 'checked':'') }} name="place-ret-pickup" id="golf-ret-pickup" value="3" onclick="checkPlace('ret-pickup',this);">Golf Course
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-pickup" type="checkbox" {{ (old("place-ret-pickup") == 4 || (isset($orderDetails) && $orderDetails->return_place == 4) ? 'checked':'') }} name="place-ret-pickup" id="res-ret-pickup" value="4" onclick="checkPlace('ret-pickup',this);">Residential
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company Name</label>
                            <input type="text" name="retccp_company_name" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccp_company_name" value="{{(old('retccp_company_name')?old('retccp_company_name'):(isset($orderDetails) && $orderDetails->return_company_name?$orderDetails->return_company_name:''))}}" placeholder="Company Name"/>
                            @if ($errors->has('retccp_company_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_company_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Name</label>
                            <input type="text" name="retccp_contact_name" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccp_contact_name" value="{{(old('retccp_contact_name')?old('retccp_contact_name'):(isset($orderDetails) && $orderDetails->return_contact_name?$orderDetails->return_contact_name:''))}}"
                                   required="required" placeholder="Contact Name"/>
                            @if ($errors->has('retccp_contact_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_contact_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Phone No.</label>
                            <input type="tel" name="retccp_conatct_phone" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccp_conatct_phone" value="{{(old('retccp_conatct_phone')?old('retccp_conatct_phone'):(isset($orderDetails) && $orderDetails->return_phone_num?$orderDetails->return_phone_num:''))}}"
                                   required="required" placeholder="Contact Phone No."/>
                            @if ($errors->has('retccp_conatct_phone'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_conatct_phone') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Address</label>
                            <input type="text" name="retccp_address" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccp_address" value="{{(old('retccp_address')?old('retccp_address'):(isset($orderDetails) && $orderDetails->return_address?$orderDetails->return_address:''))}}"
                                  required="required" placeholder="Address"/>
                            @if ($errors->has('retccp_address'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_address') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Suburb</label>
                            <input type="text" name="retccp_suburb" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccp_suburb" value="{{(old('retccp_suburb')?old('retccp_suburb'):(isset($orderDetails) && $orderDetails->return_suburb?$orderDetails->return_suburb:''))}}"
                                   required="required" placeholder="Suburb"/>
                            @if ($errors->has('retccp_suburb'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_suburb') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Postcode</label>
                            <input type="tel" name="retccp_postcode" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccp_postcode" value="{{(old('retccp_postcode')?old('retccp_postcode'):(isset($orderDetails) && $orderDetails->return_postal_code?$orderDetails->return_postal_code:''))}}"
                                   required="required" placeholder="Postcode"/>
                            @if ($errors->has('retccp_postcode'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_postcode') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-8">
                        <textarea type="text" class="form-control retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccp_collection_notes" name="retccp_collection_notes"
                                  placeholder="Collection notes">{{(old('retccp_collection_notes')?old('retccp_collection_notes'):(isset($orderDetails) && $orderDetails->return_collection_note?$orderDetails->return_collection_note:''))}}</textarea>
                            @if ($errors->has('retccp_collection_notes'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_collection_notes') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Pickup collection date</label>
                            <input id="retfromDate" class="hasDatepicker date datepicker retele" data-provide="datepicker"  type="text" name="retccp_date" value="{{(old('retccp_date')?old('retccp_date'):(isset($orderDetails) && $orderDetails->return_date?date('m/d/Y',strtotime($orderDetails->return_date)):''))}}"
                                   required="required"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} placeholder="Pickup collection date"/>
                            @if ($errors->has('retccp_date'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_date') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <h3><span>Destination Details</span></h3>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <input class="check-box" type="checkbox" name="is_same_destination_addrs" id="is_same_destination_addrs" value="1"  {{ (old('is_same_pickup_addrs') != 1 ? "":"checked") }} onclick="sameDestinationAddress();">
                            Tick this box if destination details will be the same as the initial pickup location.
                            @if ($errors->has('is_same_destination_addrs'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('is_same_destination_addrs') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Region of drop off</label>
                            <select name="retccd_dropoff_region" id="retccd_dropoff_region" onchange="calculateTransitDays('2')" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} required="required">
                                <option value="">Select drop off</option>
                                <option value="" disabled>Victoria</option>
                                <option value="7" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Melbourne CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Melbourne CBD/Metro</option>
                                <option value="6" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Outer Melbourne'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Melbourne</option>
                                <option value="" disabled>South Australia</option>
                                <option value="1" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Adelaide CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Adelaide CBD/Metro</option>
                                <option value="13" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Outer Adelaide'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Adelaide</option>
                                <option value="" disabled>New South Wales</option>
                                <option value="5" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Sydney CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Sydney CBD/Metro</option>
                                <option value="4" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Outer Sydney'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Sydney</option>
                                <option value="" disabled>Queensland</option>
                                <option value="3" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Brisbane/Gold Coast Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Brisbane/Gold Coast Metro</option>
                                <option value="2" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Outer Brisbane'?'selected="selected"':'')}}>&nbsp; &nbsp;Outer Brisbane</option>
                                <option value="12" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Sunshine Coast'?'selected="selected"':'')}}>&nbsp; &nbsp;Sunshine Coast</option>
                                <option value="" disabled>Tasmania</option>
                                <option value="9" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Hobart CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart CBD/Metro</option>
                                <option value="14" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Hobart Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Hobart Outer</option>
                                <option value="8" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Bridport'?'selected="selected"':'')}}>&nbsp; &nbsp;Bridport</option>
                                <option value="" disabled>Western Australia</option>
                                <option value="11" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Perth CBD/Metro'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth CBD/Metro</option>
                                <option value="10" {{(isset($orderDetails) && $orderDetails->return_d_region == 'Perth Outer'?'selected="selected"':'')}}>&nbsp; &nbsp;Perth Outer</option>
                            </select>
                            @if ($errors->has('retccd_dropoff_region'))
                                <span class="help-block err">
                            <strong>{{ $errors->first('retccd_dropoff_region') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" type="checkbox" {{ (old("place-ret-dropoff") == 1 || (isset($orderDetails) && $orderDetails->return_d_place == 1) ? 'checked':'') }} name="place-ret-dropoff" id="business-ret-dropoff" value="1" onclick="checkPlace('ret-dropoff',this);">Business
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" type="checkbox" {{ (old("place-ret-dropoff") == 2 || (isset($orderDetails) && $orderDetails->return_d_place == 2) ? 'checked':'') }} name="place-ret-dropoff" id="hotel-ret-dropoff" value="2" onclick="checkPlace('ret-dropoff',this);">Hotel / Resort
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" type="checkbox" {{ (old("place-ret-dropoff") == 3 || (isset($orderDetails) && $orderDetails->return_d_place == 3) ? 'checked':'') }} name="place-ret-dropoff" id="golf-ret-dropoff" value="3" onclick="checkPlace('ret-dropoff',this);">Golf Course
                        </div>
                        <div class="form-group col-md-3">
                            <input class="check-box place-ret-dropoff" type="checkbox" {{ (old("place-ret-dropoff") == 4 || (isset($orderDetails) && $orderDetails->return_d_place == 4) ? 'checked':'') }} name="place-ret-dropoff" id="res-ret-dropoff" value="4" onclick="checkPlace('ret-dropoff',this);">Residential
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company Name</label>
                            <input type="text" name="retccd_company_name" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccd_company_name" value="{{(old('retccd_company_name')?old('retccd_company_name'):(isset($orderDetails) && $orderDetails->return_d_company_name?$orderDetails->return_d_company_name:''))}}" placeholder="Company Name"/>
                            @if ($errors->has('retccd_company_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_company_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Name</label>
                            <input type="text" name="retccd_contact_name" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccd_contact_name" value="{{(old('retccd_contact_name')?old('retccd_contact_name'):(isset($orderDetails) && $orderDetails->return_d_contact_name?$orderDetails->return_d_contact_name:''))}}"
                                   required="required" placeholder="Contact Name"/>
                            @if ($errors->has('retccd_contact_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_contact_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Phone No.</label>
                            <input type="tel" name="retccd_conatct_phone" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccd_conatct_phone" value="{{(old('retccd_conatct_phone')?old('retccd_conatct_phone'):(isset($orderDetails) && $orderDetails->return_d_phone_num?$orderDetails->return_d_phone_num:''))}}"
                                   required="required" placeholder="Contact Phone No."/>
                            @if ($errors->has('retccd_conatct_phone'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_conatct_phone') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Address</label>
                            <input type="text" name="retccd_address" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccd_address" value="{{(old('retccd_address')?old('retccd_address'):(isset($orderDetails) && $orderDetails->return_d_address?$orderDetails->return_d_address:''))}}"
                                  required="required" placeholder="Address"/>
                            @if ($errors->has('retccd_address'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_address') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Suburb</label>
                            <input type="text" name="retccd_suburb" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccd_suburb" value="{{(old('retccd_suburb')?old('retccd_suburb'):(isset($orderDetails) && $orderDetails->return_d_suburb?$orderDetails->return_d_suburb:''))}}"
                                   required="required" placeholder="Suburb"/>
                            @if ($errors->has('retccd_suburb'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_suburb') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Postcode</label>
                            <input type="tel" name="retccd_postcode" class="retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} id="retccd_postcode" value="{{(old('retccd_postcode')?old('retccd_postcode'):(isset($orderDetails) && $orderDetails->return_d_postal_code?$orderDetails->return_d_postal_code:''))}}"
                                   required="required" placeholder="Postcode"/>
                            @if ($errors->has('retccd_postcode'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_postcode') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-8">
                        <textarea type="text" class="form-control retele"  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'disabled')}} name="retccd_collection_notes"
                                  placeholder="Delivery notes">{{(old('retccd_collection_notes')?old('retccd_collection_notes'):(isset($orderDetails) && $orderDetails->return_d_note?$orderDetails->return_d_note:''))}}</textarea>
                            @if ($errors->has('retccd_collection_notes'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_collection_notes') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>
                </div>
                <h3><span>Calculate Quote</span></h3>
                <div class="row" id="calculateQuote">
                    <div class="form-group col-sm-6">
                        <h4><b>Outgoing Shipment</b></h4>
                        <div class="radio">
                            <label><input type="radio" name="outshipment" value="1" {{(isset($orderDetails) && !empty($orderDetails->outgoing_shipment) && $orderDetails->outgoing_shipment == 1?'checked':(isset($orderDetails) && !empty($orderDetails->outgoing_shipment) && $orderDetails->outgoing_shipment == 2?'':'checked'))}}>Standard courier</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="outshipment" value="2" {{(isset($orderDetails) && !empty($orderDetails->outgoing_shipment) && $orderDetails->outgoing_shipment == 2?'checked':'')}}>Express courier</label>
                        </div>
                        <div id="onewayshiptime">
                            @if(!empty($orderDetails->transit_days_out))
                            Estimated shipment time: {{$orderDetails->transit_days_out}} business days
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-sm-6 retShippment  {{(isset($orderDetails) && !empty($orderDetails->return_region)?'':'hide')}}">
                        <h4><b>Return Shipment</b></h4>
                        <div class="radio">
                            <label><input type="radio" name="returnshipment" value="1" {{(isset($orderDetails) && !empty($orderDetails->return_shipment) && $orderDetails->return_shipment == 1?'checked':(isset($orderDetails) && !empty($orderDetails->return_shipment) && $orderDetails->return_shipment == 2?'':'checked'))}}>Standard courier</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="returnshipment" value="2" {{(isset($orderDetails) && !empty($orderDetails->return_shipment) && $orderDetails->return_shipment == 2?'checked':'')}}>Express courier</label>
                        </div>
                        <div id="returnshiptime">
                            @if(!empty($orderDetails->transit_days_ret))
                                Estimated shipment time: {{$orderDetails->transit_days_ret}} business days
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Voucher Code</label>
                        <input type="text" name="voucher_code" class="col-md-8" id="voucher_code" value="{{(isset($orderDetails) && $orderDetails->offer_Code?$orderDetails->offer_Code:'')}}"
                               placeholder="Voucher Code"/>
                    </div>
                    <div class="form-group col-sm-1">
                        <button type="button" class="btn-with-label btn btn-info frontend-primary-btn col-md-12" onclick="getVoucherCodeDiscount();">Apply</button>
                    </div>
                    <div class="form-group col-sm-7" style="margin-top: 30px">
                        <span id="voucher-message" class="alert"></span>
                        <input type="hidden" name="discount-amount" id="discount-amount" value="0" />
                    </div>
                </div>
                <div class="submit-btn">
                    <input class="check-box" name="iTerms" value="1" id="iTerms" required="required" type="checkbox"><a target="_blank" href="{{url('../terms-of-service-tssclubcourier/')}}">Accept Terms and Conditions</a>
                    <br /><br />
                    <input type="submit" value="Proceed"/>
                </div>
            </form>
        </div>
        <div id="popupbs">
            <div class="modal fade" id="pickupInfo" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">

                            {{ csrf_field() }}
                            <input type="hidden" name="idOffer" value="" id="idOffer" />
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Region of pickup</h4>
                            </div>
                            <div class="modal-body">
                                <p>Select the region which matches the location you need your bag picked up from. <a target="_blank" href="{{url('../courierdelivery-zones/')}}">Click here</a> to see what region applies to your collection location</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info" data-dismiss="modal">Okay</button>
                            </div>
                    </div>
                </div>
            </div>
        </div>
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
    </section>
@endsection