@extends('layouts.dashboard')

@section('content')

    <header id="topbar" class="alt">
        <div class="topbar-left">
            <ol class="breadcrumb">
                <li class="crumb-active">
                    <a href="{{url('/banners')}}"><span class="fa fa-picture-o"></span> Banners</a>
                </li>
            </ol>
        </div>
    </header>
    <section id="content">
        <div class="panel">
            <form method="POST" enctype="multipart/form-data" action="{{ url('/search_banner') }}"
                  class="form-horizontal" role="form" id="account2">
                {{ csrf_field() }}
                <div class="panel-menu admin-form theme-primary">
                    <div class="row">
                        <?php
                        if (session('searchBanner')) {
                            $searchBanner = session('searchBanner');
                        } else {
                            $searchBanner = array();
                            $searchBanner['title'] = '';
                            $searchBanner['iActive'] = '';
                        }
                        ?>
                        <div class="col-md-6">
                            <label for="filter-title" class="field">
                                <input value="{{$searchBanner['title']}}" id="partner_name" name="searchAry[title]"
                                       class="gui-input" placeholder="Banner title.." type="text">
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="field select">

                                <select id="offer_type" name="searchAry[iActive]">
                                    <option value="">Banner Status</option>
                                    <option value="1" {{($searchBanner['iActive'] === '1' ? "selected='selected'":"")}}>
                                        Active
                                    </option>
                                    <option value="0" {{($searchBanner['iActive'] === '0' ? "selected='selected'":"")}}>
                                        Inactive
                                    </option>
                                </select>
                                <i class="arrow double"></i>
                            </label>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="button form-control btn-info pull-right"><i
                                        class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/banners') }}" class="button form-control btn-danger pull-right"><i
                                        class="fa fa-refresh" aria-hidden="true"></i> </a>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/add_banner') }}" class="button form-control btn-success pull-right"
                               title="Add New Image Banner"><i class="fa fa-plus" aria-hidden="true"></i> <i
                                        class="fa fa-file-image-o"></i> </a>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/add_text_banner') }}" class="button form-control btn-success pull-right"
                               title="Add New Text Banner"><i class="fa fa-plus" aria-hidden="true"></i> <i
                                        class="fa fa-file-text"></i> </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="panel-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th width="20%">Title</th>
                            <th width="25%">Banner</th>
                            <th width="10%">Width(px)</th>
                            <th width="10%">Height(px)</th>
                            <th width="10%">Clicks</th>
                            <th width="10%">Status</th>
                            <th width="15%">Actions</th>
                        </tr>
                        @if ($bannersAry->count() > 0)
                            @foreach ($bannersAry as $bannerDetails)
                                <tr id="banner-rec-id-{{$bannerDetails->id}}">
                                    <td>{{$bannerDetails->title}}</td>
                                    <td>
                                        @if ($bannerDetails->banner_type == 0)
                                        <img src="{{$baseUrl}}/public/banners_img/{{$bannerDetails->file_name}}"
                                             width="150"/>
                                            @else
                                        <h4>{{$bannerDetails->title}}</h4>
                                            @endif
                </td>
                <td>{{($bannerDetails->banner_type == 0?$bannerDetails->width:'--')}}</td>
                <td>{{($bannerDetails->banner_type == 0?$bannerDetails->height:'--')}}</td>
                <td>{{$bannerDetails->clicks_count}}</td>
                <td>
                    <div class="btn-group text-right">
                        <?php
                        if ($bannerDetails->iActive == 1) {
                            $statusName = "Active";
                            $btnClass = "btn-success";
                        } else {
                            $statusName = "Inactive";
                            $btnClass = "btn-danger";
                        }
                        ?>
                        <button type="button" class="btn {{$btnClass}} br2 btn-xs fs12 dropdown-toggle"
                                data-toggle="dropdown" aria-expanded="false"> {{$statusName}}
                            <span class="caret ml5"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                @if ($bannerDetails->iActive == 1)
                                    <a href="javascript:void(0)"
                                       onclick="updateBannerStatus('{{$bannerDetails->id}}','0','Inactivate')">Inactive</a>
                                @else
                                    <a href="javascript:void(0)"
                                       onclick="updateBannerStatus('{{$bannerDetails->id}}','1','Activate')">Active</a>
                                @endif
                            </li>

                        </ul>
                    </div>
                </td>
                <td>
                    <div class="navbar-btn btn-group">
                        <a href="{{ url('/view_banner') }}/{{$bannerDetails->id}}" class="button form-control btn-info"
                           title="View Banner Details">
                            <span class="fa fa-eye"></span>
                        </a>
                    </div>
                    <div class="navbar-btn btn-group">
                        <a href="{{($bannerDetails->banner_type == 0?url('/edit_banner'):url('/edit_text_banner')) }}/{{$bannerDetails->id}}" class="button form-control btn-info"
                           title="Edit Banner">
                            <span class="fa fa-pencil-square"></span>
                        </a>
                    </div>
                    <div class="navbar-btn btn-group">
                        <a href="javascript:void(0)" class="button form-control btn-danger"
                           onclick="DeleteBanner('{{$bannerDetails->id}}');" title="Delete Banner">
                            <span class="fa fa-trash-o"></span>
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
        <div id="popupbs">
            <div class="modal fade" id="bannerStatusModal" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <form method="POST" action="{{ url('/updateBannerStatus') }}" class="form-horizontal"
                              role="form">
                            {{ csrf_field() }}
                            <input type="hidden" name="idBanner" value="" id="idBanner"/>
                            <input type="hidden" name="iActive" value="" id="iActive"/>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Update Banner Status</h4>
                            </div>
                            <div class="modal-body">
                                <p id="message_banner"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Yes</button>
                                <button type="button" class="btn btn-info" data-dismiss="modal">No</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="bannerDeleteModal" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <form method="POST" action="{{ url('/deleteBanner') }}" class="form-horizontal" role="form">
                            {{ csrf_field() }}
                            <input type="hidden" name="bannerid" value="" id="bannerid"/>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Delete Banner</h4>
                            </div>
                            <div class="modal-body">
                                <p id="message_banner1"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="removeBanner();" data-dismiss="modal"
                                        class="btn btn-danger">Yes
                                </button>
                                <button type="button" class="btn btn-info" data-dismiss="modal">No</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="bannerDeleteStatus" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Delete Banner Info</h4>
                        </div>
                        <div class="modal-body">
                            <p id="message_banner2"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info" data-dismiss="modal">Okay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection