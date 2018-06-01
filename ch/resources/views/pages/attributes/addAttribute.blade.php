@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
           <ol class="breadcrumb">
                <li class="crumb-active">
                     <a href="{{url('/pro_attributes')}}"><span class="glyphicon glyphicon-list"></span> Attributes</a>
                </li>
                <li class="crumb-active">Add Attribute</li>
            </ol>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <span class="panel-title">Add New Attribute</span>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ url('/add_attribute') }}" class="form-horizontal" role="form" id="addAttributeForm">
            <div class="panel-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif

                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Attribute Name</label>
                        <div class="col-lg-6">
                            <input type="text" name="attrib_name" id="attrib_name" value="{{ old('attrib_name') }}" class="gui-input form-control" required="required" placeholder="Please type attribute name...">
                            @if ($errors->has('attrib_name'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('attrib_name') }}</strong>
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