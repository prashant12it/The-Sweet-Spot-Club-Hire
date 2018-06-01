@extends('layouts.front_partner.partner_login')

@section('content')
<section id="hire-page-banner">
    <div class="overlap-black-shadow"></div>
    <div class="text-caption">
        <div class="container">
            <div class="admin-form theme-info" id="login1">
                <div class="panel panel-info sign-in">
                    <div class="panel-heading clearfix p10 ph15">
                       <i class="fa fa-cogs" aria-hidden="true"></i> Partner Reset Password
                    </div>
                    <form method="POST" action="{{ url('/partner/saveNewPassword') }}">
                        {!! csrf_field() !!}
                        <div class="panel-body bg-light p30">
                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}</p>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="section row form-group">
										<div class="col-xs-4 co-sm-4">
											<label for="password" class="field-label text-muted fs18 mb10">New Password</label>
										</div>
                                        <div for="password" class="field col-xs-8 col-sm-6 prepend-icon">
                                            <input type="password" name="password" id="password" class="gui-input form-control" placeholder="Enter New Password" value="{{ old('password') }}" required="required">
                                        </div>
                                        @if ($errors->has('password'))
                                        <span class="help-block err">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="section row form-group">
										<div class="col-xs-4 co-sm-4">
											<label for="confirm_password" class="field-label text-muted fs18 mb10">Confirm Password</label>
										</div>
                                        <div for="confirm_password" class="field prepend-icon col-xs-8 col-sm-6">
                                            <input type="hidden" name="reset_key" id="reset_key" value="{{ $reset_key }}">
                                            <input type="password" name="confirm_password" id="confirm_password" class="gui-input form-control" placeholder="Enter Confirm Password" value="{{ old('confirm_password') }}" required="required">
                                        </div>
                                        @if ($errors->has('confirm_password'))
                                        <span class="help-block err">
                                            <strong>{{ $errors->first('confirm_password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer clearfix p10 ph15">
                            <button type="submit" class="button btn-primary mr10 pull-right">Save</button>
                            <a class="btn btn-link pull-left" href="{{ url('/partner/login') }}">Already have password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection