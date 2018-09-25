@extends('layouts.dashboard')

@section('content')
<section id="content">
    <div class="panel">
        <div class="panel mb25 mt5">
            <div class="panel-heading">
                <span class="panel-title hidden-xs">{{$offerDetailsData->name}} </span>
                <ul class="nav panel-tabs-border panel-tabs">
                    <li class="active">
                        <a href="#offer_tab" data-toggle="tab" aria-expanded="false">Voucher Details</a>
                    </li>
                    <li class="">
                        <a href="#customer_tab" data-toggle="tab" aria-expanded="true">Order List</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body p25 pb5">
                <div class="tab-content pn br-n admin-form">
                    
                    <!--START OFFER DETAILS DIV-->
                    <div id="offer_tab" class="tab-pane active">
                        <div class="panel">
                            <div class="panel-body pn">
                                <table class="table order-details-table">
                                    <thead>
                                        <tr class="hidden">
                                            <th width="50%">Voucher Details</th>
                                            <th width="50%">Voucher Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>Voucher ID : </strong>#{{$offerDetailsData->id}}<br/>
                                            </td>
                                            <td>
                                                <strong>Voucher Name : </strong>{{$offerDetailsData->name}}<br/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Voucher Code : </strong>{{$offerDetailsData->szCoupnCode}}<br/>
                                            </td>
                                            <td>
                                                <strong>Voucher Type : </strong>{{($offerDetailsData->offer_type == 1 ? 'Percentage':'Amount')}}<br/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php
                                                if($offerDetailsData->offer_type == 1){
                                                    ?>
                                                        <strong>Voucher Percentage : </strong>{{number_format($offerDetailsData->offer_percntg,2)}}<br/>
                                                    <?php
                                                }else{ ?>
                                                        <strong>Voucher Amount($) : </strong>{{number_format($offerDetailsData->offer_amnt,2)}}<br/>
                                                <?php }?>
                                                
                                            </td>
                                            <td>
                                                <strong>One Time Voucher : </strong>{{($offerDetailsData->isOneTimeOffer == 1 ? 'Yes':'No')}}<br/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Voucher From : </strong><?php echo date('M d Y', strtotime($offerDetailsData->dt_from));?><br/>
                                            </td>
                                            <td>
                                                <strong>Voucher Upto : </strong><?php echo date('M d Y', strtotime($offerDetailsData->dt_upto));?><br/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Redeemed Count : </strong>{{$offerDetailsData->redeemedTime}}<br/>
                                            </td>
                                            <td>
                                                <strong>Total Redeemed Amount ($) : </strong>{{$offerDetailsData->redeemedAmount}}<br/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <strong>Voucher Description : </strong>{{$offerDetailsData->description}}<br/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--END OFFER DEATILS DIV-->
                    
                    <!--START CUSTOMER DETAILS DIV-->
                    <div id="customer_tab" class="tab-pane">
                        
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
                                                    <th width="10%">Order ID</th>
                                                    <th width="12%">Customer ID</th>
                                                    <th width="18%">Customer Name</th>
                                                    <th width="25%">Customer Email</th>
                                                    <th width="10%">Order Amt.($)</th>
                                                    <th width="10%">Voucher Amt.($)</th>
                                                    <th width="15%">Redeemed On</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($usersAry->count() > 0)
                                                    @foreach ($usersAry as $users)
                                                        <tr>
                                                            <td>#{{$users->idOrder}}</td>
                                                            <td>#{{$users->user_id}}</td>
                                                            <td>{{$users->user_name}}</td>
                                                            <td>{{$users->user_email}}</td>
                                                            <td>${{number_format($users->paid_amnt,2)}}</td>
                                                            <td>${{number_format($users->offer_amnt,2)}}</td>
                                                            <td><?php echo date('M d Y',  strtotime($users->payment_date));?></td>
                                                        </tr>
                                                    @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5">No Customer found.</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            
                    </div>
                    <!--END CUSTOMER DETAILS DIV-->
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
