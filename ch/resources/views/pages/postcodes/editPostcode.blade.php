@extends('layouts.dashboard')

@section('content')
    <header id="topbar" class="alt">
        <div class="topbar-left">
            <ol class="breadcrumb">
                <li class="crumb-active">
                    <a href="{{url('/dashboard')}}">Dashboard</a>
                </li>
                <li class="crumb-icon">
                    <a href="{{url('/dashboard')}}">
                        <span class="glyphicon glyphicon-home"></span>
                    </a>
                </li>
                <li class="crumb-link">
                    <a href="{{url('/dashboard')}}">Home</a>
                </li>
                <li class="crumb-trail">Edit Postcode</li>
            </ol>
        </div>
    </header>
    <section id="content">
        <div class="panel">

            <div class="panel-heading">
                <span class="panel-title">Edit Postcode</span>
            </div>
            <div class="panel-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <form method="POST" action="{{ url('/update-postcode') }}" class="form-horizontal" role="form" id="addPostcodeForm">

                    <div class="panel-body p25 bg-light">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="section row form-group">
                            <label for="stateid" class="col-lg-4 control-label">State</label>
                            <div class="col-lg-6">
                                <input type="hidden" name="postcodeid" id="postcodeid" value="{{$shippinginfo->id}}" />
                                <select name="stateid" id="stateid" class="form-control" onchange="getRegions('#stateid');" required>
                                    <option value="">Select state</option>
                                    @foreach ($StatesArr as $state)

                                        <option value="{{$state->id}}" {{ (old("stateid") == $state->id ? "selected":($state->id == $stateid[0]->stateid ?"selected":"")) }}>{{$state->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('stateid'))
                                    <span class="help-block err">
                                <strong>{{ $errors->first('stateid') }}</strong>
                            </span>
                                @endif
                            </div>

                            <!-- end section -->
                        </div>
                        <div class="section row form-group">
                            <label for="stateid" class="col-lg-4 control-label">Region</label>
                            <div class="col-lg-6">
                                <select name="region_id" id="region_id" class="form-control" required>
                                    <option value="">Select region</option>
                                    @if(!empty($regions))
                                        @foreach ($regions as $reg)

                                            <option value="{{$reg->id}}" {{ (old("region_id") == $reg->id ? "selected=selected":($reg->id == $selectedRegion ?"selected=selected":"")) }}>{{$reg->region}}</option>
                                        @endforeach
                                        @endif
                                </select>
                                @if ($errors->has('region_id'))
                                    <span class="help-block err">
                                <strong>{{ $errors->first('region_id') }}</strong>
                            </span>
                                @endif
                            </div>

                            <!-- end section -->
                        </div>
                        <div class="section row form-group">
                            <label for="postcode" class="col-lg-4 control-label">Postcode</label>
                            <div class="col-lg-6">
                                <input type="text" name="postcode" id="postcode" value="{{ (old("postcode")?old("postcode"):$shippinginfo->postcode)}}" class="gui-input form-control" placeholder="Enter postcode..." required="required">
                                @if ($errors->has('postcode'))
                                    <span class="help-block err">
                                <strong>{{ $errors->first('postcode') }}</strong>
                            </span>
                                @endif
                            </div>
                            <!-- end section -->
                        </div>
                        <div class="section row form-group">
                            <label for="shipping_cost" class="col-lg-4 control-label">Shipping cost</label>
                            <div class="col-lg-6">
                                <input type="text" name="shipping_cost" id="shipping_cost" value="{{ (old("shipping_cost")?old("shipping_cost"):$shippinginfo->shipping_cost)}}" class="gui-input form-control" placeholder="Enter shipping cost..." required="required">
                                @if ($errors->has('shipping_cost'))
                                    <span class="help-block err">
                                <strong>{{ $errors->first('shipping_cost') }}</strong>
                            </span>
                                @endif
                            </div>
                            <!-- end section -->
                        </div>
                        <div class="section row form-group">
                            <label for="suburb" class="col-lg-4 control-label">Suburb</label>
                            <div class="col-lg-6">
                                <input type="text" name="suburb" id="suburb" value="{{ (old("suburb")?old("suburb"):$shippinginfo->suburb)}}" class="gui-input form-control" placeholder="Enter suburb..." required="required">
                                @if ($errors->has('suburb'))
                                    <span class="help-block err">
                                <strong>{{ $errors->first('suburb') }}</strong>
                            </span>
                                @endif
                            </div>
                            <!-- end section -->
                        </div>
                        <div class="section row form-group">
                            <label for="comments" class="col-lg-4 control-label">Comments</label>
                            <div class="col-lg-6">
                                <input type="text" name="comments" id="comments" value="{{ (old("comments")?old("comments"):$shippinginfo->comments)}}" class="gui-input form-control" placeholder="Comments..." />
                                @if ($errors->has('comments'))
                                    <span class="help-block err">
                                <strong>{{ $errors->first('comments') }}</strong>
                            </span>
                                @endif
                            </div>
                            <!-- end section -->
                        </div>
                        <!-- end .section row section -->
                    </div>

            <!-- end .form-body section -->
            <div class="panel-footer clearfix">
                <div class="col-lg-7"></div>
                <div class="col-lg-3">
                    <button type="submit" class="button form-control btn-primary pull-right">Update postcode</button>
                </div>
                <div class="col-lg-2"></div>
            </div>
            <!-- end .form-footer section -->
            </form>
        </div>

        </div>

        <div class="panel-footer clearfix">

        </div>
        </form>
        </div>

        </div>
    </section>
    <script>
        $(document).ready(function () {
            getRegions('#stateid','{{$shippinginfo->region_id}}');
        });
    </script>
@endsection