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
            <span class="panel-title">Edit Text Banner Details</span>
        </div>
        <form method="POST" enctype="multipart/form-data" action="{{ url('/edit_text_banner') }}" class="form-horizontal" role="form" id="editBannerForm">
            <div class="panel-body">
                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">Banner Link</label>
                        <div class="col-lg-6">
                            <input type="hidden" name="bannerId" id="bannerId" value="{{ $banner->id }}" class="gui-input form-control" required="required" placeholder="Please type banner title...">
                            <input type="text" name="title" id="title" value="{{ old('title',$banner->title) }}" class="gui-input form-control" required="required" placeholder="Please enter banner title...">
                            @if ($errors->has('title'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    {{--<div class="section row form-group">
                        <label for="inputStandard" class="col-lg-4 control-label">URL</label>
                        <div class="col-lg-6">
                            <input type="text" name="url_val" id="url_val" value="{{ old('url_val',$banner->url_val) }}" class="gui-input form-control" required="required" placeholder="Please type valid url...">
                            @if ($errors->has('url_val'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('url_val') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>--}}
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