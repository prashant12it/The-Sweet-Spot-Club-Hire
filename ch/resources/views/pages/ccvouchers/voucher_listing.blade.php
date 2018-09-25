@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/voucher_management')}}"><span class="glyphicon glyphicon-gift"></span> Vouchers</a>
            </li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <form method="POST" enctype="multipart/form-data" action="{{ url('/search_voucher') }}" class="form-horizontal" role="form" id="account2">
                {{ csrf_field() }}
                <div class="panel-menu admin-form theme-primary">
                    <div class="row">
                        <?php
                            if(session('searchVoucher')){
                                $searchOffer = session('searchVoucher');
                            }
                            else{
                                $searchOffer = array();
                                $searchOffer['offer_name'] = '';
                                $searchOffer['dt_from'] = '';
                                $searchOffer['dt_to'] = '';
                                $searchOffer['offer_type'] = '';
                            }
                        ?>
                        <div class="col-md-3">
                            <label for="filter-name" class="field">
                                <input value="{{$searchOffer['offer_name']}}" id="offer_name" name="searchAry[offer_name]" class="gui-input" placeholder="Voucher Name.." type="text">
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label for="filter-datepicker" class="field prepend-picker-icon date" data-provide="datepicker">
                                <input value="{{$searchOffer['dt_from']}}" id="dt_from" name="searchAry[dt_from]" class="gui-input hasDatepicker" placeholder="From.." type="text">
                                <button type="button" class="ui-datepicker-trigger"><i class="fa fa-calendar-o"></i></button>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label for="filter-datepicker" class="field prepend-picker-icon date" data-provide="datepicker">
                                <input value="{{$searchOffer['dt_to']}}" id="dt_to" name="searchAry[dt_to]" class="gui-input hasDatepicker" placeholder="Upto.." type="text">
                                <button type="button" class="ui-datepicker-trigger"><i class="fa fa-calendar-o"></i></button>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="field select">

                                <select id="offer_type" name="searchAry[offer_type]">
                                    <option value="" >Voucher Type</option>
                                    <option value="1" {{($searchOffer['offer_type'] === '1' ? "selected='selected'":"")}}>Percentage</option>
                                    <option value="0" {{($searchOffer['offer_type'] === '0' ? "selected='selected'":"")}}>Amount</option>
                                </select>
                                <i class="arrow double"></i>
                            </label>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="button form-control btn-info pull-right"><i class="fa fa-search" aria-hidden="true"></i> </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/voucher_management') }}" class="button form-control btn-danger pull-right"><i class="fa fa-refresh" aria-hidden="true"></i> </a>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/add_voucher') }}" class="button form-control btn-success pull-right" title="Add New Voucher"><i class="fa fa-plus" aria-hidden="true"></i> </a>
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
                        <th width="12%">Valid From</th>
                        <th width="12%">Valid Upto</th>
                        <th width="15%">Name</th>
                        <th width="11%">Code</th>
                        <th width="10%">Redeemed Count</th>
                        <th width="10%">Redeemed Amt.($)</th>
                        <th width="15%">Voucher Type</th>
                        <th width="15%"></th>
                    </tr>
                    @if ($offersAry->count() > 0)
                        @foreach ($offersAry as $offerDetails)
                            <tr>
                                <td>{{date('M d Y',strtotime($offerDetails->dt_from))}}</td>
                                <td>{{date('M d Y',strtotime($offerDetails->dt_upto))}}</td>
                                <td>{{$offerDetails->name}}</td>
                                <td>{{$offerDetails->szCoupnCode}}</td>
                                <td>{{$offerDetails->redeemedCount}}</td>
                                <td>{{number_format($offerDetails->redeemAmount,2)}}</td>
                                <td>{{($offerDetails->offer_type == 1 ? "Percentage":"Amount")}}</td>
                                <td>
                                    <div class="navbar-btn btn-group">
                                        <a href="{{ url('/view_voucher') }}/{{$offerDetails->id}}" class="button form-control btn-info" title="View Voucher Details">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                    </div>
                                    <div class="navbar-btn btn-group">
                                        <a href="{{ url('/edit_voucher') }}/{{$offerDetails->id}}" class="button form-control btn-info" title="Edit Voucher">
                                            <span class="fa fa-pencil-square"></span>
                                        </a>
                                    </div>
                                    <div class="navbar-btn btn-group">
                                        <a href="javascript:void(0)" class="button form-control btn-danger" title="Delete Voucher" onclick="deleteOffer('{{$offerDetails->id}}');">
                                            <span class="fa fa-trash-o"></span>
                                        </a>
                                    </div>
                                    
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">No Voucher found.</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="panel-footer clearfix">
            {!! $offersAry->render() !!}
        </div>
    </div>
    <div id="popupbs">
        <div class="modal fade" id="deleteOfferModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form method="POST" action="{{ url('/delete_voucher') }}" class="form-horizontal" role="form" >
                        {{ csrf_field() }}
                        <input type="hidden" name="idOffer" value="" id="idOffer" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Delete Voucher</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this voucher?</p>
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