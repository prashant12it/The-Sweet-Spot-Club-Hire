@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
           <ol class="breadcrumb">
                <li class="crumb-active">
                     <a href="{{url('/partners')}}"><span class="fa fa-users"></span> Partners</a>
                </li>
                <li class="crumb-active">Edit Partner Details</li>
            </ol>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <span class="panel-title">Edit Partner</span>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ url('/edit_partner') }}" class="form-horizontal" role="form" id="editPartnerForm">
            <div class="panel-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif

                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Name</label>
                        <div class="col-lg-6">
                            <input type="hidden" name="partnerId" id="partnerId" value="{{$partner->id}}" />
                            <input type="text" name="name" id="name" value="{{ old('name',$partner->name) }}" class="gui-input form-control" required="required" placeholder="Please type partner name...">
                            @if ($errors->has('name'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Email</label>
                        <div class="col-lg-6">
                            <input type="email" name="email" id="email" value="{{ old('email',$partner->email) }}" class="gui-input form-control" required="required" placeholder="Please type partner email...">
                            @if ($errors->has('email'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Password</label>
                        <div class="col-lg-6">
                            <input type="text" name="password" id="password" value="{{ old('password') }}" class="gui-input form-control" placeholder="Please type partner new password...">
                            @if ($errors->has('password'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">ZIP Code</label>
                        <div class="col-lg-6">
                            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode',$partner->zipcode) }}" class="gui-input form-control" required="required" placeholder="Please type zip/postal code...">
                            @if ($errors->has('zipcode'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('zipcode') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Address</label>
                        <div class="col-lg-6">
                            <input type="text" name="address" id="address" value="{{ old('address',$partner->address) }}" class="gui-input form-control" required="required" placeholder="Please type partner address...">
                            @if ($errors->has('address'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">State</label>
                        <div class="col-lg-6">
                            <input type="text" name="state" id="state" value="{{ old('state',$partner->state) }}" class="gui-input form-control" required="required" placeholder="Please type partner state...">
                            @if ($errors->has('state'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('state') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Country</label>
                        <div class="col-lg-6">
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
                        <label for="inputStandard" class="col-lg-4 control-label">Contact</label>
                        <div class="col-lg-6">
                            <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no',$partner->contact_no) }}" class="gui-input form-control" placeholder="Please type contact number...">
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
                <div class="col-lg-8"></div>
                <div class="col-lg-2 pull-right">
                    <button type="submit" class="button form-control btn-primary pull-right">Save</button>
                </div>
                <div class="col-lg-2"></div>
            </div>
        </form>
    </div>

</div>
</section>

@endsection