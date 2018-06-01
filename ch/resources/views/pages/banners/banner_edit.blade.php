@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
           <ol class="breadcrumb">
                <li class="crumb-active">
                     <a href="{{url('/banners')}}"><span class="fa fa-picture-o"></span> Banners</a>
                </li>
                <li class="crumb-active">Edit Banner</li>
            </ol>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <span class="panel-title">Edit Banner Details</span>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ url('/edit_banner') }}" class="form-horizontal" role="form" id="editBannerForm">
            <div class="panel-body">
                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Title</label>
                        <div class="col-lg-6">
                            <input type="hidden" name="bannerId" id="bannerId" value="{{ $banner->id }}" class="gui-input form-control" required="required" placeholder="Please type banner title...">
                            <input type="text" name="title" id="title" value="{{ old('title',$banner->title) }}" class="gui-input form-control" required="required" placeholder="Please type banner title...">
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
                            <input type="number" name="width" id="width" value="{{ old('width',$banner->width) }}" class="gui-input form-control" required="required" placeholder="Please type banner width...">
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
                            <input type="number" name="height" id="height" value="{{ old('height',$banner->height) }}" class="gui-input form-control" required="required" placeholder="Please type banner height...">
                            @if ($errors->has('height'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('height') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Uploaded Banner</label>
                        <div class="col-lg-6">
                            @if(old('file_name') == '' && $errors->has('file_name') == '')
                                <p id="old_banner_image">
                                    <img src="{{$baseUrl}}/public/banners_img/{{$banner->file_name}}" width="150" />&nbsp;
                                    <a href="javascript:void(0);" onclick="removeBannerImage()">
                                        <i class="fa fa-times-circle-o" style="color:red;font-size: 22px;"></i>
                                    </a>
                                </p>
                            @endif
                            
                            <input type="hidden" name="old_file_name" id="old_file_name" value="{{ $banner->file_name }}">
                            <input type="file" name="file_name" id="file_name" value="{{ old('file_name') }}" class="btn btn-dark" style="display:{{(old('file_name') || $errors->has('file_name')!=''?'block':'none')}}">

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