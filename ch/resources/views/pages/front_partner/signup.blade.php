@extends('layouts.front_partner.partner_login')

@section('content')
<section id="hire-page-banner">
    <div class="overlap-black-shadow"></div>
    <div class="text-caption">
        <div class="container">
            <div class="admin-form theme-info form-width" id="login1">
                <div class="panel panel-info sign-in">
                    <div class="panel-heading clearfix p10 ph15">
                       <i class="fa fa-user-plus" aria-hidden="true"></i>Sign Up
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                            </div>
                            @elseif($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <form method="POST" action="{{ url('/partner/signup_partner') }}">
                                {!! csrf_field() !!}
                                <div class="panel-body bg-light p30">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="section row form-group">
                                                <label for="inputStandard" class="col-xs-4 col-sm-4 control-label">Name</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="gui-input form-control" required="required" placeholder="Please type partner name...">
                                                    @if ($errors->has('name'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="section row form-group">
                                                <label for="email" class="col-xs-4 col-sm-4 control-label">E-Mail</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="email" name="email" id="email" class="gui-input form-control" placeholder="Please type email..." value="{{ old('email') }}" required="required">
                                                    @if ($errors->has('email'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="section row form-group">
                                                <label for="password" class="col-xs-4 col-sm-4 control-label">Password</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="password" name="password" id="password" class="gui-input form-control" placeholder="Please type password..." required="required">
                                                    @if ($errors->has('password'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>     
											<div class="section row form-group">
                                                <label for="inputStandard" class="col-xs-4 col-sm-4 control-label">Country</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <select id="country" name="country" class="gui-input form-control" required="required">
                                                        <option value="0">Select Country</option>
                                                        @if ($countriesAry->count() > 0)
                                                            @foreach ($countriesAry as $country)
                                                                <option value="{{$country->id}}" {{(old('country') == $country->id ? "selected=selected":"")}}>{{$country->name}}</option>
                                                            @endforeach
                                                        @endif

                                                    </select>
                                                    @if ($errors->has('country'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('country') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
										</div>
										<div class="col-md-6">
                                            <div class="section row form-group">
                                                <label for="inputStandard" class="col-xs-4 col-sm-4 control-label">Address</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="text" name="address" id="address" value="{{ old('address') }}" class="gui-input form-control" required="required" placeholder="Please type address...">
                                                    @if ($errors->has('address'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('address') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="section row form-group">
                                                <label for="inputStandard" class="col-xs-4 col-sm-4 control-label">State</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="text" name="state" id="state" value="{{ old('state') }}" class="gui-input form-control" required="required" placeholder="Please type state...">
                                                    @if ($errors->has('state'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('state') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                             <div class="section row form-group">
                                                <label for="inputStandard" class="col-xs-4 col-sm-4 control-label">ZIP Code</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode') }}" class="gui-input form-control" required="required" placeholder="Please type zip/postal code...">
                                                    @if ($errors->has('zipcode'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('zipcode') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
											
                                            <div class="section row form-group">
                                                <label for="inputStandard" class="col-xs-4 col-sm-4 control-label">Contact</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="text" maxlength="10" name="contact_no" id="contact_no" value="{{ old('contact_no') }}" class="gui-input form-control" placeholder="Please type contact number...">
                                                    @if ($errors->has('contact_no'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('contact_no') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="section row form-group">
                                                <div class="col-sm-12">
                                                    <input class="check-box" type="checkbox" name="Terms" value="1" id="Terms" required="required"  {{ (old("is_same_pickup_addrs") == 1 ? "checked=checked":"") }} ><a target="_blank" href="{{url('../affiliate-terms-and-conditions/')}}">Accept Terms and Conditions</a>
                                                    @if ($errors->has('Terms'))
                                                        <span class="help-block err">
                                                            <strong>{{ $errors->first('Terms') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="panel-footer clearfix">
                                        <button type="submit" class="button btn-primary mr10 pull-right">Sign Up</button>
                                        <a class="btn btn-link pull-left" href="{{ url('/partner/login') }}"> Already have account?</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection