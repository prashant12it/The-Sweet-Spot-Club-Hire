@extends('layouts.login')

@section('content')
<div class="admin-form theme-info" id="login1">

          <div class="row mb15 table-layout">

            <div class="col-xs-6 va-m pln">
              <a href="{{url('/')}}" title="Return to Dashboard">
                <h1 class="text-logo">Golf Club Hire</h1>
              </a>
            </div>

            <div class="col-xs-6 text-right va-b pr5">
              <div class="login-links">
                <!-- <a href="{{url('/login')}}" class="active" title="Sign In">Sign In</a>
                <span class="text-white"> | </span>
                <a href="{{url('/register')}}" class="" title="Register">Register</a> -->
              </div>

            </div>

          </div>

          <div class="panel panel-info mt10 br-n">

            <div class="panel-heading heading-border bg-white">
              <span class="panel-title">
                <i class="fa fa-sign-in"></i>SignIn</span>
              
            </div>

            <!-- end .form-header section -->
            <form method="POST" action="{{ url('/login') }}" id="contact">
            {!! csrf_field() !!}
              <div class="panel-body bg-light p30">
                <div class="row">
                <div class="col-sm-2 pr30"></div>
                  <div class="col-sm-8 pr30">
                    <div class="section">
                      <label for="email" class="field-label text-muted fs18 mb10">E-Mail Address</label>
                      <label for="email" class="field prepend-icon">
                        <input type="email" name="email" id="email" class="gui-input" placeholder="Enter email id" value="{{ old('email') }}">
                        <label for="email" class="field-icon">
                          <i class="fa fa-user"></i>
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

                    
                      <label for="password" class="field-label text-muted fs18 mb10">Password</label>
                      <label for="password" class="field prepend-icon">
                        <input type="password" name="password" id="password" class="gui-input" placeholder="Enter password">
                        <label for="password" class="field-icon">
                          <i class="fa fa-lock"></i>
                        </label>

                      </label>
                      @if ($errors->has('password'))
                                    <span class="help-block err">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                    </div>
                    <!-- end section -->

                  </div>
                  <div class="col-sm-2 pr30"></div>
                </div>
              </div>
              <!-- end .form-body section -->
              <div class="panel-footer clearfix p10 ph15">
                <button type="submit" class="button btn-primary mr10 pull-right">Sign In</button>
                <label class="switch ib switch-primary pull-left input-align mt10">
                  <input type="checkbox" name="remember" id="remember" checked>
                  <label for="remember" data-on="YES" data-off="NO"></label>
                  <span>Remember me</span>
                </label>
                <a class="btn btn-link pull-left" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
              </div>
              <!-- end .form-footer section -->
            </form>
          </div>
        </div>

@endsection