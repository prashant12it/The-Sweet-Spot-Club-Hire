@extends('layouts.front_partner.partner_login')

@section('content')
<section id="hire-page-banner">
    <div class="overlap-black-shadow"></div>
    <div class="text-caption">
        <div class="container">
            <div class="admin-form theme-info" id="login1">
                <div class="panel panel-info sign-in">
                    <div class="panel-heading clearfix p10 ph15">
                        <i class="fa fa-key" aria-hidden="true"></i> Partner Forgot Password
                    </div>
                    <form method="POST" action="{{ url('/partner/send_forgot_password') }}">
                        {!! csrf_field() !!}
                        <div class="panel-body bg-light p30">
                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="section row forget-key form-group">
										<div class="col-xs-4 col-sm-4">
											<label for="email" class="field-label text-muted fs18 mb10">E-Mail Address</label>
										</div>
										<div class="col-xs-8 col-sm-6">
                                        <label for="email" class="field prepend-icon ">
                                            <input type="email" name="email" id="email" class="gui-input form-control" placeholder="Enter email id..." value="{{ old('email') }}" required="required">
                                        </label>
                                        @if ($errors->has('email'))
                                        <span class="help-block err">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
										</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer clearfix p10 ph15">
                            <button type="submit" class="button btn-primary mr10 pull-right">Send</button>
                            <a class="btn btn-link pull-left" href="{{ url('/partner/login') }}">Already have password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection