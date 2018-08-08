@extends('layouts.frontend_shipping')

@section('content')

@include('includes.frontend.steps')

<section class="yellow-bg">
	<div class="container">
		<div class="cart-value">
			<table id="CartItems" class="table table-striped GridMain" cellspacing="0" cellpadding="0">
				<thead>
				<tr class="GridHeader">
					<th>YOUR ORDER : {{date('jS F Y',strtotime(session()->get('fromDate')))}} - {{date('jS F Y',strtotime(session()->get('toDate')))}}</th>
					<th>QUANTITY</th>
					<th>COST</th>
				</tr>
				</thead>
				<tbody>
				<?php $total = 0;?>
				@if(!empty($cartDetailArr))
				@foreach($cartDetailArr as $cartkey => $cartprods)
				<tr class="GridRow">
					<td data-title="YOUR ORDER : {{date('jS F Y',strtotime(session()->get('fromDate')))}} - {{date('jS F Y',strtotime(session()->get('toDate')))}}">{{$cartprods['prod-name']}}
						<br />
						@if(!empty($cartprods['allAttribSet']))
							@foreach($cartprods['allAttribSet'] as $attrKey => $attributesSet)
								@if(count($attributesSet)>0)
                                    <?php $totalAttrib = count($attributesSet);
                                    $i=1;?>
						-
						@foreach($attributesSet as $attributes)
						{{$attributes->value}}{{($i != $totalAttrib?', ':'')}}
						<?php $i++;?>
										@endforeach
								@endif
								<br>
							@endforeach
						@endif
					</td>
					<td data-title="QUANTITY">{{$cartprods['quantity']}}</td>
					<td data-title="COST">${{number_format($cartprods['price']*($cartprods['product_type']==5?$cartprods['quantity']:1),2,'.',',')}}</td>
				</tr>
				<?php $total = $total + $cartprods['price']*($cartprods['product_type']==5?$cartprods['quantity']:1); ?>
				@endforeach
				@endif
				<!--<tr class="GridRow">
					<td>Insurance</td>
					<td>{{($insurance >0?'1':'0')}}</td>
					<td>${{number_format($orderDetails->insurance_amnt,2,'.',',')}}</td>
				</tr>-->
				<!--                    --><?php //$total = $total + $orderDetails->insurance_amnt; ?>
				</tbody>
			</table>
			<table id="CartItems" class="table table-striped GridMain" cellspacing="0" cellpadding="0">
				<tbody>
				<tr class="GridRow">
					<td class="hidden-xs"></td>
					<td><strong>SUB TOTAL</strong></td>
					<td>${{number_format($total,2,'.',',')}}</td>
				</tr>
				<tr class="GridRow">
					<td class="hidden-xs"></td>
					<td><strong>MULTI SET DISCOUNT</strong></td>
					<td>${{number_format($cartprods['Discount'],2,'.',',')}}</td>
				</tr>
				<tr class="GridRow">
					<td class="hidden-xs"></td>
					<td><strong>PARTNER DISCOUNT</strong></td>
					<td>${{number_format(($orderDetails->partner_discount_amnt>0?$orderDetails->partner_discount_amnt:0.00),2,'.',',')}}</td>
				</tr>
				<tr class="GridRow">
					<td class="hidden-xs"></td>
					<td><strong>INSURANCE</strong></td>
					<td>${{number_format($orderDetails->insurance_amnt,2,'.',',')}}</td>
				</tr>
				<tr class="GridRow">
					<td class="hidden-xs"></td>
					<td><strong>HANDLING / DELIVERY FEE</strong></td>
					<td>${{number_format($orderDetails->shipping_amnt,2,'.',',')}}</td>
				</tr>
				@if(!empty($orderDetails->tss) && $orderDetails->tss > 0)
				<tr class="GridRow">
					<td class="hidden-xs"></td>
					<td><strong>TSS DISCOUNT</strong></td>
					<td><b>-</b>${{(!empty($orderDetails->tss)?$orderDetails->tss:'0.00')}}</td>
				</tr>
				@endif
                <tr class="GridRow">
                    <td class="hidden-xs"></td>
                    <td><strong>OFFER CODE</strong></td>
                    <td>{{(!empty($orderDetails->offer_Code)?$orderDetails->offer_Code:'N/A')}}</td>
                </tr>
                <tr class="GridRow">
                    <td class="hidden-xs"></td>
                    <td><strong>OFFER DISCOUNT</strong></td>
                    <td><b>- </b>${{(!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:'0.00')}}</td>
                </tr>
				<tr class="GridRow">
					<td class="hidden-xs"></td>
					<td><strong>TOTAL</strong></td>
					<td>${{number_format((number_format($total,2,'.','') + number_format($orderDetails->shipping_amnt,2,'.','') + number_format($orderDetails->insurance_amnt,2,'.','') - number_format($cartprods['Discount'] + ($orderDetails->partner_discount_amnt>0?$orderDetails->partner_discount_amnt:0.00) + ($orderDetails->tss>0?$orderDetails->tss:0.00) ,2,'.','') - number_format((!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:0.00),2,'.','')),2,'.',',')}}</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

<section id="details">
	<div class="container">
		<?php
		$cartProd = '';
		$duplicateNameHolder = array();
		$duplicateCart = array();
		?>
		<form method="post" action="{{$NabConfig['PostUrl']}}/live/hpp/payment" id="nabform">
			<input type="hidden" name="vendor_name" value="{{$NabConfig['vendorid']}}">
			<input type="hidden" name="print_zero_qty" value="false">
			@if(!empty($cartDetailArr))
			@foreach($cartDetailArr as $cartkey => $cartprods)
				<?php
								if(!in_array($cartprods['prod-name'],$duplicateNameHolder)){
								    array_push($duplicateNameHolder,$cartprods['prod-name']);
									$duplicateCart[$cartkey]['prod-name'] = $cartprods['prod-name'];
									$duplicateCart[$cartkey]['quantity'] = $cartprods['quantity'];
									$duplicateCart[$cartkey]['product_type'] = $cartprods['product_type'];
									$duplicateCart[$cartkey]['price'] = $cartprods['price'];
								}else{
								    foreach($duplicateCart as $Dcartkey => $Dcartprods){
								        if($cartprods['prod-name'] == $Dcartprods['prod-name']){
											$duplicateCart[$Dcartkey]['quantity'] = $duplicateCart[$Dcartkey]['quantity'] + $cartprods['quantity'];
										}
									}
								}

						?>
			{{--<input type="hidden" name="{{$cartprods['prod-name']}}" value="{{$cartprods['quantity']}},{{($cartprods['product_type']==5?$cartprods['price']:$cartprods['price']/$cartprods['quantity'])}}" />--}}
				<?php $cartProd .= "Name(product): ".$cartprods['prod-name']." - Value: ".$cartprods['quantity'].','.$cartprods['price'].PHP_EOL; ?>
			@endforeach
			@endif
			@if(!empty($duplicateCart))
				@foreach($duplicateCart as $Dcartkey => $Dcartprods)
					<input type="hidden" name="{{$Dcartprods['prod-name']}}" value="{{$Dcartprods['quantity']}},{{($Dcartprods['product_type']==5?$Dcartprods['price']:$Dcartprods['price']/$Dcartprods['quantity'])}}" />
                @endforeach
			@endif
			<input type="hidden" name="payment_alert" value="{{$supportEmail}}" />
			<input type="hidden" name="Discount" value="1,{{($cartprods['Discount']?-($cartprods['Discount']):0.00)+($cartprods['partnerDiscount']>0?-($cartprods['partnerDiscount']):0.00)+(!empty($cartprods['tss'])?-($cartprods['tss']):0.00)}}" />
			<input type="hidden" name="Insurance" value="1,{{($orderDetails->insurance_amnt?$orderDetails->insurance_amnt:0.00)}}" />
			<input type="hidden" name="Handling/delivery fee" value="1,{{($orderDetails->shipping_amnt?$orderDetails->shipping_amnt:0.00)}}" />
			<input type="hidden" name="Offer discount" value="{{($orderDetails->offer_amnt?1:0)}},{{($orderDetails->offer_amnt?-$orderDetails->offer_amnt:0.00)}}" />
			<input type="hidden" name="payment_reference" value="{{$orderDetails->order_reference_id}}" />
			<input type="hidden" name="Name" value="{{$orderDetails->buyer_first_name}}" />
			<input type="hidden" name="Surname" value="{{$orderDetails->buyer_last_name}}" />
			<input type="hidden" name="E-mail" value="{{$orderDetails->buyer_email}}" />
			<input type="hidden" name="Country" value="{{$country}}" />
			<input type="hidden" name="Phone-No" value="{{$orderDetails->phone_no_aus}}" />
			<input type="hidden" name="information_fields" value="Name,Surname,E-mail,Country,Phone-No" />
			<input type="hidden" name="reply_link_url" value="{{$siteUrl.'/thankyou/?payment_reference='}}">
			<input type="hidden" name="return_link_url" value="{{$siteUrl.'/thankyou/?payment_reference='}}">
			<input type="hidden" name="return_link_text" value="Click here to complete your order successfully." />

		</form>
		<?php
        /*$log  = "Form Post: ".$NabConfig['PostUrl'].'/live/hpp/payment'.PHP_EOL.
        "vendor_name: ".$NabConfig['vendorid'].PHP_EOL.
            $cartProd.
        "Discount: 1,".($cartprods['Discount']?-($cartprods['Discount']):0.00)+($cartprods['partnerDiscount']>0?-($cartprods['partnerDiscount']):0.00)+(!empty($cartprods['tss'])?-($cartprods['tss']):0.00).PHP_EOL.
        "Insurance: 1,".($orderDetails->insurance_amnt?$orderDetails->insurance_amnt:0.00).PHP_EOL.
            "Handling/delivery fee: 1,".($orderDetails->shipping_amnt?$orderDetails->shipping_amnt:0.00).PHP_EOL.
            "Offer discount: ".($orderDetails->offer_amnt?-$orderDetails->offer_amnt:0.00).PHP_EOL.
            "payment_reference: ".$orderDetails->order_reference_id.PHP_EOL.
            "Name: ".$orderDetails->buyer_first_name.PHP_EOL.
            "Surname: ".$orderDetails->buyer_last_name.PHP_EOL.
            "E-mail: ".$orderDetails->buyer_email.PHP_EOL.
            "information_fields: Name,Surname,E-mail,Country".PHP_EOL.
            "reply_link_url: ".$siteUrl.'/thankyou/?payment_reference='.PHP_EOL.
            "return_link_url: ".$siteUrl.'/thankyou/?payment_reference='.PHP_EOL.
            "return_link_text: Return to TSS Clubhire".PHP_EOL.
        "-------------------------".PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents('../log_check_'.time().'--'.$orderDetails->order_reference_id.'.txt', $log, FILE_APPEND);*/
		?>
		<form method="POST" action="{{$PaydollarConfig['PostUrl']}}eng/payment/payForm.jsp" name="payFormCcard" id="paydollarform">
			<input type="hidden" name="merchantId" value="{{$PaydollarConfig['merchantId']}}">
			<input type="hidden" name="amount" value="{{number_format((number_format($total,2,'.','') + number_format($orderDetails->shipping_amnt,2,'.','') + number_format($orderDetails->insurance_amnt,2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00) + ($cartprods['tss']>0?$cartprods['tss']:0.00),2,'.','') - number_format((!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:0.00),2,'.','')),2,'.','')}}" >
			<input type="hidden" name="orderRef" value="{{$orderDetails->order_reference_id}}">
			<input type="hidden" name="currCode" value="{{$PaydollarConfig['currCode']}}" >
			<input type="hidden" name="mpsMode" value="NIL" >
			<input type="hidden" name="successUrl"
			       value="{{$siteUrl.'/thankyou'}}">
			<input type="hidden" name="failUrl" value="{{$siteUrl}}">
			<input type="hidden" name="cancelUrl" value="{{$siteUrl}}">
			<input type="hidden" name="payType" value="N">
			<input type="hidden" name="lang" value="E">
			<input type="hidden" name="payMethod" value="WECHATONL">
			<input type="hidden" name="secureHash"
			       value="44f3760c201d3688440f62497736bfa2aadd1bc0">
			{{ csrf_field() }}
			<input type="hidden" name="order_reference_id" id="order_reference_id" value="{{$orderDetails->order_reference_id}}"/>
			<h3><span>Your Details</span></h3>
			<div class="row">
				<div class="form-group col-sm-6">
					<label>First Name ...</label>
					<input type="text" disabled="disabled" name="buyer_first_name" value="{{$orderDetails->buyer_first_name}}"/>

				</div>
				<div class="form-group col-sm-6">
					<label>Surname ...</label>
					<input type="text" disabled="disabled" name="buyer_last_name" value="{{$orderDetails->buyer_last_name}}"/>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-sm-6">
					<label>E-mail ...</label>
					<input type="text" disabled="disabled" name="buyer_email" value="{{$orderDetails->buyer_email}}"/>
				</div>
                <div class="form-group col-sm-6">
                    <label>Country of residence ...</label>
                    <input type="text" disabled="disabled" name="buyer_email" value="{{$country}}"/>
                </div>
				<div class="form-group col-sm-6">
					<label>Contact Phone Number In Australia ...</label>
					<input type="text" disabled="disabled" name="phone_no_aus" value="{{$orderDetails->phone_no_aus}}"/>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-sm-6">
					<h3><span>Your Delivery Details (Drop off)</span></h3>
					<div class="row">
						<div class="form-group col-sm-12">
							<div class="col-xs-12 col-sm-4 col-md-4">
								<input class="check-box place-dropoff" type="checkbox" disabled="disabled" name="dropoff_place" {{($orderDetails->dropoff_place == '1'?'checked="checked"':'')}} id="hotel-res-dropoff" value="1" onclick="checkPlace('dropoff',this);">Hotel / Resort
							</div>
							<div class="col-xs-12 col-sm-4 col-md-4">
								<input class="check-box place-dropoff" type="checkbox" disabled="disabled" name="dropoff_place" {{($orderDetails->dropoff_place == '2'?'checked="checked"':'')}} id="business-place-dropoff" value="2" onclick="checkPlace('dropoff',this);">Business
							</div>
							<div class="col-xs-12 col-sm-4 col-md-4">
								<input class="check-box place-dropoff" type="checkbox" disabled="disabled" name="dropoff_place" {{($orderDetails->dropoff_place == '3'?'checked="checked"':'')}} id="golf-course-dropoff" value="3" onclick="checkPlace('dropoff',this);">Golf Course
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12">
							<label>Name of hotel/golf course/accommodation to be delivered to ...</label>
							<input type="text" disabled="disabled" name="delvr_hotel_name" id="delvr_hotel_name" value="{{$orderDetails->delvr_hotel_name}}" style="background-image:none">

						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12">
							<label>Address for clubs to be delivered to (street no,Street name) ...</label>
							<input type="text" name="delvr_address" id="delvr_address" disabled="disabled" value="{{$orderDetails->delvr_address}}"/>
							</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6">
							<label>State of delivery address ...</label>
							<input type="text" name="delvr_address" disabled="disabled" id="delvr_address" value="{{$Deliverystates}}"/>
						</div>
						<div class="form-group col-sm-6">
							<label>Post code of delivery address ...</label>
							<input type="text" name="delvr_postal_code" id="delvr_postal_code" value="{{$orderDetails->delvr_postal_code}}" disabled="disabled"/>
							</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6">
							<label>Suburb ...</label>
							<input type="text" disabled="disabled" name="suburb" value="{{$orderDetails->suburb}}"/>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<h3><span>Your Delivery Details (Pick up)</span></h3>
					<div class="row">
						<div class="form-group col-sm-12">
							<div class="col-xs-12 col-sm-4 col-md-4">
								<input class="check-box place-pickup" type="checkbox" disabled="disabled" name="pickup_place" {{($orderDetails->pickup_place == '1'?'checked="checked"':'')}} id="hotel-res-pickup" value="1" onclick="checkPlace('pickup',this);">Hotel / Resort
							</div>
							<div class="col-xs-12 col-sm-4 col-md-4">
								<input class="check-box place-pickup" type="checkbox" disabled="disabled" name="pickup_place" {{($orderDetails->pickup_place == '2'?'checked="checked"':'')}} id="business-place-pickup" value="2" onclick="checkPlace('pickup',this);">Business
							</div>
							<div class="col-xs-12 col-sm-4 col-md-4">
								<input class="check-box place-pickup" type="checkbox" disabled="disabled" name="pickup_place" {{($orderDetails->pickup_place == '3'?'checked="checked"':'')}} id="golf-course-pickup" value="3" onclick="checkPlace('pickup',this);">Golf Course
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12">
							<label>Name of hotel/golf course/accommodation to be picked up from ...</label>
							<input type="text" disabled="disabled" name="pickup_hotel_name" id="pickup_hotel_name" value="{{$orderDetails->pickup_hotel_name}}">
							</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12">
							<label>Address for clubs to be picked up from (street no,Street name) ...</label>
							<input type="text" name="pickup_address" disabled="disabled" id="pickup_address"  value="{{$orderDetails->pickup_address}}"/>
							</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6">
							<label>State of delivery state ...</label>
							<input type="text" name="pickup_address" disabled="disabled" id="pickup_address"  value="{{$Pickupstates}}"/>
						</div>
						<div class="form-group col-sm-6">
							<label>Post code of pick up address ...</label>
							<input type="text" name="pickup_postal_code"  id="pickup_postal_code" value="{{$orderDetails->pickup_postal_code}}" disabled="disabled"/>

						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6">
							<label>Suburb ...</label>
							<input type="text" disabled="disabled" name="suburbpickup" value="{{$orderDetails->suburbpickup}}"/>
						</div>
					</div>

				</div>
			</div>
		</form>
			<div class="submit-btn">
				{{--<input type="button" onclick="showPayOpt('{{$paymentSwitch}}');" value="Confirm and Proceed to Payment"/>--}}
				{{--<input type="button" onclick="showPayOptStripe();" value="Confirm and Proceed to Payment"/>--}}
				<?php
                $log  = "Discount: 1,".(($cartprods['Discount']?-($cartprods['Discount']):0.00)+($cartprods['partnerDiscount']>0?-($cartprods['partnerDiscount']):0.00)+(!empty($cartprods['tss'])?-($cartprods['tss']):0.00)).PHP_EOL.
                "Insurance: 1,".($orderDetails->insurance_amnt?$orderDetails->insurance_amnt:0.00).PHP_EOL.
                "Handling/delivery fee: 1,".($orderDetails->shipping_amnt?$orderDetails->shipping_amnt:0.00).PHP_EOL.
                "Offer discount: ".($orderDetails->offer_amnt?-$orderDetails->offer_amnt:0.00).PHP_EOL.
                "order_reference: ".($orderDetails->order_reference_id).PHP_EOL.
                "Name: ".$orderDetails->buyer_first_name.PHP_EOL.
                "Surname: ".$orderDetails->buyer_last_name.PHP_EOL.
                "E-mail: ".$orderDetails->buyer_email.PHP_EOL.
                "Total: ".(number_format((number_format($total,2,'.','') + number_format($orderDetails->shipping_amnt,2,'.','') + number_format($orderDetails->insurance_amnt,2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00) + ($cartprods['tss']>0?$cartprods['tss']:0.00),2,'.','') - number_format((!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:0.00),2,'.','')),2,'.','')).PHP_EOL.
                "Date Time: ".(date('d-m-Y h:i:s')).PHP_EOL.
                "-------------------------".PHP_EOL;
                //Save string to log, use FILE_APPEND to append.
                file_put_contents('../stripe_log_check.txt', $log, FILE_APPEND);
				?>
				<form action="{{ URL::to('thankyou')}}" method="POST">
					<script
							src="https://checkout.stripe.com/checkout.js" class="stripe-button"
							data-key="pk_live_NJvVMdwtbtGVAyB6orFniC8k"
							data-amount="{{number_format((number_format($total,2,'.','') + number_format($orderDetails->shipping_amnt,2,'.','') + number_format($orderDetails->insurance_amnt,2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00) + ($cartprods['tss']>0?$cartprods['tss']:0.00),2,'.','') - number_format((!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:0.00),2,'.','')),2,'.','')*100}}"
							data-name="The Sweet Spot Club Hire"
							data-description="TSS Clubhire booking Charges."
							data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
							data-email="{{$orderDetails->buyer_email}}"
							data-label="Confirm and proceed to payment"
							data-currency="AUD"
							data-locale="auto">
					</script>
					<input type="hidden" name="amount" value="{{number_format((number_format($total,2,'.','') + number_format($orderDetails->shipping_amnt,2,'.','') + number_format($orderDetails->insurance_amnt,2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00) + ($cartprods['tss']>0?$cartprods['tss']:0.00),2,'.','') - number_format((!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:0.00),2,'.','')),2,'.','')*100}}" />
					<input type="hidden" name="description" value="TSS Clubhire booking Charges." />
				</form>
			</div>
		<hr/>
	</div>
	@include('includes.frontend.popupMessage')
	{{--<div class="modal fade" id="stripemodal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						&times;
					</button>
				</div>
				<div class="modal-body">

					<div class="row">
						<div class="col-md-12 text-center">
							<form action="{{ URL::to('thankyou')}}" method="POST">
								<script
										src="https://checkout.stripe.com/checkout.js" class="stripe-button"
										data-key="pk_test_GgDsO4jfJYgodWomCEg6TQYg"
										data-amount="{{number_format((number_format($total,2,'.','') + number_format($orderDetails->shipping_amnt,2,'.','') + number_format($orderDetails->insurance_amnt,2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00) + ($cartprods['tss']>0?$cartprods['tss']:0.00),2,'.','') - number_format((!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:0.00),2,'.','')),2,'.','')*100}}"
										data-name="The Sweet Spot Club Hire"
										data-description="TSS Clubhire booking Charges."
										data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
										data-email="{{$orderDetails->buyer_email}}"
										data-label="Confirm and proceed to payment"
										data-locale="auto">
								</script>
								<input type="hidden" name="amount" value="{{number_format((number_format($total,2,'.','') + number_format($orderDetails->shipping_amnt,2,'.','') + number_format($orderDetails->insurance_amnt,2,'.','') - number_format($cartprods['Discount'] + ($cartprods['partnerDiscount']>0?$cartprods['partnerDiscount']:0.00) + ($cartprods['tss']>0?$cartprods['tss']:0.00),2,'.','') - number_format((!empty($orderDetails->offer_Code)?$orderDetails->offer_amnt:0.00),2,'.','')),2,'.','')*100}}" />
								<input type="hidden" name="description" value="TSS Clubhire booking Charges." />
							</form>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info frontend-primary-btn" data-dismiss="modal">Close
					</button>
				</div>
			</div>

		</div>
	</div>--}}
</section>
@endsection