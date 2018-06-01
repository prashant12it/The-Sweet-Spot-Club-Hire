@extends('layouts.empty_cart')

@section('content')
    <section id="third-hire-block">
        <div class="container-fluid">
            <div class="col-sm-12 col-md-10 col-md-offset-1">
                <div class="row insurance">
                    <div class="col-md-12 clearfix">
                        <h2>Your cart is empty. Please go back and book your club sets. </h2>
                        <div class="table-responsive" style="color: #000">
                            <p>If you are not redirected to booking page in some time, <a href="{{url('../')}}">Click here</a> for booking page.</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </section>
@endsection