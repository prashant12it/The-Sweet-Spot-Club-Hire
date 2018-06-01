@extends('layouts.front_partner.partner_post_login')

@section('content')
    <section id="content">
        <div class="panel">
		<h2 class="heading text-center">Edit Profile</h2>
            <form method="POST" enctype="multipart/form-data" action="{{ url('/partner/updateProfile') }}" class="form-horizontal form-style" role="form" id="editPartnerForm">
                <div class="panel-body">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                    @endif

                    {{ csrf_field() }}
                    <div class="panel-body p25 bg-light">
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">Name</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" id="name" value="{{ old('name',$partner->name) }}" class="gui-input form-control" required="required" placeholder="Enter your name...">
                                @if ($errors->has('name'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">Email</label>
                            <div class="col-sm-6">
                                <input type="email" name="email" id="email" value="{{ old('email',$partner->email) }}" class="gui-input form-control" required="required" placeholder="Enter your email...">
                                @if ($errors->has('email'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">ZIP Code</label>
                            <div class="col-sm-6">
                                <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode',$partner->zipcode) }}" class="gui-input form-control" required="required" placeholder="Enter your zip/postal code...">
                                @if ($errors->has('zipcode'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('zipcode') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">Address</label>
                            <div class="col-sm-6">
                                <input type="text" name="address" id="address" value="{{ old('address',$partner->address) }}" class="gui-input form-control" required="required" placeholder="Enter your address...">
                                @if ($errors->has('address'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">State</label>
                            <div class="col-sm-6">
                                <input type="text" name="state" id="state" value="{{ old('state',$partner->state) }}" class="gui-input form-control" required="required" placeholder="Enter your state...">
                                @if ($errors->has('state'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('state') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">Country</label>
                            <div class="col-sm-6">
                                <select id="country" name="country" class="gui-input form-control" required="required">
                                    <option value="0">Select Country</option>
                                    @if ($countriesAry->count() > 0)
                                        @foreach ($countriesAry as $country)
                                            <option value="{{$country->id}}" {{(old('country',$partner->country) == $country->id ? "selected=selected":"")}}>{{$country->name}}</option>
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
                        <div class="section row form-group">
                            <label for="inputStandard" class="col-sm-4 control-label">Contact</label>
                            <div class="col-sm-6">
                                <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no',$partner->contact_no) }}" class="gui-input form-control" placeholder="Enter your contact number...">
                                @if ($errors->has('contact_no'))
                                <span class="help-block err">
                                    <strong>{{ $errors->first('contact_no') }}</strong>
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