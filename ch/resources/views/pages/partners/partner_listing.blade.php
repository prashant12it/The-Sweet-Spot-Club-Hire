@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/partners')}}"><span class="fa fa-users"></span> Partners</a>
            </li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <form method="POST" enctype="multipart/form-data" action="{{ url('/search_partner') }}" class="form-horizontal" role="form" id="account2">
                {{ csrf_field() }}
                <div class="panel-menu admin-form theme-primary">
                    <div class="row">
                        <?php
                            if(session('searchPartner')){
                                $searchPartner = session('searchPartner');
                            }
                            else{
                                $searchPartner = array();
                                $searchPartner['name'] = '';
                                $searchPartner['iActive'] = '';
                            }
                        ?>
                        <div class="col-md-7">
                            <label for="filter-name" class="field">
                                <input value="{{$searchPartner['name']}}" id="partner_name" name="searchAry[name]" class="gui-input" placeholder="Partner Name/Email/State...." type="text">
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="field select">

                                <select id="offer_type" name="searchAry[iActive]">
                                    <option value="" >Account Status</option>
                                    <option value="1" {{($searchPartner['iActive'] === '1' ? "selected='selected'":"")}}>Active Accounts</option>
                                    <option value="0" {{($searchPartner['iActive'] === '0' ? "selected='selected'":"")}}>Inactive Accounts</option>
                                </select>
                                <i class="arrow double"></i>
                            </label>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="button form-control btn-info pull-right"><i class="fa fa-search" aria-hidden="true"></i> </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/partners') }}" class="button form-control btn-danger pull-right"><i class="fa fa-refresh" aria-hidden="true"></i> </a>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/add_partner') }}" class="button form-control btn-success pull-right" title="Add New Partner"><i class="fa fa-plus" aria-hidden="true"></i> </a>
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
                        <th width="15%">Name</th>
                        <th width="25%">Email</th>
                        <th width="10%">Clicks</th>
                        <th width="10%">Last Clicked</th>
                        <th width="10%">Orders</th>
                        <th width="10%">Commission($)</th>
                        <th width="10%">Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                    @if ($partnersAry->count() > 0)
                        @foreach ($partnersAry as $partnerDetails)
                            <tr>
                                <td>{{$partnerDetails->name}}</td>
                                <td>{{$partnerDetails->email}}</td>
                                <td>{{$partnerDetails->total_clicks}}</td>
                                <td>@if(trim($partnerDetails->date_last_clicked) != '')
                                    {{date('M d Y',strtotime($partnerDetails->date_last_clicked))}}
                                    @endif
                                </td>
                                <td>{{$partnerDetails->total_orders}}</td>
                                <td>{{number_format($partnerDetails->earned_commission,2)}}</td>
                                <td>
                                    <div class="btn-group text-right">
                                        <?php
                                            if($partnerDetails->iActive == 1){
                                                $statusName = "Active";
                                                $btnClass = "btn-success";
                                            }
                                            else{
                                                $statusName = "Inactive";
                                                $btnClass = "btn-danger";
                                            }
                                        ?>
                                        <button type="button" class="btn {{$btnClass}} br2 btn-xs fs12 dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> {{$statusName}}
                                            <span class="caret ml5"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                @if ($partnerDetails->iActive == 1)
                                                    <a href="javascript:void(0)" onclick="updatePartnerStatus('{{$partnerDetails->id}}','0','Inactivate')">Inactive</a>
                                                @else
                                                    <a href="javascript:void(0)" onclick="updatePartnerStatus('{{$partnerDetails->id}}','1','Activate')">Active</a>
                                                @endif
                                            </li>
                                            
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div class="navbar-btn btn-group">
                                        <a href="{{ url('/view_partner') }}/{{$partnerDetails->id}}" class="button form-control btn-info" title="View Partner Details">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                    </div>
                                    <div class="navbar-btn btn-group">
                                        <a href="{{ url('/edit_partner') }}/{{$partnerDetails->id}}" class="button form-control btn-info" title="Edit Partner">
                                            <span class="fa fa-pencil-square"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">No Partner account found.</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="panel-footer clearfix">
            {!! $partnersAry->render() !!}
        </div>
    </div>
    <div id="popupbs">
        <div class="modal fade" id="partnerStatusModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form method="POST" action="{{ url('/updatePartnerStatus') }}" class="form-horizontal" role="form" >
                        {{ csrf_field() }}
                        <input type="hidden" name="idPartner" value="" id="idPartner" />
                        <input type="hidden" name="iActive" value="" id="iActive" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Update Partner Status</h4>
                        </div>
                        <div class="modal-body">
                            <p id="message"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Yes</button>
                            <button type="button" class="btn btn-info" data-dismiss="modal">No</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection