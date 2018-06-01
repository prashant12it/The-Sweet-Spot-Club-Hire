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
                @elseif ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
                @endif
                <div class="row">
                    <div class="col-sm-6 col-xl-6">
                        <div class="panel panel-tile text-center br-a br-grey">
                            <div class="panel-body">
                                <h1 class="fs30 mt5 mbn">{{$partnerDetailsData->total_clicks}}</h1>
                            </div>
                            <div class="panel-footer br-t p12">
                                <span class="fs11">
                                    <i class="fa fa-hand-o-up pr5"></i>
                                    <b>TOTAL CLICKS</b>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-6">
                        <div class="panel panel-tile text-center br-a br-grey">
                            <div class="panel-body">
                                <h1 class="fs30 mt5 mbn"><?php if(trim($partnerDetailsData->date_last_clicked) != ''){ echo date('M d Y',strtotime($partnerDetailsData->date_last_clicked));}else{ echo "N.A";}?></h1>
                            </div>
                            <div class="panel-footer br-t p12">
                                <span class="fs11">
                                    <i class="fa fa-calendar pr5"></i>
                                    <b>LAST CLICKED DATE</b>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-sm-6 col-xl-6">
                        <div class="panel panel-tile text-center br-a br-grey">
                            <div class="panel-body">
                                <h1 class="fs30 mt5 mbn">{{$partnerDetailsData->total_orders}}</h1>
                            </div>
                            <div class="panel-footer br-t p12">
                                <span class="fs11">
                                    <i class="fa fa-shopping-cart pr5"></i>
                                    <b>TOTAL ORDERS</b>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-6">
                        <div class="panel panel-tile text-center br-a br-grey">
                            <div class="panel-body">
                                <h1 class="fs30 mt5 mbn">${{number_format($partnerDetailsData->earned_commission,2)}}</h1>
                            </div>
                            <div class="panel-footer br-t p12">
                                <span class="fs11">
                                    <i class="fa fa-money pr5"></i>
                                    <b>EARNED COMMISSION</b>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection