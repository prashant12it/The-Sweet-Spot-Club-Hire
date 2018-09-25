<div class="col-sm-4 col-md-3 your-order-panel">
	<div class="right-side-form" data-spy="affix" data-offset-top="800">
		<div class="top-head">
			<div class="col-xs-10">
				<h3>Your Order</h3>
				<p>{{date('jS F Y',strtotime(session()->get('fromDate')))}} - {{date('jS F
					Y',strtotime(session()->get('toDate')))}}</p>
			</div>
			<div class="col-xs-2">
				<i class="fa fa-shopping-cart" aria-hidden="true"></i>
			</div>
		</div>
		<div class="inner-body cart-dets">
			<?php $total = 0;
			$state = session()->get('states');
			$estShipping = $EstimatedShipping[$state];?>
			@if(!empty($cartDetailArr))
			@foreach($cartDetailArr as $cartkey => $cartprods)

			<div class="item-view">
				<div class="row">
					<div class="col-xs-7">
						<p>{{$cartprods['prod-name']}}</p>
                        @if(!empty($cartprods['allAttribSet']))
                            @foreach($cartprods['allAttribSet'] as $attrKey => $attributesSet)
						@if(!empty($attributesSet))
                                    <button class="close-btns"
                                            onclick="removeSet('{{$_COOKIE['order_reference_id']}}','{{$cartprods['prodidArr'][$attrKey]}}');">
                                        X
                                    </button>
						<ul class="clearfix">
							@foreach($attributesSet as $attributes)
							<li>
								<button>{{$attributes->value}}</button>
							</li>
							@endforeach
						</ul>

						@endif
                            @endforeach
                        @endif
					</div>
					<div class="col-xs-5">
                        @if($cartprods['price'] > 0)
						<button class="close-btns"
						        onclick="removeCartProductConfirmation('{{$_COOKIE['order_reference_id']}}','prods-{{$cartkey}}');">
							X
						</button>

                            @endif
                            <p></p>
                            <img src="/product_img/{{(!empty($cartprods['parent-prod-feat_img'])?$cartprods['parent-prod-feat_img']:'comingsoon.png')}}"
                                 alt=""/>
					</div>
				</div>
				<div class="btn-group check-out">
					<label>Qty:</label>
                    @if($cartprods['price'] > 0)<button class="btn btn-default glyphicon glyphicon-minus leftmove" onclick="decreaseQuantity('prods-{{$cartkey}}','{{$cartprods['product_type']}}','{{$cartprods['quantity']}}');" data-dir="dwn"></button>@endif
					<input type="text" id="qtyval" class="form-control input-box text-center"
					       value="{{$cartprods['quantity']}}" min="0" max="180">
                    @if($cartprods['price'] > 0)<button class="btn btn-default glyphicon glyphicon-plus" data-dir="up" onclick="increaseQuantity('{{$cartkey}}','{{$cartprods['product_type']}}','{{$cartprods['parent-prod-id']}}');" ></button>@endif
				</div>


                @if(!empty($cartprods['childProdArr']))
                @foreach($cartprods['childProdArr'] as $childproducts)
                <input type="hidden" id="Avail-prods-{{$childproducts->id}}[]" name="Avail-prods-{{$cartkey}}[]" value="{{$childproducts->id}}"/>
                @endforeach
                @endif

				@if(!empty($cartprods['prodidArr']))
				@foreach($cartprods['prodidArr'] as $products)
				<input type="hidden" name="prods-{{$cartkey}}[]" value="{{$products}}"/>
				@endforeach
				@endif
				<span class="amt">{{($cartprods['price']>0?'$'.number_format($cartprods['price'],2,'.',','):'Free Gift')}}</span>
				<?php $total = $total + $cartprods['price']*$cartprods['quantity']; ?>
			</div>
			@endforeach
			<?php $total = number_format( $total, 2, '.', ',' ); ?>
		</div>
		<div class="bottom-ftr">
            <input type="hidden" name="setcount" id="setcount" value="{{$cartprods['setcount']}}">
            @if($cartprods['subtotal']>0)
            <div class="row">
                <span>Sub Total</span>
                <span>${{number_format($cartprods['subtotal'],2,'.',',')}}</span>
            </div>
            @endif
            @if($cartprods['Discount']>0)
            <div class="row">
                <span>Discount</span>
                <span>${{number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00),2,'.',',')}}</span>
            </div>
            @endif
            @if($cartprods['shipping']>0)
            <div class="row">
                <span>Shipping</span>
                <span>${{number_format($cartprods['shipping'],2,'.',',')}}</span>
            </div>
            @else
                <div class="row">
                    <span>Estimated Shipping</span>
                    <span>${{number_format($estShipping,2,'.',',')}}</span>
                </div>
            @endif
            @if($cartprods['insurance']>0)
            <div class="row">
                <span>Insurance</span>
                <span>${{number_format($cartprods['insurance'],2,'.',',')}}</span>
            </div>
            @endif
			<div class="row">
				<span>Total</span>
				<span>${{number_format((number_format($cartprods['subtotal'],2,'.','') + number_format(($cartprods['shipping']>0?$cartprods['shipping']:$estShipping),2,'.','') + number_format($cartprods['insurance'],2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00),2,'.','')),2,'.',',')}}</span>
			</div>
			<button id="continue-ordering" onclick="checkandproceed('{{$redirectPage}}','{{(isset($insurance) && $insurance==0?'1':'1')}}');">
				Continue
			</button>
		</div>
		@else
	</div>
	<div class="bottom-ftr empty-cart">
        <div class="row">
            <span style="width: 60%; float: left; color: #fff; font-size: 18px; padding: 0px 30px;">Estimated Shipping</span>
            <span style="width: 30%; float: left; color: #000; font-size: 18px; padding: 0px 30px;">${{number_format($estShipping,2,'.',',')}}</span>
        </div>
		<div class="row">
			<span>Your cart is empty.</span>
		</div>
	</div>
	@endif

</div>
<div id="removeprodpopupbs">
    <div class="modal fade" id="deleteConfirm" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <input type="hidden" id="order-ref-id" value="" />
                        <input type="hidden" id="Prod-Arr" value="" />
                        <h4 class="modal-title">Remove Product</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to remove this product from your cart?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="remove-Prod-Action" onclick="removeProd()" data-dismiss="modal" class="btn btn-danger">Yes</button>
                        <button type="button" id="CancelAction" class="btn btn-info frontend-primary-btn" data-dismiss="modal">No</button>
                    </div>
            </div>

        </div>

    </div>

</div>

<div id="actionInfo">
    <div class="modal fade" id="info-container" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <input type="hidden" id="order-ref-id" value="" />
                    <input type="hidden" id="Prod-Arr" value="" />
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="CancelAction" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Okay</button>
                </div>
            </div>

        </div>

    </div>

</div>