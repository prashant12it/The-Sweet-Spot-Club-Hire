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
            <li class="crumb-trail">Add Region</li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <span class="panel-title">Add New Region</span>
        </div>
        <div class="panel-body">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            <form method="POST" action="{{ url('/regions') }}" class="form-horizontal" role="form" id="addRegionForm">

                <div class="panel-body p25 bg-light">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <div class="section row form-group">
                        <label for="stateid" class="col-lg-4 control-label">State</label>
                        <div class="col-lg-6">
                            <select name="stateid" id="stateid" class="form-control">
                                <option value="">Select state</option>
                                @foreach ($StatesArr as $state)

                                <option value="{{$state->id}}" {{ (old("stateid") == $state->id ? "selected":"") }}>{{$state->name}}</option>
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
                        <label for="region" class="col-lg-4 control-label">Region</label>
                        <div class="col-lg-6">
                            <input type="text" name="region" id="region" value="{{ old('region') }}" class="gui-input form-control" placeholder="Enter region..." required="required">
                            @if ($errors->has('region'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('region') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!-- end section -->
                    </div>
                    <!-- end .section row section -->
                </div>


        </div>
        <!-- end .form-body section -->
        <div class="panel-footer clearfix">
            <div class="col-lg-8"></div>
            <div class="col-lg-2">
                <button type="submit" class="button form-control btn-primary pull-right">Add region</button>
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
@endsection