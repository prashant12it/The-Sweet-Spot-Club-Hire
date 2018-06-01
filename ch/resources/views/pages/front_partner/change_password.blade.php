@extends('layouts.front_partner.partner_post_login')

@section('content')
    <section id="content">
        <div class="panel">
			<h2 class="heading text-center">Change Password</h2>
            <form method="POST" enctype="multipart/form-data" action="{{ url('/partner/update_password') }}" class="form-horizontal form-style" role="form" id="changePasswordForm">
                <div class="panel-body">
                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    {{ csrf_field() }}
                    <div class="panel-body p25 bg-light">
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">Old Password</label>
                            <div class="col-sm-6">
                                <input type="password" name="old_password" id="old_password" value="" class="gui-input form-control" required="required" placeholder="Enter your old password...">
                                @if ($errors->has('old_password'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('old_password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">New Password</label>
                            <div class="col-sm-6">
                                <input type="password" name="password" id="password" value="" class="gui-input form-control" required="required" placeholder="Enter your new password...">
                                @if ($errors->has('password'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">Confirm New Password</label>
                            <div class="col-sm-6">
                                <input type="password" name="confirm_password" id="confirm_password" value="" class="gui-input form-control" required="required" placeholder="Enter confirm password...">
                                @if ($errors->has('confirm_password'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('confirm_password') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer clearfix">
                        <button type="submit" class="button form-control">Save</button>
                </div>
            </form>
        </div>

    </div>
    </section>
@endsection