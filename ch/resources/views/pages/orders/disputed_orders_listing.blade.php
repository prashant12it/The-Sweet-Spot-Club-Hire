@extends('layouts.dashboard')

@section('content')

    <header id="topbar" class="alt">
        <div class="topbar-left">
            <ol class="breadcrumb">
                <li class="crumb-active">
                    <a href="{{url('/disputed_orders')}}"><span class="glyphicon glyphicon-shopping-cart"></span> Disputed Orders</a>
                </li>
            </ol>
        </div>
    </header>
    <section id="content">
        <div class="panel">
            <div class="panel-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th width="15%">Date</th>
                            <th width="15%">Order Reference ID</th>
                            <th width="25%">Customer</th>
                            <th width="15%">Amount</th>
                            <th width="20%">Action</th>
                            <th width="10%"></th>
                        </tr>
                        @if ($ordersAry->count() > 0)
                            @foreach ($ordersAry as $oderDetails)
                                <tr>
                                    <td>{{date('M d Y',strtotime($oderDetails->dtUpdatedOn))}}</td>
                                    <td>#{{$oderDetails->order_reference_id}}</td>
                                    <td>{{$oderDetails->buyer_first_name.' '.$oderDetails->buyer_last_name}}</td>
                                    <td>{{number_format($oderDetails->total_amnt,2)}}</td>
                                    <td>
                                        <div class="btn-group text-right">
                                            <?php
                                            if($oderDetails->payment_in_progress == 1){
                                                $statusName = "Disputed";
                                                $btnClass = "btn-danger";
                                            }
                                            else{
                                                $statusName = "Paid";
                                                $btnClass = "btn-success";
                                            }
                                            ?>
                                            <button type="button" class="btn {{$btnClass}} br2 btn-xs fs12 dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> {{$statusName}}
                                                <span class="caret ml5"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            <a href="javascript:void(0)" onclick="updateDisputedOrderStatus('{{$oderDetails->order_reference_id}}','1')">Create Order</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                    <li>
                                                        <a href="javascript:void(0)" onclick="updateDisputedOrderStatus('{{$oderDetails->order_reference_id}}','0')">Cancel Order</a>
                                                    </li>
                                                    <li class="divider"></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="navbar-btn btn-group">
                                            <a href="{{ url('/view_disputed_orders') }}/{{$oderDetails->id}}" class="button form-control btn-info" title="View Order Details">
                                                <span class="fa fa-eye"></span>
                                            </a>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">No Disputed Order found.</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="panel-footer clearfix">
                {{--{!! $ordersAry->render() !!}--}}
            </div>
        </div>
        <div id="popupbs">
            <div class="modal fade" id="updateStatusModal" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <form method="POST" action="{{ url('/create_order_by_admin') }}" class="form-horizontal" role="form" >
                            {{ csrf_field() }}
                            <input type="hidden" name="idOrder" value="" id="idOrder" />
                            <input type="hidden" name="idStatus" value="" id="idStatus" />
                            <input type="hidden" name="adminFlag" value="1" id="adminFlag" />
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Update Order Status</h4>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to update status?</p>
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