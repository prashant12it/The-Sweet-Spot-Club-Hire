@extends('layouts.front_partner.partner_post_login')

@section('content')

<section id="content">
    <div class="panel">
        <div class="panel mb25 mt5">
            <div class="panel-body">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            <div class="table-responsive"> 
                <table class="table">
                    <tr>
                        <th width="30%">Banner</th>
                        <th width="40%">Banner Code</th>
                        <th width="15%">Clicks</th>
                        <th width="15%">Actions</th>
                    </tr>
                    @if ($bannersAry->count() > 0)
                        @foreach ($bannersAry as $bannerDetails)
                            <tr>
                                <td width="30%">
                                    @if ($bannerDetails->banner_type == 0)
                                    <a href="javascript:void(0)"  onclick="viewOrignalBanner('{{$bannerDetails->file_name}}','{{$bannerDetails->width}}','{{$bannerDetails->height}}')">
                                        <img src="{{$getSiteUrl}}/public/banners_img/{{$bannerDetails->file_name}}" width="150" />
                                    </a>
                                    <br/>
                                    {{$bannerDetails->height}}X{{$bannerDetails->width}}px
                                        @else
                                        <h5>{{$bannerDetails->title}}</h5>
                                        @endif
                                </td>
                                <td width="40%">
<pre><textarea  id="banner_{{$bannerDetails->id}}" rows="5" cols="130" readonly="readonly">
 <div style="">
    <a href="{{$getSiteUrl.'/partner_pro/'.$partner_ref.'/'.$bannerDetails->banner_reference_id}}" target="_blank" title="{{$bannerDetails->title}}">
        @if ($bannerDetails->banner_type == 0)
        <img src="{{$getSiteUrl.'/public/banners_img/'.$bannerDetails->file_name}}" alt="The Sweet Spot Club Hire" width="{{$bannerDetails->width}}" height="{{$bannerDetails->height}}">
            @else
            {{$bannerDetails->title}}
            @endif
    </a>
 </div></textarea>
</pre>
<span id="banner_copied_{{$bannerDetails->id}}" ></span>
                                </td>
                                <td width="15%">
                                    {{$bannerDetails->clicks_count}}
                                </td>
                                <td width="15%">
                                    <div class="navbar-btn btn-group">
                                        <a href="javascript:void(0)" class="button form-control btn-info" title="View Banner" onclick="viewOrignalBanner('{{$bannerDetails->file_name}}','{{$bannerDetails->width}}','{{$bannerDetails->height}}','{{$bannerDetails->title}}')">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                    </div>
                                    <div class="navbar-btn btn-group">
                                        <a href="javascript:void(0)" class="button form-control btn-info" title="Copy Code" onclick="copyBannerCode('{{$bannerDetails->id}}')">
                                            <span class="fa fa-copy"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">No Banner found.</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="panel-footer clearfix">
            {!! $bannersAry->render() !!}
        </div>
        </div>
        
    </div>
    <div id="popupbs" class="img-view">
        <div class="modal fade" id="viewOrignalBanner" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Original Banner View</h4>
                        </div>
                        <div class="modal-body">
                            <img id="banner_view" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection