@extends('layouts.dashboard')

@section('content')

<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/view_products')}}"><span class="glyphicon glyphicon-book"></span> Products</a>
            </li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">
        <div class="panel-heading">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Products</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{url('/add_product')}}"> Add New Product</a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            <form method="POST" enctype="multipart/form-data" action="{{ url('/search_inventory') }}" class="form-horizontal" role="form" id="account2">
                {{ csrf_field() }}
                @if ($attributesAry->count() > 0)
                <div class="panel panel-widget calendar-widget calendar-alt" id="p02">
                    <div class="panel-heading ui-sortable-handle">
                        <span class="panel-icon">
                            <i class="fa fa-search"></i>
                        </span>
                        <span class="panel-title"> Search</span>
                    </div>
                    <div class="panel-body bg-white p15">
                        <div class="row" style="padding-top: 0px;">
                            <?php
                            $iDivCount = 1;
                            $searchAttribute = array();
                            
                            if(session('searchAttribute')){
                                $searchAttribute = session('searchAttribute');
                            }
                            foreach($attributesAry as $iKey=>$attributeData){
                            ?>
                            <div class="col-lg-3  control-label">
                                    <!--<label for="attributes" class="control-label  pull-left"><h4><?php echo $attributeData->attrib_name;?></h4></label>-->
                                    <?php
                                    if(!empty($attributesOptions[$attributeData->id])){
                                    ?>
                                        <select class='select2-single form-control pull-left' name="searchAttribute[<?php echo $iKey;?>]">
                                            <option value=""><?php echo "Please select ".$attributeData->attrib_name;?></option>
                                            <?php
                                            foreach ($attributesOptions[$attributeData->id] as $options){
                                                ?>
                                            <option value="<?php echo $options->id;?>" <?php if(in_array($options->id, $searchAttribute)){?> selected="selected"<?php }?>><?php echo $options->value;?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    <?php
                                    }
                            ?>
                            </div>
                            <?php 
                                if($iDivCount == 4){
                                    $iDivCount = 1;
                                    echo '</div><div class="row">';
                                }
                                else{
                                    $iDivCount = $iDivCount+1;
                                }
                            }?>
                        </div>
                        <div class="clmr"></div>
                        <div class="row">
                            <div class="col-md-9"></div>
                            <div class="col-md-2 pull-right">
                                <button type="submit" class="button form-control btn-info pull-right"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                                <!--<a class="btn btn-info" href="javascript:void(0)"><i class="fa fa-search"></i> Search</a>-->
                            </div>
                            <div class="col-md-1 pull-right">
                                <a class="btn btn-danger" href="/view_products" title="Reset Search"><i class="fa fa-refresh" aria-hidden="true"></i> Reset</a>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
                @endif
            </form>
            <div class="table-responsive"> 
                <table class="table table-striped">
                    <tr>
                        <th>Sr.</th>
                        <th>Product</th>
                        <th>Description</th>
                        <th>SKU</th>
                        <th>Attributes</th>
                        <th>Available Quantity</th>
                        <th>Price($)</th>
                        <th width="280px">Action</th>
                    </tr>
                    @if ($products->count() > 0)
                        @foreach ($products as $product)
                        <tr>
                            <td>{{ ++$i }}.</td>
                            <td>{{ $product->name}}</td>
                            <td>{!! str_limit($product->description,100)!!}</td>
                            <td>{{ $product->sku}}</td>
                            <td>
                                @foreach ($product->attributeAry as $attributeData)
                                    {{$attributeData->attrib_name}} : {{$attributeData->value}}<br/>
                                @endforeach
                            </td>
                            <td>{{ $product->quantity}}</td>
                            <td>${{ $product->price}}</td>
                            <td>
                                <a class="btn btn-info" title="View product details" href="{{ route('productCRUD.show',$product->id) }}">
                                    <i class="fa fa-eye"></i> </a>
                                <a class="btn btn-primary" title="Edit product details" href="{{ route('productCRUD.edit',$product->id) }}">
                                    <i class="fa fa-edit"></i> </a>
                                @if($product->product_type == 4)
                                <a class="btn btn-alert" title="Manage golf sets" href="{{url('/manage_sets/'.$product->id)}}">
                                    <i class="fa fa-gears"></i></a>
                                @endif
                                <a id="act-btn-{{$product->id}}" class="btn {{ $product->disable=='1'?'btn-warning':'btn-success'}}" title="{{ $product->disable=='1'?'Enable':'Disable'}}" href="javascript:void(0);"  onclick="enableDisableProd('{{$product->id}}','{{($product->product_type == 4?1:0)}}');">
                                    <i class="fa fa-power-off"></i></a>
                                <input type="hidden" id="{{'prod_'.$product->id.'_flag'}}" name="{{'prod_'.$product->id.'_flag'}}" value="{{ $product->disable=='1'?'0':'1'}}" />
                                <a class="btn btn-danger" title="Delete product" href="javascript:void(0);" onclick="checkfn({{$product->id}});">
                                    <i class="fa fa-trash-o"></i></a>

                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">No Product found.</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="panel-footer clearfix">
            {!! $products->render() !!}
        </div>
    </div>
    <div id="popupbs">
        <div class="modal fade" id="deleteConfirm" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <form method="POST" action="{{ url('/delete_product') }}" class="form-horizontal" role="form" >
                        {{ csrf_field() }}
                        <input type="hidden" name="delProdid" value="" id="delProdid" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Delete Product</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this product?</p>
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