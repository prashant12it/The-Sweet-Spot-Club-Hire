@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
	<div class="topbar-left">
		<ol class="breadcrumb">
			<li class="crumb-active">
				<a href="{{url('/view_products')}}"><span class="glyphicon glyphicon-book"></span> Golf Sets</a>
			</li>
		</ol>
	</div>
</header>
<section id="content">
	<div class="panel">
		<div class="panel-heading">
			<div class="col-lg-12 margin-tb">
				<div class="pull-left">
					<h2>Golf Sets</h2>
				</div>
				<div class="pull-right">
                    <a class="btn btn-success" href="{{url('/view_products')}}"> Back</a>
					<a class="btn btn-success" href="{{url('/add_sets/'.$prodid)}}"> Add New Set</a>
				</div>
			</div>
		</div>
		<div class="panel-body">
			@if ($message = Session::get('success'))
			<div class="alert alert-success">
				<p>{{ $message }}</p>
			</div>
			@endif

			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th>Sr.</th>
						<th>Set</th>
						<th>SKU</th>
						<th>Attributes</th>
						<th width="280px">Action</th>
					</tr>
					@if (!empty($data))
					@foreach ($data as $key => $result)
                    <td>{{++$i}}.</td>
                    <td>{{ $result['name']}}</td>
                    <td>{{ $result['sku']}}</td>
                    <td>{{ $result['attribs']}}</td>

                    <td>

                        <a class="btn btn-primary" title="Edit product details" href="{{url('/edit_set/'.$result['productid'].'/'.$prodid)}}">
                            <i class="fa fa-edit"></i> </a>
						<a id="act-btn-{{$result['productid']}}" class="btn {{ $result['disable']=='1'?'btn-warning':'btn-success'}}" title="{{ $result['disable']=='1'?'Enable':'Disable'}}" href="javascript:void(0);"  onclick="enableDisableProd('{{$result['productid']}}','0');">
							<i class="fa fa-power-off"></i></a>
						<input type="hidden" id="{{'prod_'.$result['productid'].'_flag'}}" name="{{'prod_'.$result['productid'].'_flag'}}" value="{{ $result['disable']=='1'?'0':'1'}}" />
                        <a class="btn btn-danger" title="Delete set" href="javascript:void(0);"  onclick="checkfn({{$result['productid']}});">
                            <i class="fa fa-trash-o"></i></a>

                    </td>
                    </tr>
					@endforeach
					@else
					<tr>
						<td colspan="7">No set found.</td>
					</tr>
					@endif
				</table>
			</div>
		</div>
		<div class="panel-footer clearfix">

		</div>
	</div>
	<div id="popupbs">
		<div class="modal fade" id="deleteConfirm" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<form method="POST" action="{{ url('/delete_set') }}" class="form-horizontal" role="form" >
						{{ csrf_field() }}
						<input type="hidden" name="delProdid" value="" id="delProdid" />
                        <input type="hidden" name="productid" value="{{$prodid}}" id="productid" />
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Delete set</h4>
						</div>
						<div class="modal-body">
							<p>Are you sure you want to delete this set?</p>
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-danger">Yes</button>
							<button type="button" class="btn btn-info" data-dismiss="modal">No</button>
						</div>
					</form>
				</div>

			</div>
		</div>

	</div>
</section>
@endsection