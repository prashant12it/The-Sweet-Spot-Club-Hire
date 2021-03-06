@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
           <ol class="breadcrumb">
                <li class="crumb-active">
                     <a href="{{url('/banners')}}"><span class="fa fa-picture-o"></span> Banners</a>
                </li>
                <li class="crumb-active">Add Banner</li>
            </ol>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <span class="panel-title">Add New Banner</span>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ url('/add_banner') }}" class="form-horizontal" role="form" id="addBannerForm">
            <div class="panel-body">
                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Title</label>
                        <div class="col-lg-6">
                            <input type="text" name="title" id="title" value="{{ old('title') }}" class="gui-input form-control" required="required" placeholder="Please type banner title...">
                            @if ($errors->has('title'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Width</label>
                        <div class="col-lg-6">
                            <input type="number" name="width" id="width" value="{{ old('width') }}" class="gui-input form-control" required="required" placeholder="Please type banner width...">
                            @if ($errors->has('width'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('width') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Height</label>
                        <div class="col-lg-6">
                            <input type="number" name="height" id="height" value="{{ old('height') }}" class="gui-input form-control" required="required" placeholder="Please type banner height...">
                            @if ($errors->has('height'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('height') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Upload Banner</label>
                        <div class="col-lg-6">
                            <input type="file" name="file_name" id="file_name" value="{{ old('file_name') }}" class="btn btn-dark" required="required">
                            @if ($errors->has('file_name'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('file_name') }}</strong>
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