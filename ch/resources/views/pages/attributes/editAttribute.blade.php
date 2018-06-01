@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/pro_attributes')}}"><span class="glyphicon glyphicon-list"></span> Attributes</a>
           </li>
            <li class="crumb-active">Edit Attribute</li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Edit Attribute</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{url('/pro_attributes')}}"> Back</a>
                </div>
            </div>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ url('/update_attribute') }}" class="form-horizontal" role="form" id="updateAttributeForm">
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
                            <input type="text" name="attrib_name" id="attrib_name" value="{{$attributesData->attrib_name}}" class="gui-input form-control" required="required" placeholder="Please type attribute name...">
                            <input type="hidden" name="id" value="{{$attributesData->id}}" />
                            @if ($errors->has('attrib_name'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('attrib_name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- end .form-body section -->
            <div class="panel-footer clearfix">
                <div class="col-lg-8"></div>
                <div class="col-lg-2 pull-right">
                    <button type="submit" class="button form-control btn-primary pull-right">Update product</button>
                </div>
                <div class="col-lg-2"></div>
            </div>
            <!-- end .form-footer section -->
        </form>
    </div>

</div>
</section>

@endsection