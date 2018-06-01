@extends('layouts.dashboard')

@section('content')


<header id="topbar" class="alt">
    <div class="topbar-left">
        <ol class="breadcrumb">
            <li class="crumb-active">
                <a href="{{url('/view_products')}}"><span class="glyphicon glyphicon-book"></span> Products</a>
            </li>
            <li class="crumb-trail">Product Details</li>
        </ol>
    </div>
</header>
<section id="content">
  <div class="panel">
  <div class="panel-heading">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Product Details</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{url('/view_products')}}"> Back</a>
            </div>
        </div>
    </div>
    <div class="panel-body">
    <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Product category:</strong>
                {{ $categories->name}}
            </div>
        </div>
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Product type:</strong>
                {{ $ProductType}}
            </div>
        </div>
    <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Product name:</strong>
                {{ $product->name}}
            </div>
        </div>
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>About product:</strong>
                {!! $product->description!!}
            </div>
        </div>
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Product SKU:</strong>
                {{ $product->sku}}
            </div>
        </div>
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Available quantity:</strong>
                {{ $product->quantity}}
            </div>
        </div>
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Unit price:</strong>
                ${{ $product->price}}
            </div>
        </div>
        @if($product->sale == 1)
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Sale price:</strong>
                ${{ $product->sale_price}}
            </div>
        </div>
        @endif
        @if($product->rent == 1)
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Rent:</strong>
                ${{ $product->rent_price}}
            </div>
        </div>
        @endif
        @if($product->product_type == 3)
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <p><strong>Grouped products:</strong></p>
                @if($GroupProds)
                @foreach($GroupProds as $key => $Prods)
                <p>{{++$key}}. {{$Prods->name}}</p>
                @endforeach
                @endif
            </div>
        </div>
        @endif
        @if($product->is_upsell_product == 1)
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <p><strong>Upsell products:</strong></p>
                @if($UpsellProds)
                @foreach($UpsellProds as $key => $Prods)
                <p>{{++$key}}. {{$Prods->name}}</p>
                @endforeach
                @endif
            </div>
        </div>
        @endif

        <div class="col-xs-6 col-sm-12 col-md-6">
            <p><strong>Product featured image:</strong></p>
            <div class="form-group">
                <img src="/product_img/{{$product->feat_img}}" width="200" /></div>
        </div>

        @if(!empty($ProdGalleryArr))
        <div class="col-xs-6 col-sm-12 col-md-6">
            <p><strong>Product images:</strong></p>
        @foreach($ProdGalleryArr as $key => $img)
        <div class="col-lg-6" style="padding-top: 10px">
            <img class="col-sm-12" id="preview" src="/shop/public/{{Storage::url($img[1])}}" />
            <div class="col-lg-12">
                <input type="text" id="gal-caption-{{$img[0]}}" onblur="updateCaption({{$img[0]}})" name="gal-caption-{{$key}}" value="{{(!empty($img[2])?$img[2]:'')}}" placeholder="Caption" class="form-control" />
            </div>
        </div>
        @endforeach
        </div>
        @endif

        @if(!empty($embedVideoUrl))
        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                {!! $embedVideoUrl !!}
            </div>
        </div>
        @endif

        <div class="col-xs-6 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Attributes:</strong>
            </div>
            @if($product->product_type == 4)
            @if(!empty($SetTypeAttribsArr))
            <div class="form-group">
            @foreach($SetTypeAttribsArr as $key => $Attrib)
            <p>{{++$key}}. {{$Attrib->attrib_name}}</p>
            @endforeach
            </div>
            @endif
            @else
            @if (count($productAttributes) > 0)
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="panel" id="p18">
                        <div class="panel-body pn">
                            <table class="table mbn tc-med-1 tc-bold-2">
                                <thead>
                                <tr class="hidden">
                                    <th>#</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($productAttributes as $attributeData)
                                <tr>
                                    <td>
                                        <strong>
                                            <span class="va-t mr10"></span>{{$attributeData->attrib_name}} : {{$attributeData->value}}
                                        </strong>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <p>No attribute found.</p>
            @endif
            @endif
        </div>
        </div>
        <div class="panel-footer clearfix">
            <div class="col-lg-12">
                <div class="pull-left">
                    <a class="btn btn-success" href="{{url('/view_products')}}"> Back</a>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{url('/add_product')}}"> Add New Product</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection