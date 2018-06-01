@extends('layouts.front_partner.partner_login')

@section('content')
<section id="hire-page-banner">
    <div class="overlap-black-shadow"></div>
    <div class="text-caption">
        <div class="container">
            <div class="admin-form theme-info" id="login1">
                <div class="panel panel-info sign-in">
                    <div class="panel-heading clearfix p10 ph15">
                        <span class="panel-title">
                            <i class="fa fa-sign-in"></i> SignIn </span>
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
                            <form method="POST" action="{{ url('/partner/validate_login') }}">
                                {!! csrf_field() !!}
                                <div class="panel-body bg-light">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="section row form-group">
                                                <label for="email" class="col-xs-4 col-sm-4 control-label">E-Mail</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="email" name="login_email" id="login_email" class="gui-input form-control" placeholder="Enter your email..." value="{{ old('login_email') }}" required="required">
                                                    @if ($errors->has('login_email'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('login_email') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="section row form-group">
                                                <label for="password" class="col-xs-4 col-sm-4 control-label">Password</label>
                                                <div class="col-xs-8 col-sm-6">
                                                    <input type="password" name="login_password" id="login_password" class="gui-input form-control" placeholder="Enter your password..." required="required">
                                                    @if ($errors->has('login_password'))
                                                    <span class="help-block err">
                                                        <strong>{{ $errors->first('login_password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>                                         
                                            </div>
                                            <div class="section row form-group forget">
                                                <div class="col-xs-4 col-sm-4"></div>
                                                <div class="col-xs-8 col-sm-6">
                                                    <label class="switch ib switch-primary input-align mt10 remeber">
                                                        <input type="checkbox" name="remember" id="remember" checked value="1">
                                                        <span>Remember me</span>
                                                        <a class="btn btn-link" href="{{ url('/partner/forgotPassword') }}">Forgot Password?</a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer clearfix">
                                    <button type="submit" class="button btn-primary mr10 pull-right">Sign In</button>
                                    <a class="btn btn-link pull-left" href="{{ url('/partner/signup') }}">Create an account</a>
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