@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/banners')}}"><span class="fa fa-picture-o"></span> Banners</a>
            </li>
            <li class="crumb-trail">Banner Details</li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <div class="panel mb25 mt5">
            <div class="panel-heading">
                <span class="panel-title hidden-xs">
                    <strong> Title:</strong> {{$bannerDetailsData->title}} 
                </span>
                <ul class="nav panel-tabs-border panel-tabs">
                    <li class="active">
                        <a href="#banner_tab" data-toggle="tab" aria-expanded="false">Banner Details</a>
                    </li>
                    <li class="">
                        <a href="#partners_tab" data-toggle="tab" aria-expanded="true">Partners</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body p25 pb5">
                <div class="tab-content pn br-n admin-form">
                    
                    <!--START BANNER DETAILS DIV-->
                    <div id="banner_tab" class="tab-pane active">
                        <div class="panel">
                            <div class="panel-body pn table-responsive">
                                <table class="table order-details-table table-scroll">
                                    <thead>
                                        <tr class="hidden">
                                            <th width="100%">Banner</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                @if ($bannerDetailsData->banner_type == 0)
                                                <img src="{{$baseUrl}}/public/banners_img/{{$bannerDetailsData->file_name}}" width="{{$bannerDetailsData->width}}"/><br/>
                                                @else
                                                    <h4>{{$bannerDetailsData->title}}</h4>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($bannerDetailsData->banner_type == 0)
                                                <strong>Width(px) : </strong>{{$bannerDetailsData->width}}<br/>
                                                <strong>Height(px) : </strong>{{$bannerDetailsData->height}}<br/>
                                                @endif
                                                <strong>Status : </strong>{{($bannerDetailsData->iActive == 1?'Active':'Inactive')}}<br/>
                                                <strong>Number of clicks : </strong>{{$bannerDetailsData->clicks_count}}<br/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--END BANNER DEATILS DIV-->
                    
                    <!--START PARTNER DETAILS DIV-->
                    <div id="partners_tab" class="tab-pane">
                        <div class="panel">
                            <div class="panel-body pn">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="20%">Partner Name</th>
                                            <th width="25%">Partner Email</th>
                                            <th width="20%">Total Clicks</th>
                                            <th width="20%">Total Sales($)</th>
                                            <th width="15%">Last Clicked</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($bannerPartnerAry) > 0)
                                            @foreach ($bannerPartnerAry as $bannerPartner)
                                                <tr>
                                                    <td width="20%">{{$bannerPartner->name}}</td>
                                                    <td width="25%">{{$bannerPartner->email}}</td>
                                                    <td width="20%">{{$bannerPartner->clickCount}}</td>
                                                    <td width="20%">{{number_format($bannerPartner->total_sale,2)}}</td>
                                                    <td width="15%">{{date('M d Y',strtotime($bannerPartner->last_clicked))}}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                        <tr>
                                            <td colspan="5">
                                                No Partner linked with this banner.
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            
                    </div>
                    <!--END PARTNER DETAILS DIV-->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
