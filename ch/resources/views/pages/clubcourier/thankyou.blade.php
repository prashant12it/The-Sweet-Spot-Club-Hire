@extends('layouts.clubcourier')

@section('content')
    <section id="ccbooking-banner">
        <div class="overlap-black-shadow"></div>
        <div class="hire-caption">
            <h3>Thank You!</h3>
            <p>{{$PageDescription1}}<br/>
                {{$PageDescription2}}</p>
            @if($errorFlag == 0)
                <p class="alert alert-success">Your order has been successfully placed.</p>
            @else
                <p class="alert alert-success">{{$error}}</p>
            @endif
        </div>
    </section>
    <section id="third-hire-block">
        <div class="container-fluid">
            @if($errorFlag == 0)
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <div class="row insurance">
                        <div class="col-md-12 clearfix">
                            <h2>Your order Details</h2>
                            <div class="table-responsive" style="color: #000">
                                <table class="table table-striped">
                                    <tr>
                                        <td><b>Order Reference ID</b></td>
                                        <td>{{$checkOrderExist->order_reference_id}}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>{{($checkOrderExist->payment_option == 3?'Payer ID':($checkOrderExist->payment_option == 1?'Merchant ID':($checkOrderExist->payment_option == 2?'Vendor ID':'')))}}</b>
                                        </td>
                                        <td>{{$checkOrderExist->merchant_email}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Email ID</b></td>
                                        <td>{{$checkOrderExist->user_email}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Amount Paid</b></td>
                                        <td>${{number_format($checkOrderExist->paid_amnt,'2','.',',')}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Payment Gateway</b></td>
                                        <td>{{($checkOrderExist->payment_option == 1?'Pay Dollar':($checkOrderExist->payment_option == 2?'NAB Transact':'Stripe'))}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Status</b></td>
                                        <td>{{$checkOrderExist->payment_success_response}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Date/Time</b></td>
                                        <td>{{date('jS M Y / h:i:s A',strtotime($checkOrderExist->dtCreatedOn))}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-1"></div>
            @endif
        </div>
    </section>
@endsection