@extends('layouts.login')

@section('content')

<div class="admin-form theme-info mw700" style="margin-top: 3%;" id="login1">

          <div class="row mb15 table-layout">

            <div class="col-xs-6 va-m pln">
              <a href="{{url('/')}}" title="Return to Dashboard">
                <h1 class="text-logo">Golf Club Hire</h1>
              </a>
            </div>

            <div class="col-xs-6 text-right va-b pr5">
              <div class="login-links">
               <!--  <a href="{{url('/login')}}" title="Sign In">Sign In</a>
                <span class="text-white"> | </span>
                <a href="{{url('/register')}}" class="active" class="" title="Register">Register</a> -->
              </div>

            </div>

          </div>

          <div class="panel panel-info mt10 br-n">

            <div class="panel-heading heading-border bg-white">
            <span class="panel-title">
                <i class="fa fa-sign-in"></i>Register</span>
              <div class="section row mn">
                
            </div>

            <form method="POST" action="{{ url('/register') }}" id="account2">
            {!! csrf_field() !!}
              <div class="panel-body p25 bg-light">
                <div class="section-divider mt10 mb40">
                  <span>Set up your account</span>
                </div>
                <!-- .section-divider -->

                <div class="section row">
                  <div class="col-md-12">
                    <label for="name" class="field prepend-icon">
                      <input type="text" name="name" id="name" value="{{ old('name') }}" class="gui-input" placeholder="Enter your name...">
                      <label for="name" class="field-icon">
                        <i class="fa fa-user"></i>
                      </label>
                    </label>
                    @if ($errors->has('name'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                  </div>
                  <!-- end section -->
                </div>
                <!-- end .section row section -->

                <div class="section">
                  <label for="email" class="field prepend-icon">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="gui-input" placeholder="Your email address...">
                    <label for="email" class="field-icon">
                      <i class="fa fa-envelope"></i>
                    </label>
                  </label>
                                @if ($errors->has('email'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                </div>
                <!-- end section -->

                <div class="section">
                  <label for="password" class="field prepend-icon">
                    <input type="password" name="password" id="password" class="gui-input" placeholder="Create a password...">
                    <label for="password" class="field-icon">
                      <i class="fa fa-unlock-alt"></i>
                    </label>
                  </label>
                                @if ($errors->has('password'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                </div>
                <!-- end section -->

                <div class="section">
                  <label for="password_confirmation" class="field prepend-icon">
                    <input type="password" name="password_confirmation" id="password-confirm" class="gui-input" placeholder="Retype your password...">
                    <label for="password_confirmation" class="field-icon">
                      <i class="fa fa-lock"></i>
                    </label>
                  </label>
                  @if ($errors->has('password_confirmation'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                </div>
                <!-- end section -->
<div class="section row">
                  <div class="col-md-12">
                    <label for="phone" class="field prepend-icon">
                      <input type="text" name="phone" id="phone" class="gui-input" value="{{ old('phone') }}" placeholder="Contact No...">
                      <label for="phone" class="field-icon">
                        <i class="fa fa-mobile"></i>
                      </label>
                    </label>
                  @if ($errors->has('phone'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                  </div>
                  <!-- end section -->
                </div>

<div class="section row">
                  <div class="col-md-12">
                    <label for="country" class="field prepend-icon">
                    <select name="country" id="country" class="form-control">
                      
                    </select>
                      <!-- <input type="text" name="country" id="country" class="gui-input" value="{{ old('country') }}" placeholder="Country..."> -->
                      <!-- <label for="country" class="field-icon">
                        <i class="fa fa-globe"></i>
                      </label> -->
                    </label>
                  @if ($errors->has('country'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                  </div>
                  <!-- end section -->
                </div>

                <div class="section row">
                  <div class="col-md-12">
                    <label for="state" class="field prepend-icon">
                    <select name="state" id="state" class="form-control">
                      <option value="">Select State</option>
                    </select>
                      <!-- <input type="text" name="state" id="state" class="gui-input" value="{{ old('state') }}" placeholder="State..."> -->
                      <!-- <label for="state" class="field-icon">
                        <i class="fa fa-map-marker"></i>
                      </label> -->
                    </label>
                  @if ($errors->has('state'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('state') }}</strong>
                                    </span>
                                @endif
                  </div>
                  <!-- end section -->
                </div>

                <div class="section row">
                  <div class="col-md-12">
                    <label for="address" class="field prepend-icon">
                      <input type="text" name="address" id="address" class="gui-input" value="{{ old('address') }}" placeholder="Address...">
                      <label for="address" class="field-icon">
                        <i class="fa fa-building"></i>
                      </label>
                    </label>
                  @if ($errors->has('address'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                  </div>
                  <!-- end section -->
                </div>
                <div class="section row">
                  <div class="col-md-12">
                    <label for="zipcode" class="field prepend-icon">
                      <input type="text" name="zipcode" id="zipcode" class="gui-input" value="{{ old('zipcode') }}" placeholder="Zip Code...">
                      <label for="zipcode" class="field-icon">
                        <i class="fa fa-flag"></i>
                      </label>
                    </label>
                  @if ($errors->has('zipcode'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('zipcode') }}</strong>
                                    </span>
                                @endif
                  </div>
                  <!-- end section -->
                </div>
                

              </div>
              <!-- end .form-body section -->
              <div class="panel-footer clearfix">
                <button type="submit" class="button btn-primary pull-right">Create Account</button>
              </div>
              <!-- end .form-footer section -->
            </form>
          </div>
        </div>

@endsection