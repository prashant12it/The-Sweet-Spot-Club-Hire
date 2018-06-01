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
            <li class="crumb-trail">Edit Product</li>
        </ol>
    </div>
</header>
<section id="content">
    <div class="panel">

        <div class="panel-heading">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Edit Product</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="{{url('/view_products')}}"> Back</a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
            @endif
            <form method="POST" enctype="multipart/form-data" action="{{ url('/update_product') }}" class="form-horizontal" role="form" id="editProductForm">
                {{ csrf_field() }}
                <div class="panel-body p25 bg-light">
                    <div class="section row form-group">
                        <input type="hidden" name="prodid" id="prodid" value="{{$product->id}}" />
                        <label for="category" class="col-lg-4 control-label">Product category</label>
                        <div class="col-lg-6">
                            <select name="category" id="product_category" class="form-control" onchange="enableDisableFields('editProductForm');">
                                <option value="">Select category</option>
                                @foreach ($categories as $category)

                                <option value="{!!$category['id']!!}" {{ (old("category") == $category['id'] ? "selected":($product->category == $category['id'] ?"selected":"")) }}>{!!$category['name']!!}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('category'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('category') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="product_type" class="col-lg-4 control-label">Product type</label>
                        <div class="col-lg-6">
                            <select name="product_type" id="product_type" class="form-control" disabled="disabled" onchange="autoConfigureProducts();">
                                <option value="">Select product type</option>
                                @foreach ($prodType as $key => $type)

                                <option value="{!!$key!!}" {{ (old("product_type") == $key ? "selected":($product->product_type == $key ?"selected":"")) }}>{!!$type!!}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('product_type'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('product_type') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!-- end section -->
                    </div>
                    <div class="section row form-group">
                        <label for="name" class="col-lg-4 control-label">Product title</label>
                        <div class="col-lg-6">
                            <input type="text" disabled="disabled"name="name" id="name" value="{{ (old("name")?old("name"):$product->name)}}" class="gui-input form-control" placeholder="Enter product title...">
                            <input type="hidden" name="id" value="{{$product->id}}" />
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
                        <label for="description" class="col-lg-4 control-label">About product</label>
                        <div class="col-lg-6">
<!--                            <input type="text" disabled="disabled" name="description" id="description" value="{{(old("description")?old("description"):$product->description)}}" class="gui-input form-control" placeholder="About product...">-->
                            <textarea name="description" id="description" >{!!(old("description")?old("description"):$product->description)!!}</textarea>
                            @if ($errors->has('description'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="section row form-group">
                        <label for="prod_video" class="col-lg-4 control-label">Product video url</label>
                        <div class="col-lg-6">
                            <input type="text" disabled="disabled" name="prod_video" id="prod_video" value="{{(old("prod_video")?old("prod_video"):$product->prod_video)}}" class="gui-input form-control" placeholder="Product description video url...">

                            @if ($errors->has('prod_video'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('prod_video') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- end section -->


                    <div class="section row form-group">
                        <label for="sku" class="col-lg-4 control-label">Product SKU</label>
                        <div class="col-lg-6">
                            <input type="text" disabled="disabled" name="sku" id="sku" readonly="readonly" class="gui-input form-control" value="{{(old("sku")?old("sku"):$product->sku)}}" placeholder="Product SKU...">
                            @if ($errors->has('sku'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('sku') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!-- end section -->
                    </div>

                    <div class="section row form-group">
                        <label for="quantity" class="col-lg-4 control-label">Product quantity</label>
                        <div class="col-lg-6">
                            <input type="number" disabled="disabled" step="1" min="1" name="quantity" id="quantity" class="gui-input form-control" value="{{(old("quantity")?old("quantity"):$product->quantity)}}" placeholder="Product quantity...">
                            @if ($errors->has('quantity'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('quantity') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!-- end section -->
                    </div>

                    <div class="section row form-group">
                        <label for="price" class="col-lg-4 control-label">Product price</label>
                        <div class="col-lg-6">
                            <input type="number" onblur="setMaxLimit(this)" disabled="disabled" step="0.01" min="0.01" name="price" id="price" class="gui-input form-control" value="{{(old("price")?old("price"):$product->price)}}" placeholder="Product price...">
                            @if ($errors->has('price'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="section row form-group sale-sec">
                        <div class="col-lg-4"></div>
                        <div class="checkbox-custom mb5 control-check col-lg-2">
                            <input id="sale" onclick="checkOptionChecked(this,'sale_price','rent-sec');" {{(old("sale") == '1' || $product->sale == '1'?"checked":"")}} value="1" name="sale" type="checkbox">
                            <label for="sale">Sale product</label>
                        </div>
                        <label for="sale_price" class="col-lg-1 control-label">Sale price</label>
                        <div class="col-lg-3">
                            <input type="number" disabled="disabled" readonly="readonly" step="0.01" min="0.01" name="sale_price" id="sale_price" class="gui-input form-control" value="{{(old("sale_price")?old("sale_price"):$product->sale_price)}}" placeholder="Product sale price...">
                            @if ($errors->has('sale_price'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('sale_price') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="section row form-group rent-sec">
                        <div class="col-lg-4"></div>
                        <div class="checkbox-custom mb5 control-check col-lg-2">
                            <input id="rent" onclick="checkOptionChecked(this,'rent_price','sale-sec');" {{(old("rent") == '1' || $product->rent == '1'?"checked":"")}} value="1" name="rent" type="checkbox">
                            <label for="rent">Rental product</label>
                        </div>
                        <label for="rent" class="col-lg-1 control-label">Rent</label>
                        <div class="col-lg-3">
                            <input type="number" disabled="disabled" readonly="readonly" step="0.01" min="0.01" name="rent_price" id="rent_price" class="gui-input form-control" value="{{(old("rent_price")?old("rent_price"):$product->rent_price)}}" placeholder="Product rent price...">
                            @if ($errors->has('rent_price'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('rent_price') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- end section -->
                    <div class="section row form-group">
                        <label for="feat_img" class="col-lg-4 control-label">Product featured image</label>
                        <div class="col-lg-6  control-label">
                            <input data-preview="#preview" disabled="disabled" name="feat_img" type="file" class="gui-input" id="feat_img"><br />
                            <img class="col-sm-6" id="preview" src="/product_img/{{$product->feat_img}}" width="100" />
                            <input type="hidden" name="old_prod_img" value="{{$product->feat_img}}">
                        </div>
                    </div>
                    <div class="section row form-group">
                        <label for="gallery_img" class="col-lg-4 control-label">Product images</label>
                        <div class="col-lg-6  control-label">
                            <input data-preview="#preview" disabled="disabled" name="gallery_img[]" type="file" multiple class="gui-input" id="feat_img">
                            @if ($errors->has('gallery_img'))
                            <span class="help-block err">
                                <strong>{!!$errors->first('gallery_img')!!}</strong>
                            </span>
                            @endif
                            <div class="col-lg-12">
                                @if(!empty($ProdGalleryArr))
                                @foreach($ProdGalleryArr as $key => $img)
                                <div class="col-lg-4" style="padding-top: 10px">
                                    <img class="col-sm-12" id="preview" src="/shop/public/{{Storage::url($img[1])}}" />
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="section row form-group group-prod-sec">
                        <label for="group_products" class="col-lg-4 control-label">Group Products</label>
                        <div class="col-lg-6">
                            <select name="group_products[]" id="group_products" class="select2-multiple form-control select-primary" multiple="multiple">

                            </select>
                            @if ($errors->has('group_products'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('group_products') }}</strong>
                            </span>
                            @endif
                        </div>
                        <script type="text/javascript">
                                    @if ($SelectedProds)
                                    var valArr = [];
                                    @foreach ($SelectedProds as $prod)
                                    valArr.push({{$prod}});
                                    @endforeach
                                    @endif
                                    @if (old("group_products"))
                                    var valArr = [];
                                    @foreach (old("group_products") as $prod)
                                    valArr.push({{$prod}});
                                    @endforeach
                                    @endif
                        </script>

                    </div>

                    <div class="section row form-group">
                        <div class="col-lg-4"></div>
                        <div class="checkbox-custom mb5 control-check col-lg-2">
                            <input id="is_upsell_product" onclick="showUpsellProductsList();" {{(old("is_upsell_product") == '1' || $product->is_upsell_product == '1'?"checked":"")}} value="1" name="is_upsell_product" type="checkbox">
                            <label for="is_upsell_product">Upsell product</label>
                        </div>
                    </div>

                    <div class="section row form-group" id="upsell_products_parent_div" {{ ((old("is_upsell_product")?old("is_upsell_product"):$product->is_upsell_product) != 1 ? "style=display:none;":"") }} >
                        <label for="upsell_products" class="col-lg-4 control-label">Products</label>
                        <div class="col-lg-6" style="padding-top:0px;">
                            <?php
                            $upsellProductsAry = session('upsell_products');
                            ?>
                            <select class='select2-multiple upsellProOpt form-control select-primary pull-left' multiple="multiple" name="upsell_products[]" id="upsell_products">

                                <?php
                                if (!empty($productsAry)) {
                                    foreach ($productsAry as $productData) {
                                        $selected = false;
                                        if (!empty($upsellProductsAry)) {
                                            foreach ($upsellProductsAry as $upsellProduct) {
                                                if ($upsellProduct == $productData->id) {
                                                    $selected = true;
                                                }
                                            }
                                        }
                                        ?>
                                        <option value="<?php echo  $productData->id; ?>"  <?php if ($selected == true) { ?> selected="selected"<?php } ?>><?php echo  $productData->name; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            @if ($errors->has('upsell_products'))
                            <span class="help-block err">
                                <strong>{{ $errors->first('upsell_products') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="section row attribsec form-group simpleattribs">
                        <label for="attributes" class="col-lg-4 control-label">Product Attributes</label>
                        <div class="col-lg-6  control-label" style="padding-top:0px;">
                            <?php
                            $iCount = 0;
                            $idAttribute = '';
                            $attributesAry = session('attributesAry');
                            $attributesOptions = session('attributesOptions');
                            $postAttributesAry = session('postAttributesAry');
                            $postAttributesOptions = session('postAttributesOptions');
                            //                                    print_R($postAttributesOptions);die;
                            if (!empty($postAttributesAry)) {
                                foreach ($postAttributesAry as $key => $attributeValue) {
                                    $iCount = $iCount + 1;
                                    ?>
                                    <div id="attributeDiv_<?php echo  $iCount; ?>" class="row">
                                        <div class='col-lg-6'>
                                            <h4 class='pull-left'>Attributes Name <?php echo  $iCount; ?> </h4>
                                            <select class='select2-single proAttr form-control pull-left' name='proAttr[attribName_<?php echo  $iCount; ?>]' id='attribName_<?php echo  $iCount; ?>' onchange="getAttributeOptions('<?php echo  $attributeValue; ?>')">
                                                <?php
                                                foreach ($attributesAry as $attributeData) {
                                                    ?>
                                                    <option value='<?php echo  $attributeData->id; ?>' <?php if ($attributeData->id == $postAttributesAry['attribName_' . $iCount]) { ?> selected="selected"<?php } ?>><?php echo  $attributeData->attrib_name; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-5" id="attributeOptionsDiv_<?php echo  $iCount; ?>" >
                                            <h4 class='pull-left'>Options</h4>
                                            <select class='select2-multiple proAttrOpt form-control select-primary pull-left' multiple="multiple"  name="proAttrOpt[attribOptions_<?php echo  $iCount; ?>][]" id="attribOptions_<?php echo  $iCount; ?>">
                                                <?php
                                                if (!empty($attributesOptions[$postAttributesAry['attribName_' . $iCount]])) {
                                                    foreach ($attributesOptions[$postAttributesAry['attribName_' . $iCount]] as $optionData) {
                                                        $selected = false;
                                                        if (!empty($postAttributesOptions['attribOptions_' . $iCount])) {
                                                            foreach ($postAttributesOptions['attribOptions_' . $iCount] as $selectedOptions) {
                                                                if ($selectedOptions == $optionData->id) {
                                                                    $selected = true;
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <option value="<?php echo  $optionData->id; ?>"  <?php if ($selected == true) { ?> selected="selected"<?php } ?>><?php echo  $optionData->value; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                    <h2></h2>
                                    <?php
                                }
                                ?>
                                <div id="attributeDiv_<?php echo  $iCount + 1; ?>" class="row">
                                    <div class="col-lg-4">
                                        <a href="javascript:void(0)" class="button form-control btn-dark" onclick="addProAttribute('<?php echo  $iCount + 1; ?>')"><i class="fa fa-plus"></i> Add Attribute</a>
                                    </div>
                                </div>
    <?php } else {
    ?>
                                <div id="attributeDiv_1" class="row">
                                    <div class="col-lg-4">
                                        <a href="javascript:void(0)" class="button form-control btn-dark" onclick="addProAttribute('1')"><i class="fa fa-plus"></i> Add Attribute</a>
                                    </div>
                                </div>
<?php } ?>
                        </div>
                    </div>

                <div class="section row form-group attribsec parentattribs">
                    <label for="attributes" class="col-lg-4 control-label">Product Attributes</label>

                    @if(old('totalattribs'))
                    @for($i = 1; $i <= old("totalattribs"); $i++)
                    <script>
                        setTimeout(function () {
                            addParentAttribute({{old('attribNo').$i}},{{$i}});
                        },1500);
                    </script>
                    @endfor
                    @elseif(!empty($SetTypeAttribsArr))
                    @foreach($SetTypeAttribsArr as $key => $attrib)
                    <script>
                        setTimeout(function () {
                            addParentAttribute({{$attrib->attrib_id}},{{++$key}});
                        },1500);
                    </script>
                    @endforeach
                    @endif
                    <div class="col-lg-6" style="padding-top: 0px;">

                        <div id="addAttribs" class="row">
                            <div class="col-lg-4">
                                <input type="hidden" name="totalattribs" id="totalattribs" value="{{ (old("totalattribs")? old("totalattribs"):(!empty($SetTypeAttribsArr)?count($SetTypeAttribsArr):"0")) }}" />
                                <a href="javascript:void(0)" class="button form-control btn-dark" onclick="addParentAttribute()"><i class="fa fa-plus"></i> Add Attribute</a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
        </div>
        <!-- end .form-body section -->
        <div class="panel-footer clearfix">
            <div class="col-lg-8"></div>
            <div class="col-lg-2">
                <button type="submit" disabled="disabled" class="button form-control btn-primary pull-right">Update product</button>
            </div>
            <div class="col-lg-2"></div>
        </div>
        <!-- end .form-footer section -->
        </form>
    </div>

    <script src="../../vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
</section>
@endsection