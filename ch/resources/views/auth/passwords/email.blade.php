@extends('layouts.login')

<!-- Main Content -->
@section('content')
<!-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Reset Password</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->

<div class="admin-form theme-info" id="login1">

          <div class="row mb15 table-layout">

            <div class="col-xs-6 va-m pln">
              <a href="{{url('/')}}" title="Return to Dashboard">
                <h1 class="text-logo">Golf Club Hire</h1>
              </a>
            </div>

            <div class="col-xs-6 text-right va-b pr5">
              <div class="login-links">
                <a href="{{url('/login')}}" title="Sign In">Sign In</a>
                <span class="text-white"><!-- | </span>
                <a href="{{url('/register')}}" class="" title="Register">Register</a>-->
              </div>

            </div>

          </div>

          <div class="panel panel-info mt10 br-n">

            <div class="panel-heading heading-border bg-white">
              <span class="panel-title">
                <i class="fa fa-sign-in"></i>Reset Password</span>
              
            </div>
@if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
            <!-- end .form-header section -->
            <form method="POST" action="{{ url('/password/email') }}" id="contact">
            {{ csrf_field() }}
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
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
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
                <button type="submit" class="button btn-primary mr10 pull-right">Send Password Reset Link</button>
              </div>
              <!-- end .form-footer section -->
            </form>
          </div>
        </div>

@endsection
