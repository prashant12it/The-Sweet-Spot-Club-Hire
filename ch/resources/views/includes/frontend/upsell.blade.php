
<div class="container-fluid">
	<div class="col-sm-8 col-md-9">

		<div class="row">
			@if(!empty($TotalUpsellProdsArr))
			@foreach($TotalUpsellProdsArr as $Upkey => $UpSellprodsArr)
			@if(!empty($UpSellprodsArr))
			@foreach($UpSellprodsArr as $UpSellProd)
			<div class="col-xs-12 col-sm-4 col-md-4">
				<div class="product-panel clearfix">
					<h3>{{$UpSellProd->name}}</h3>
					<!--<ul>
						<li><a href="#">Promotions</a></li>
						<li><a href="#">Delivery</a></li>
						<li><a href="#">Set Inclusions</a></li>
					</ul>-->
					<div class="row">
						<div class="col-xs-12">
							<img src="/product_img/{{(!empty($UpSellProd->feat_img)?$UpSellProd->feat_img:'commingsoon.png')}}" alt=""/>
                            <span id="hid-desc-{{$UpSellProd->id}}" class="hidden-desc">{{strip_tags($UpSellProd->description)}}</span>
						</div>
						<!--<div class="col-xs-7">

							<p>{{str_limit($UpSellProd->description,200)}}</p>
						</div>-->
					</div>
                    <div class="clearfix upsprod">
                        <a href="javascript:void(0);" onclick="showProdDets('{{$UpSellProd->name}}','{{$UpSellProd->id}}','1','{{(!empty($UpSellProd->feat_img)?$UpSellProd->feat_img:'commingsoon.png')}}');"><h3>
                                View More
                            </h3>
                        </a>
                    </div>
                    <div class="clearfix upsprod">
                        <a href="javascript:void(0);"><h3>
                                ${{$UpSellProd->price}}
                            </h3>
                        </a>
                    </div>
					<div class="bottom-btn clearfix">
						<div class="btn-group number-spinner">
										<span class="input-prepend data-dwn">
											<button class="btn btn-default qty-decrease left-btn glyphicon glyphicon-play"
											        data-dir="dwn" onclick="quantityCounter('qty-decrease','qty-{{$UpSellProd->id}}');"></button>
										</span>
							<input type="text" name="quantity-{{$UpSellProd->id}}" id="quantity-{{$UpSellProd->id}}" class="form-control qty-{{$UpSellProd->id}} input-box text-center" value="1" min="1"
							       max="{{$UpSellProd->quantity}}" style="max-width:100px;">
							<span class="input-append data-up">
											<button class="btn btn-default qty-increase glyphicon glyphicon-play"
											        data-dir="up" onclick="quantityCounter('qty-increase','qty-{{$UpSellProd->id}}');"></button>
										</span>
						</div>
						<input type="hidden" name="product-{{$UpSellProd->id}}[]" value="{{$UpSellProd->id}}" />
						<button class="add-cart" onclick="addToCart('0','product-{{$UpSellProd->id}}','quantity-{{$UpSellProd->id}}');">Add to Cart</button>
					</div>
				</div>
			</div>
			@endforeach
			@endif
			@endforeach
			@endif

		</div>
	</div>
</div>