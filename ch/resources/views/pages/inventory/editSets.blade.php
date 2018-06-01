@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
	<div class="topbar-left">
		<ol class="breadcrumb">
			<li class="crumb-active">
				<a href="{{url('/dashboard')}}">Dashboard</a>
			</li>
			<li class="crumb-icon">
				<a href="{{url('/dashboard')}}">
					<span class="glyphicon glyphicon-home"></span>
				</a>
			</li>
			<li class="crumb-link">
				<a href="{{url('/dashboard')}}">Home</a>
			</li>
			<li class="crumb-trail">Edit Golf Set</li>
		</ol>
	</div>
</header>
<section id="content">
	<div class="panel">

		<div class="panel-heading">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Edit Golf Set</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{url('/manage_sets/'.$parentid)}}"> Back</a>
                </div>
            </div>
		</div>
		<div class="panel-body">
			@if ($message = Session::get('success'))
			<div class="alert alert-success">
				<p>{{ $message }}</p>
			</div>
			@endif
			<form method="POST" enctype="multipart/form-data" action="{{ url('/edit_set') }}" class="form-horizontal" role="form" id="addProductForm">

				<div class="panel-body p25 bg-light">
					<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
					<input type="hidden" name="parent_productid" id="parent_productid" value="" />
					<div class="section row form-group">
						<label for="name" class="col-lg-4 control-label">Set title</label>
						<div class="col-lg-6">
							<input type="text" name="name" id="name" value="{{ ( old("name") ? old("name"):$product->name) }}" class="gui-input form-control" placeholder="Enter set title..." required="required">
							@if ($errors->has('name'))
							<span class="help-block err">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
							@endif
						</div>
						<!-- end section -->
					</div>
					<!-- end .section row section -->

					<div class="section row form-group">
						<label for="description" class="col-lg-4 control-label">SKU</label>
						<div class="col-lg-6">
							<input type="text" readonly="readonly" name="sku" id="sku" value="{{ (old("sku") ? old("sku"):$product->sku) }}" class="gui-input form-control" placeholder="Set sku..." required="required">

							@if ($errors->has('sku'))
							<span class="help-block err">
                                <strong>{{ $errors->first('sku') }}</strong>
                            </span>
							@endif
						</div>
					</div>

					<div class="section row form-group">
						<label for="product_type" class="col-lg-4 control-label">Set attributes value</label>
						<div class="col-lg-6">
							@if(!empty($AttribsArr))
							@foreach($AttribsArr as $key => $value)
							@if(!empty($value))

							@foreach($value as $Attrkey => $Attrvalue)
							@if(!empty($Attrvalue))

							@if($Attrkey == 'attrib_name')

							<div class="col-lg-6 control-label">
								<strong>{{$Attrvalue}}</strong>
							</div>
							@endif
							@if($Attrkey == 'attrib_vals')

							<div class="col-lg-6"  style="margin-bottom:15px">
								<select name="attribvals{{++$counter}}" id="attribvals{{$counter}}" class="form-control">
									<option value="">Select attribute value</option>
									@foreach ($Attrvalue as $valsKey => $attribOpt)
                                    <?php $selected = ''; ?>
                                    @if(!empty($AttribValsArr))
                                    @foreach($AttribValsArr as $Akey => $Avalue)
                                    @if($attribOpt->id == $Avalue->id)
									<?php $selected = 'selected="selected"';?>
                                    @endif
                                    @endforeach
                                    @endif
									<option value="{{$attribOpt->id}}" {{$selected}} {{ (old("attribvals") == $attribOpt->id ? "selected":"") }}>{{$attribOpt->value}}</option>
									@endforeach
								</select>

							</div>
							@endif

							@endif
							@endforeach
							<input type="hidden" name="totalattribs" value="{{$counter}}" />
							<input type="hidden" name="productid" value="{{$prodid}}" />
							@endif
							@endforeach
							@endif

						</div>

						<!-- end section -->
					</div>

				</div>


		</div>
		<!-- end .form-body section -->
		<div class="panel-footer clearfix">
			<div class="col-lg-8"></div>
			<div class="col-lg-2">
				<button type="submit" class="button form-control btn-primary pull-right">Update set</button>
			</div>
			<div class="col-lg-2"></div>
		</div>
		<!-- end .form-footer section -->
		</form>
	</div>

	</div>

	<div class="panel-footer clearfix">

	</div>
	</form>
	</div>

	</div>
</section>
@endsection