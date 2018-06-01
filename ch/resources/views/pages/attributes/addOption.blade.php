@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
           <ol class="breadcrumb">
                <li class="crumb-active">
                     <a href="{{url('/pro_attributes')}}"><span class="glyphicon glyphicon-list"></span> Attributes</a>
                </li>
                <li class="crumb-active">
                    <a href="/attribute_options/{{$idAttribute}}">Attributes Options</a>
                </li>
                <li class="crumb-active">Add Option</li>
            </ol>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <span class="panel-title">Add New Option</span>
        </div>
        <form method="POST" enctype="multipart/form-data" action="/option_add/{{$idAttribute}}" class="form-horizontal" role="form" id="addOptionForm">
            <div class="panel-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif

                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Option Name</label>
                        <div class="col-lg-6">
                            <input type="text" name="value" id="value" value="{{ old('value') }}" class="gui-input form-control" required="required" placeholder="Please type attribute option...">
                            <input type="hidden" name="attrib_id" id="attrib_id" value="{{$idAttribute}}" >
                            @if ($errors->has('value'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('value') }}</strong>
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