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
                    <div class="form-group col-md-4">
                        <label>Name</label>
                        <input type="text" name="ccp_name" value="{{old('ccp_name')}}"
                               required="required" placeholder="Name"/>
                        @if ($errors->has('ccp_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email Address</label>
                        <input type="email" name="ccp_email" value="{{old('ccp_email')}}"
                               required="required" placeholder="Email"/>
                        @if ($errors->has('ccp_email'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_email') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Phone No.</label>
                        <input type="tel" name="ccp_phone" value="{{old('ccp_phone')}}"
                               required="required" placeholder="Phone No."/>
                        @if ($errors->has('ccp_phone'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_phone') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <h3><span>Pickup Details</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label>Region of pickup</label>
                        <select name="ccp_pickup_region" id="ccp_pickup_region" required="required">
                            <option value="">Select pickup region</option>
                            <option value="" disabled>Victoria</option>
                            <option value="7">&nbsp; &nbsp;Melbourne CBD/Metro</option>
                            <option value="6">&nbsp; &nbsp;Outer Melbourne</option>
                            <option value="" disabled>South Australia</option>
                            <option value="1">&nbsp; &nbsp;Adelaide CBD/Metro</option>
                            <option value="12">&nbsp; &nbsp;Outer Adelaide</option>
                            <option value="" disabled>New South Wales</option>
                            <option value="5">&nbsp; &nbsp;Sydney CBD/Metro</option>
                            <option value="4">&nbsp; &nbsp;Outer Sydney</option>
                            <option value="" disabled>Queensland</option>
                            <option value="3">&nbsp; &nbsp;Brisbane CBD/Metro</option>
                            <option value="2">&nbsp; &nbsp;Outer Brisbane</option>
                            <option value="" disabled>Tasmania</option>
                            <option value="9">&nbsp; &nbsp;Hobart CBD/Metro</option>
                            <option value="13">&nbsp; &nbsp;Hobart Outer</option>
                            <option value="8">&nbsp; &nbsp;Bridport</option>
                            <option value="" disabled>Western Australia</option>
                            <option value="11">&nbsp; &nbsp;Perth CBD</option>
                            <option value="10">&nbsp; &nbsp;Perth metropolitan</option>
                            {{--@if ($countriesAry->count() > 0)
                                @foreach ($countriesAry as $country)
                                    <option value="{{$country->id}}" {{(old('buyer_country') == $country->id ? "selected=selected":($country->id == '13'?"selected=selected":""))}}>{{$country->name}}</option>
                                @endforeach
                            @endif--}}
                        </select>
                        @if ($errors->has('ccp_pickup_region'))
                            <span class="help-block err">
                            <strong>{{ $errors->first('ccp_pickup_region') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Company Name</label>
                        <input type="text" name="ccp_company_name" id="ccp_company_name" value="{{old('ccp_company_name')}}" placeholder="Company Name"/>
                        @if ($errors->has('ccp_company_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_company_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Name</label>
                        <input type="text" name="ccp_contact_name" id="ccp_contact_name" value="{{old('ccp_contact_name')}}"
                               required="required" placeholder="Contact Name"/>
                        @if ($errors->has('ccp_contact_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_contact_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Phone No.</label>
                        <input type="tel" name="ccp_conatct_phone" id="ccp_conatct_phone" value="{{old('ccp_conatct_phone')}}"
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
                        <input type="text" name="ccp_address" id="ccp_address" value="{{old('ccp_address')}}"
                               placeholder="Address"/>
                        @if ($errors->has('ccp_address'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_address') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Suburb</label>
                        <input type="text" name="ccp_suburb" id="ccp_suburb" value="{{old('ccp_suburb')}}"
                               required="required" placeholder="Suburb"/>
                        @if ($errors->has('ccp_suburb'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_suburb') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Postcode</label>
                        <input type="tel" name="ccp_postcode" id="ccp_postcode" value="{{old('ccp_postcode')}}"
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
                                  placeholder="Collection notes"></textarea>
                        @if ($errors->has('ccp_collection_notes'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccp_collection_notes') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Pickup collection date</label>
                        <input id="fromDate" class="hasDatepicker date datepicker" data-provide="datepicker"  type="text" name="ccp_date" value="{{old('ccp_date')}}"
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
                        <select name="ccd_dropoff_region" id="ccd_dropoff_region" required="required">
                            <option value="">Select drop off</option>
                            <option value="" disabled>Victoria</option>
                            <option value="7">&nbsp; &nbsp;Melbourne CBD/Metro</option>
                            <option value="6">&nbsp; &nbsp;Outer Melbourne</option>
                            <option value="" disabled>South Australia</option>
                            <option value="1">&nbsp; &nbsp;Adelaide CBD/Metro</option>
                            <option value="12">&nbsp; &nbsp;Outer Adelaide</option>
                            <option value="" disabled>New South Wales</option>
                            <option value="5">&nbsp; &nbsp;Sydney CBD/Metro</option>
                            <option value="4">&nbsp; &nbsp;Outer Sydney</option>
                            <option value="" disabled>Queensland</option>
                            <option value="3">&nbsp; &nbsp;Brisbane CBD/Metro</option>
                            <option value="2">&nbsp; &nbsp;Outer Brisbane</option>
                            <option value="" disabled>Tasmania</option>
                            <option value="9">&nbsp; &nbsp;Hobart CBD/Metro</option>
                            <option value="13">&nbsp; &nbsp;Hobart Outer</option>
                            <option value="8">&nbsp; &nbsp;Bridport</option>
                            <option value="" disabled>Western Australia</option>
                            <option value="11">&nbsp; &nbsp;Perth CBD</option>
                            <option value="10">&nbsp; &nbsp;Perth metropolitan</option>
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
                    <div class="form-group col-md-4">
                        <label>Company Name</label>
                        <input type="text" name="ccd_company_name" id="ccd_company_name" value="{{old('ccd_company_name')}}" placeholder="Company Name"/>
                        @if ($errors->has('ccd_company_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_company_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Name</label>
                        <input type="text" name="ccd_contact_name" id="ccd_contact_name" value="{{old('ccd_contact_name')}}"
                               required="required" placeholder="Contact Name"/>
                        @if ($errors->has('ccd_contact_name'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_contact_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contact Phone No.</label>
                        <input type="tel" name="ccd_conatct_phone" id="ccd_conatct_phone" value="{{old('ccd_conatct_phone')}}"
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
                        <input type="text" name="ccd_address" id="ccd_address" value="{{old('ccd_address')}}"
                               placeholder="Address"/>
                        @if ($errors->has('ccd_address'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_address') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Suburb</label>
                        <input type="text" name="ccd_suburb" id="ccd_suburb" value="{{old('ccd_suburb')}}"
                               required="required" placeholder="Suburb"/>
                        @if ($errors->has('ccd_suburb'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_suburb') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Postcode</label>
                        <input type="tel" name="ccd_postcode" id="ccd_postcode" value="{{old('ccd_postcode')}}"
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
                                  placeholder="Delivery notes"></textarea>
                        @if ($errors->has('ccd_collection_notes'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('ccd_collection_notes') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <h3><span>Bag Details</span></h3>
                <div class="row" id="bag1">
                    <div class="form-group col-sm-5">
                        <label>Bag title</label>
                        <input type="text" name="bagTitle1" value="{{old('bagTitle1')}}" placeholder="Bag title" required="required"/>
                        @if ($errors->has('bagTitle1'))
                            <span class="help-block err">
                                        <strong>{{ $errors->first('bagTitle1') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group col-sm-5">
                        <label>Bag type/size</label>
                        <select name="bagType1" required="required">
                            <option value="1">Standard bag (123cmx30cmx30cm)</option>
                            <option value="2">Large bag (132cmx38cmx30cm)</option>
                        </select>
                        @if ($errors->has('bagType1'))
                            <span class="help-block err">
                            <strong>{{ $errors->first('bagType1') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row" id="add-bag">
                    <input type="hidden" name="bagcount" id="bagcount" value="1" />
                    <div class="form-group col-sm-6">
                        <button class="btn btn-info frontend-primary-btn col-md-4" type="button" onclick="addAnotherBag()">Add another bag</button>
                    </div>
                </div>
                <h3><span>Shipping Option</span></h3>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <input type="hidden" name="shipOpt" id="shipOpt" value="1" />
                        <button id="oneway" class="btn btn-info frontend-primary-btn col-md-5" value="1" type="button" onclick="onewayship()">One Way</button>
                        <button id="returnship" class="btn btn-info frontend-primary-btn opt-inactive col-md-5" value="2" style="margin-left: 10px" type="button" onclick="returnShip()">Return</button>
                    </div>
                </div>
                <div id="return-form">
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
                            <select name="retccp_pickup_region" id="retccp_pickup_region" class="retele" disabled required="required">
                                <option value="">Select pickup region</option>
                                <option value="" disabled>Victoria</option>
                                <option value="7">&nbsp; &nbsp;Melbourne CBD/Metro</option>
                                <option value="6">&nbsp; &nbsp;Outer Melbourne</option>
                                <option value="" disabled>South Australia</option>
                                <option value="1">&nbsp; &nbsp;Adelaide CBD/Metro</option>
                                <option value="12">&nbsp; &nbsp;Outer Adelaide</option>
                                <option value="" disabled>New South Wales</option>
                                <option value="5">&nbsp; &nbsp;Sydney CBD/Metro</option>
                                <option value="4">&nbsp; &nbsp;Outer Sydney</option>
                                <option value="" disabled>Queensland</option>
                                <option value="3">&nbsp; &nbsp;Brisbane CBD/Metro</option>
                                <option value="2">&nbsp; &nbsp;Outer Brisbane</option>
                                <option value="" disabled>Tasmania</option>
                                <option value="9">&nbsp; &nbsp;Hobart CBD/Metro</option>
                                <option value="13">&nbsp; &nbsp;Hobart Outer</option>
                                <option value="8">&nbsp; &nbsp;Bridport</option>
                                <option value="" disabled>Western Australia</option>
                                <option value="11">&nbsp; &nbsp;Perth CBD</option>
                                <option value="10">&nbsp; &nbsp;Perth metropolitan</option>
                                {{--@if ($countriesAry->count() > 0)
                                    @foreach ($countriesAry as $country)
                                        <option value="{{$country->id}}" {{(old('buyer_country') == $country->id ? "selected=selected":($country->id == '13'?"selected=selected":""))}}>{{$country->name}}</option>
                                    @endforeach
                                @endif--}}
                            </select>
                            @if ($errors->has('retccp_pickup_region'))
                                <span class="help-block err">
                            <strong>{{ $errors->first('retccp_pickup_region') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company Name</label>
                            <input type="text" name="retccp_company_name" class="retele" disabled id="retccp_company_name" value="{{old('retccp_company_name')}}" placeholder="Company Name"/>
                            @if ($errors->has('retccp_company_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_company_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Name</label>
                            <input type="text" name="retccp_contact_name" class="retele" disabled id="retccp_contact_name" value="{{old('retccp_contact_name')}}"
                                   required="required" placeholder="Contact Name"/>
                            @if ($errors->has('retccp_contact_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_contact_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Phone No.</label>
                            <input type="tel" name="retccp_conatct_phone" class="retele" disabled id="retccp_conatct_phone" value="{{old('retccp_conatct_phone')}}"
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
                            <input type="text" name="retccp_address" class="retele" disabled id="retccp_address" value="{{old('retccp_address')}}"
                                  required="required" placeholder="Address"/>
                            @if ($errors->has('retccp_address'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_address') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Suburb</label>
                            <input type="text" name="retccp_suburb" class="retele" disabled id="retccp_suburb" value="{{old('retccp_suburb')}}"
                                   required="required" placeholder="Suburb"/>
                            @if ($errors->has('retccp_suburb'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_suburb') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Postcode</label>
                            <input type="tel" name="retccp_postcode" class="retele" disabled id="retccp_postcode" value="{{old('retccp_postcode')}}"
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
                        <textarea type="text" class="form-control retele" disabled id="retccp_collection_notes" name="retccp_collection_notes"
                                  placeholder="Collection notes"></textarea>
                            @if ($errors->has('retccp_collection_notes'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccp_collection_notes') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Pickup collection date</label>
                            <input id="retfromDate" class="hasDatepicker date datepicker retele" data-provide="datepicker"  type="text" name="retccp_date" value="{{old('retccp_date')}}"
                                   required="required" disabled placeholder="Pickup collection date"/>
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
                            <select name="retccd_dropoff_region" id="retccd_dropoff_region" class="retele" disabled required="required">
                                <option value="">Select drop off</option>
                                <option value="" disabled>Victoria</option>
                                <option value="7">&nbsp; &nbsp;Melbourne CBD/Metro</option>
                                <option value="6">&nbsp; &nbsp;Outer Melbourne</option>
                                <option value="" disabled>South Australia</option>
                                <option value="1">&nbsp; &nbsp;Adelaide CBD/Metro</option>
                                <option value="12">&nbsp; &nbsp;Outer Adelaide</option>
                                <option value="" disabled>New South Wales</option>
                                <option value="5">&nbsp; &nbsp;Sydney CBD/Metro</option>
                                <option value="4">&nbsp; &nbsp;Outer Sydney</option>
                                <option value="" disabled>Queensland</option>
                                <option value="3">&nbsp; &nbsp;Brisbane CBD/Metro</option>
                                <option value="2">&nbsp; &nbsp;Outer Brisbane</option>
                                <option value="" disabled>Tasmania</option>
                                <option value="9">&nbsp; &nbsp;Hobart CBD/Metro</option>
                                <option value="13">&nbsp; &nbsp;Hobart Outer</option>
                                <option value="8">&nbsp; &nbsp;Bridport</option>
                                <option value="" disabled>Western Australia</option>
                                <option value="11">&nbsp; &nbsp;Perth CBD</option>
                                <option value="10">&nbsp; &nbsp;Perth metropolitan</option>
                                {{--@if ($countriesAry->count() > 0)
                                    @foreach ($countriesAry as $country)
                                        <option value="{{$country->id}}" {{(old('buyer_country') == $country->id ? "selected=selected":($country->id == '13'?"selected=selected":""))}}>{{$country->name}}</option>
                                    @endforeach
                                @endif--}}
                            </select>
                            @if ($errors->has('retccd_dropoff_region'))
                                <span class="help-block err">
                            <strong>{{ $errors->first('retccd_dropoff_region') }}</strong>
                        </span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Company Name</label>
                            <input type="text" name="retccd_company_name" class="retele" disabled id="retccd_company_name" value="{{old('retccd_company_name')}}" placeholder="Company Name"/>
                            @if ($errors->has('retccd_company_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_company_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Name</label>
                            <input type="text" name="retccd_contact_name" class="retele" disabled id="retccd_contact_name" value="{{old('retccd_contact_name')}}"
                                   required="required" placeholder="Contact Name"/>
                            @if ($errors->has('retccd_contact_name'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_contact_name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contact Phone No.</label>
                            <input type="tel" name="retccd_conatct_phone" class="retele" disabled id="retccd_conatct_phone" value="{{old('retccd_conatct_phone')}}"
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
                            <input type="text" name="retccd_address" class="retele" disabled id="retccd_address" value="{{old('retccd_address')}}"
                                  required="required" placeholder="Address"/>
                            @if ($errors->has('retccd_address'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_address') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Suburb</label>
                            <input type="text" name="retccd_suburb" class="retele" disabled id="retccd_suburb" value="{{old('retccd_suburb')}}"
                                   required="required" placeholder="Suburb"/>
                            @if ($errors->has('retccd_suburb'))
                                <span class="help-block err">
                                        <strong>{{ $errors->first('retccd_suburb') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-4">
                            <label>Postcode</label>
                            <input type="tel" name="retccd_postcode" class="retele" disabled id="retccd_postcode" value="{{old('retccd_postcode')}}"
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
                        <textarea type="text" class="form-control retele" disabled name="retccd_collection_notes"
                                  placeholder="Delivery notes"></textarea>
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
                            <label><input type="radio" name="outshipment" value="1" checked>Standard courier</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="outshipment" value="2">Express courier</label>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 retShippment hide">
                        <h4><b>Return Shipment</b></h4>
                        <div class="radio">
                            <label><input type="radio" name="returnshipment" value="1" checked>Standard courier</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="returnshipment" value="2">Express courier</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>Voucher Code</label>
                        <input type="text" name="voucher_code" class="col-md-8" id="voucher_code" value=""
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
                    <input type="submit" value="Proceed"/>
                </div>
            </form>
        </div>
    </section>
@endsection