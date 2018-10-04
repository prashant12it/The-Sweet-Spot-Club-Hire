@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/club_courier_orders')}}"><span class="glyphicon glyphicon-shopping-cart"></span> Club Courier Orders</a>
            </li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <form method="POST" enctype="multipart/form-data" action="{{ url('/club_courier_search_order') }}" class="form-horizontal" role="form" id="account2">
                {{ csrf_field() }}
                <div class="panel-menu admin-form theme-primary">
                    <div class="row">
                        <?php
                            if(session('searchOrder')){
                                $searchOrder = session('searchOrder');
                            }
                            else{
                                $searchOrder = array();
                                $searchOrder['filter_status'] = '';
                                $searchOrder['filter_date'] = '';
                                $searchOrder['filter_user'] = '';
                                $searchOrder['filter_product_type'] = '';
                            }
                        ?>
                        <div class="col-md-3">
                            <label for="filter-datepicker" class="field prepend-picker-icon date" data-provide="datepicker">
                                <input value="{{$searchOrder['filter_date']}}" id="filter-datepicker" name="searchAry[filter_date]" class="gui-input hasDatepicker" placeholder="Order Date" type="text">
                                <button type="button" class="ui-datepicker-trigger"><i class="fa fa-calendar-o"></i></button>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="field select">
                                
                                <select id="filter_status" name="searchAry[filter_status]" class="empty">
                                    <option value="">Order Status</option>
                                     @if ($statusAry->count() > 0)
                                        @foreach ($statusAry as $status)
                                            @if ($status->id>1 && $status->id<5)
                                                <option value="{{$status->id}}" <?php echo ($searchOrder['filter_status'] == $status->id ? "selected='selected'" : "");?>>{{$status->status}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="">No status found in database.</option>
                                    @endif
                                </select>
                                <i class="arrow double"></i>
                            </label>
                        </div>
                         <div class="col-md-3">
                            <label for="filter-customer" class="field">
                                <input value="{{$searchOrder['filter_user']}}" id="filter-user" name="searchAry[filter_user]" class="gui-input" placeholder="Customer Name" type="text">
                            </label>
                        </div>
                         <div class="col-md-2">
                             <a href="{{url('ccorder_courier')}}" class="button form-control btn-success pull-right">
                                 <i class="fa fa-truck"></i> Courier File
                             </a>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="button form-control btn-info pull-right"><i class="fa fa-search" aria-hidden="true"></i> </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url('/club_courier_orders') }}" class="button form-control btn-danger pull-right"><i class="fa fa-refresh" aria-hidden="true"></i> </a>
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
                        <th width="15%">Order Date</th>
                        <th width="15%">Order ID</th>
                        <th width="25%">Customer</th>
                        <th width="15%">Amount</th>
                        <th width="20%">Order Status</th>
                        <th width="10%"></th>
                    </tr>
                    @if ($ordersAry->count() > 0)
                        @foreach ($ordersAry as $oderDetails)
                            @if($oderDetails->order_status<5)
                            <tr>
                                <td>{{date('M d Y',strtotime($oderDetails->payment_date))}}</td>
                                <td>#{{$oderDetails->id}}</td>
                                <td>{{$oderDetails->user_name}}</td>
                                <td>{{number_format($oderDetails->total_amnt,2)}}</td>
                                <td>
                                    <div class="btn-group text-right">
                                        <?php
                                            if($oderDetails->order_status == 1){
                                                $statusName = "Uncompleted";
                                                $btnClass = "btn-default";
                                            }
                                            else if($oderDetails->order_status == 2){
                                                $statusName = "Pending";
                                                $btnClass = "btn-info";
                                            }
                                            else if($oderDetails->order_status == 3){
                                                $statusName = "Canceled";
                                                $btnClass = "btn-danger";
                                            }
                                            else if($oderDetails->order_status == 4){
                                                $statusName = "Completed";
                                                $btnClass = "btn-success";
                                            }
                                        ?>
                                        <button type="button" class="btn {{$btnClass}} br2 btn-xs fs12 dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> {{$statusName}}
                                            <span class="caret ml5"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            @foreach ($statusAry as $status)
                                                @if ($status->id != $oderDetails->order_status && $status->id>1 && $status->id<5)
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="updateOrderStatus('{{$oderDetails->id}}','{{$status->id}}')">{{$status->status}}</a>
                                                    </li>
                                                    <li class="divider"></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <div class="navbar-btn btn-group">
                                        <a href="{{ url('/view_club_courier_orders') }}/{{$oderDetails->id}}" class="button form-control btn-info" title="View Order Details">
                                            <span class="fa fa-eye"></span>
                                        </a>
                                    </div>
                                    
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">No Order found.</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="panel-footer clearfix">
            {!! $ordersAry->render() !!}
        </div>
    </div>
    <div id="popupbs">
        <div class="modal fade" id="updateStatusModal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form method="POST" action="{{ url('/ccupdate_order_status') }}" class="form-horizontal" role="form" >
                        {{ csrf_field() }}
                        <input type="hidden" name="idOrder" value="" id="idOrder" />
                        <input type="hidden" name="idStatus" value="" id="idStatus" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Update Order Status</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to update status?</p>
                            <br/>
                            <div id="canceledDescriptionDiv" style="display: none;">
                                <label>Reason </label>
                                <textarea name="cancelDescription" id="cancelDescription" rows="3" required="required" style="width: 100%"></textarea>
                            </div>
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