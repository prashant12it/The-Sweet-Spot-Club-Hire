@extends('layouts.frontend')

@section('content')
@include('includes.frontend.steps')
<section id="secound-hire-block">
    <div class="container-fluid">
        <div class="col-xs-12 col-sm-12">
            <div class="col-xs-12 col-sm-3 large-filter-title">
                <span>Filter your sets</span>
            </div>
            <div class="pull-left mobile-filter">
                <button class="btn frontend-primary-btn clear-btn" onclick="showhidefilter();">
                    Filter sets
                </button>
            </div>
            <div class="pull-right">
                <button class="btn frontend-primary-btn clear-btn" onclick="clearFilterCart();">
                    Clear All
                </button>
            </div>
        </div>
        <div class="filter-opt">
		<?php $i = 0; ?>
        @foreach($AttributesArr as $attributes)
        <div class="col-xs-12 col-sm-3">
            @if($attributes->attrib_name != 'Handicap')
            <strong>{{__($attributes->attrib_name)}}</strong>
            <input type="hidden" id="attrib-{{$attributes->id}}" name="attr[]" value="{{$defaultFilterArr[$i++]}}"/>
            <ul>
                @foreach($AttribOptsArr as $options)
                @if($options->attrib_id == $attributes->id)
                <li>
                    <button class="attrib-{{$attributes->id}} {{(in_array($options->id,$defaultFilterArr)? 'active':'')}}"
                            onclick="setAttribVal('{{$options->id}}','attrib-{{$attributes->id}}',this)">
                        {{$options->value}}
                    </button>
                </li>
                @endif
                @endforeach
            </ul>
                @endif
        </div>
        @endforeach
        </div>
    </div>
</section>
<section id="third-hire-block">
    <div class="container-fluid">

        @include('includes.frontend.cart')

    </div>
    <div class="col-sm-8 col-md-9">
        <div class="row product-listing">
            @if(!empty($AllAvailProdsArr))
            @foreach($AllAvailProdsArr as $key => $prods)
			<?php $ArrCount = count( $prods ); ?>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="product-panel clearfix">
                    <h3>{{$prods[$ArrCount-1]['parent-prod-name']}}</h3>
                    <div class="row">
                        <div class="col-xs-12">
                            <img src="/product_img/{{(!empty($prods[$ArrCount-1]['parent-prod-feat_img'])?$prods[$ArrCount-1]['parent-prod-feat_img']:'commingsoon.png')}}"
                                 alt=""/>
                            <span id="hid-desc-{{$prods[$ArrCount-1]['parent-prod-id']}}" class="hidden-desc">{!!$prods[$ArrCount-1]['parent-prod-description']!!}</span>
                        </div>
                    </div>
                    <span>&nbsp;</span>
                    <div class="clearfix">
                        <a href="javascript:void(0);"><h3>
                                Handicap: {!!$prods[$ArrCount-1]['parent-prod-attrib-handicap']!!}
                            </h3>
                        </a>
                        <br />
                        <a href="javascript:void(0);" onclick="showProdDets('{{$prods[$ArrCount-1]['parent-prod-name']}}','{{$prods[$ArrCount-1]['parent-prod-id']}}',false,'{{(!empty($prods[$ArrCount-1]['parent-prod-feat_img'])?$prods[$ArrCount-1]['parent-prod-feat_img']:'commingsoon.png')}}');"><h3>
                                View More
                            </h3>
                        </a>
                    </div>
                    <div class="bottom-btn clearfix">
                        <div class="btn-group number-spinner">
										<span class="input-prepend data-dwn">
											<button class="btn btn-default qty-decrease left-btn glyphicon glyphicon-play"
                                                    data-dir="dwn"
                                                    onclick="quantityCounter('qty-decrease','qty-{{$prods[$ArrCount-1]['parent-prod-id']}}');"></button>
										</span>

                            <input type="text" id="quantity-{{$prods[$ArrCount-1]['parent-prod-id']}}"
                                   class="form-control qty-{{$prods[$ArrCount-1]['parent-prod-id']}} input-box text-center"
                                   value="1" min="1"
                                   max="{{$prods[$ArrCount-1]['parent-prod-quantity']}}"
                                   name="quantity-{{$prods[$ArrCount-1]['parent-prod-id']}}" style="max-width:100px;">
                            <span class="input-append data-up">
											<button class="btn btn-default qty-increase glyphicon glyphicon-play"
                                                    data-dir="up"
                                                    onclick="quantityCounter('qty-increase','qty-{{$prods[$ArrCount-1]['parent-prod-id']}}');"></button>
										</span>
                        </div>
                        @if(!empty($prods))
                        @for($i=0; $i<$ArrCount-1;$i++)
                        <input type="hidden" name="childprodid-{{$prods[$ArrCount-1]['parent-prod-id']}}[]"
                               value="{{$prods[$i]->id}}"/>
                        @endfor
                        @endif
                        <button class="add-cart"
                                onclick="addToCart('{{$prods[$ArrCount-1]['parent-prod-id']}}','childprodid-{{$prods[$ArrCount-1]['parent-prod-id']}}','quantity-{{$prods[$ArrCount-1]['parent-prod-id']}}');">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
                @else
                <h2>No Clubs Available</h2>
                <p>There are no sets available to match your current search. Please try another shaft type or amend your collection and drop off dates for further availability.</p>
            @endif
        </div>
    </div>
    @include('includes.frontend.upsell')
    </div>
    @include('includes.frontend.popupMessage')
</section>
@if($showGift && !empty($giftProdId))
    <script>
        var GiftProds = new Array();
        @foreach($giftProdId as $giftProd)
        GiftProds.push('{{$giftProd}}');
        @endforeach
        setTimeout(function () {
            getGift(GiftProds);
        },1000);
    </script>
    @endif
@endsection