@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/partners')}}"><span class="fa fa-users"></span> Partners</a>
            </li>
            <li class="crumb-trail">Partner Details</li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <div class="panel mb25 mt5">
            <div class="panel-heading">
                <span class="panel-title hidden-xs">{{$partnerDetailsData->name}} </span>
                <ul class="nav panel-tabs-border panel-tabs">
                    <li class="active">
                        <a href="#detail_tab" data-toggle="tab" aria-expanded="false">Partner Details</a>
                    </li>
                    <li class="">
                        <a href="#order_tab" data-toggle="tab" aria-expanded="true">Partner Booked Orders</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body p25 pb5">
                <div class="tab-content pn br-n admin-form">
                    
                    <!--START PARTNER DETAILS DIV-->
                    <div id="detail_tab" class="tab-pane active">
                        <div class="panel">
                            <div class="panel-body pn">
                                <table class="table order-details-table">
                                    <thead>
                                        <tr class="hidden">
                                            <th width="100%">Partner Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Email : </strong>{{$partnerDetailsData->email}}<br/></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address : </strong>{{$partnerDetailsData->address.', '.$partnerDetailsData->state.', '.$partnerDetailsData->zipcode.', '.$partnerDetailsData->country_name}}<br/></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contact : </strong>{{$partnerDetailsData->contact_no}}<br/></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Clicks : </strong>{{$partnerDetailsData->total_clicks}}<br/></td>
                                        </tr>
                                        @if(trim($partnerDetailsData->date_last_clicked) != '')
                                        <tr>
                                            <td><strong>Last Clicked : </strong>{{date('M d Y',strtotime($partnerDetailsData->date_last_clicked))}}<br/></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Total Orders : </strong>{{$partnerDetailsData->total_orders}}<br/></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Commission Earned($) : </strong>{{number_format($partnerDetailsData->earned_commission,2)}}<br/></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--END PARTNER DEATILS DIV-->
                    
                    <!--START ORDER DETAILS DIV-->
                    <div id="order_tab" class="tab-pane">
                        
                                <div class="panel">
                                    <div class="panel-heading zero-padding">
                                        <span class="panel-icon">
                                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        </span>
                                        <span class="panel-title"> Orders</span>
                                    </div>
                                    <div class="panel-body pn">
                                        <table class="table order-details-table">
                                            <thead>
                                                <tr>
                                                    <th width="20%">Order Date</th>
                                                    <th width="10%">Order ID</th>
                                                    <th width="15%">Booked From</th>
                                                    <th width="15%">Booked Upto</th>
                                                    <th width="15%">Order Amt.($)</th>
                                                    <th width="15%">Status</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($partner_orders->count() > 0)
                                                    @foreach ($partner_orders as $order)
                                                        <tr>
                                                            <td>{{date('M d Y',strtotime($order->payment_date))}}</td>
                                                            <td>#{{$order->id}}</td>
                                                            <td>{{date('M d Y',strtotime($order->dt_book_from))}}</td>
                                                            <td>{{date('M d Y',strtotime($order->dt_book_upto))}}</td>
                                                            <td>{{number_format($order->paid_amnt,2)}}</td>
                                                            <td>
                                                                <div class="btn-group text-right">
                                                                    <?php
                                                                        if($order->order_status == 1){
                                                                            $statusName = "Uncompleted";
                                                                            $btnClass = "btn-default";
                                                                        }
                                                                        else if($order->order_status == 2){
                                                                            $statusName = "Pending";
                                                                            $btnClass = "btn-info";
                                                                        }
                                                                        else if($order->order_status == 3){
                                                                            $statusName = "Canceled";
                                                                            $btnClass = "btn-danger";
                                                                        }
                                                                        else if($order->order_status == 4){
                                                                            $statusName = "Completed";
                                                                            $btnClass = "btn-success";
                                                                        }
                                                                    ?>
                                                                    <button type="button" class="btn {{$btnClass}} br2 btn-xs fs12"> {{$statusName}} </button>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group text-right">
                                                                    <a href="{{ url('/view_orders') }}/{{$order->id}}" class="btn btn-info btn-sm " title="View Order Details">
                                                                        <span class="fa fa-eye"></span>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5">No Order found.</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            
                    </div>
                    <!--END ORDER DETAILS DIV-->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
