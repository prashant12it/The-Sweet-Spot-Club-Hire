@extends('layouts.frontend')

@section('content')
@include('includes.frontend.steps')
<section id="third-hire-block">
    <div class="container-fluid">
        @include('includes.frontend.cart')
    </div>
    <div class="col-sm-8 col-md-9">
        <div class="row insurance">
            <div class="col-md-2">
                <img src="{{ URL::asset('frontend/images/insurance-300x263.jpg')}}"/>
            </div>
            <div class="col-md-10 clearfix">
                <h4>Insurance</h4>
                <p>Protect yourself against the loss or damage to your hire set/s.</p>
                <p>A fee of $10 AUD set per rental will be charged . This covers you for breakages and damage to the clubs. </p>
                <p>An excess will apply if a club is lost or stolen. Please see below for our excess policy.</p>
                <p>By not protecting the clubs, clients may be subject to the charges listed.</p>
                <div class="col-md-12">
                    <div class="pull-right">
                        <a id="insurance-no" class="insurance-opt {{($insurance==0?'active':'')}}" href="javascript:void(0)"
                           onclick="addremoveInsuranceToOrder('{{$_COOKIE['order_reference_id']}}','2','insurance','1')"><i
                                    class="fa fa-times"></i> No</a>
                        <a id="insurance-yes" class="insurance-opt  {{($insurance>0?'active':'')}}" href="javascript:void(0)"
                           onclick="addremoveInsuranceToOrder('{{$_COOKIE['order_reference_id']}}','1','insurance','1')"><i
                                    class="fa fa-check"></i> Yes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-12 excess-cst">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <button class="btn clear-btn btn-xs" type="button" onclick="openTable();">View Excess Costs <i class="fa fa-arrow-circle-down"></i> </button>
                    </div>
                    <div class="col-xs-12 col-sm-3">
                    </div>
                </div>
            </div>
            <div class="col-md-12 excess-cost">
                <div class="table-responsive" style="color: #000">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th colspan="5" class="col-xs-12">TSS Insurance for Damages/ Stolen Equipment</th>
                        </tr>

                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-xs-4"></td>
                            <td colspan="2" class="col-xs-4">Damaged</td>
                            <td colspan="2" class="col-xs-4">Lost/Stolen</td>
                        </tr>
                        <tr>
                            <td>Club</td>
                            <td >No Cover</td>
                            <td>TSS Cover</td>
                            <td>No Cover</td>
                            <td>TSS Cover</td>
                        </tr>
                        <tr>
                            <td>Driver</td>
                            <td>$90</td>
                            <td>$0</td>
                            <td>$600</td>
                            <td>$360</td>
                        </tr>
                        <tr>
                            <td>Fairway</td>
                            <td>$80</td>
                            <td>$0</td>
                            <td>$400</td>
                            <td>$240</td>
                        </tr>
                        <tr>
                            <td>Rescue</td>
                            <td>$80</td>
                            <td>$0</td>
                            <td>$400</td>
                            <td>$240</td>
                        </tr>
                        <tr>
                            <td>Irons</td>
                            <td>$60</td>
                            <td>$0</td>
                            <td>$150</td>
                            <td>$80</td>
                        </tr>
                        <tr>
                            <td>Putter</td>
                            <td>$70</td>
                            <td>$0</td>
                            <td>$200</td>
                            <td>$80</td>
                        </tr>
                        <tr>
                            <td>Umbrella</td>
                            <td>$15</td>
                            <td>$0</td>
                            <td>$30</td>
                            <td>$0</td>
                        </tr>
                        <tr>
                            <td>Tour Bag</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>$250</td>
                            <td>$50</td>
                        </tr>
                        <tr>
                            <td>Head Cover</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>$20</td>
                            <td>$0</td>
                        </tr>
                        <tr>
                            <td>Putter Cover</td>
                            <td>N/A</td>
                            <td>N/A</td>
                            <td>$20</td>
                            <td>$0</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 extra-prod">
<div class="row">

    <div class="col-md-2">
        <img src="{{ URL::asset('frontend/images/extras.jpg')}}"/>
    </div>
    <div class="col-md-10 clearfix">
        <h4>Extras</h4>
        <p>The Sweet Spot Golf Hire has a wide range of golf accessories and essential tools to complement and support your golf game.</p>
        <p>We have all kinds of tools that can help improve your game and make you stand out on, and off, the course.</p>
    </div>
</div>
            </div>
        </div>
    </div>
    @include('includes.frontend.upsell')
    </div>
    @include('includes.frontend.popupMessage')
</section>
@endsection