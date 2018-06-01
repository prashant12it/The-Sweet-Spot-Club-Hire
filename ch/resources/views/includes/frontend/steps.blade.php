<section id="first-hire-block">
    <div class="container-fluid">
        <div class="row hire-row1">
            <div class="col-xs-12 col-sm-3 col-md-3">
                <div class="yb">
                    <div class="table-style">
                        <span>{{__('Date:')}}</span>
                    </div>
                </div>
                <div class="{{($filter == 'date'?'active':'black-b')}}">

                    <div class="table-style">
                        <a href="{{url('/')}}"><span><sub>{{date('jS M Y',strtotime(session()->get('fromDate')))}} - {{date('jS M Y',strtotime(session()->get('toDate')))}} {{__('(change)')}}</sub></span>
                        </a>
                    </div>
                </div>
                <input type="hidden" id="state_id" name="state_id" value="{{session()->get('states')}}">
                <input type="hidden" id="fromDate" name="fromDate" value="{{session()->get('fromDate')}}">
                <input type="hidden" id="toDate" name="toDate" value="{{session()->get('toDate')}}">
                <input type="hidden" name="order_reference_id" id="order_reference_id"
                       value="{{(isset($_COOKIE['order_reference_id'])?$_COOKIE['order_reference_id']:time())}}"/>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3">
                <div class="yb">
                    <div class="table-style">
                        <span>{{__('Club selection')}}</span>
                    </div>
                </div>
                <div class="{{($filter == 'sets'?'active':'black-b')}}">
                    <a href="{{url('/clubsearch')}}">
                        <div class="table-style">
                            <span>{{__('Sets')}} ({{$setCount}})</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3">
                <div class="yb">
                    <div class="table-style">
                        <span>{{__('Extras & Insurance')}}</span>
                    </div>
                </div>
                <div class="{{($filter == 'insurance'?'active':'black-b')}}">
                    <a href="{{url('/insurance')}}">
                        <div class="table-style">
                            <span>({{($insurance>0?'1':'0')}})</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3">
                <div class="yb">
                    <div class="table-style">
                        <span>{{__('Delivery & Payment')}}</span>
                    </div>
                </div>
                <div class="{{($filter == 'payment'?'active':'black-b')}}">
                    <div class="table-style">
                        <span>{{__('Pick up & Drop off')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>