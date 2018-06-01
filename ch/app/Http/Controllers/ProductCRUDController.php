<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Category;
use DB;
use Config;
use View;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProductCRUDController extends Controller {
	protected $layout = 'layouts.dashboard';
	public function __construct() {
		$this->DBTables = Config::get( 'constants.DbTables' );
	}

	public function index( Request $request ) {
		View::share( 'title', 'View products' );
		session()->flash( 'searchAttribute', null );
		$attributesAry     = $attributesAry = DB::table( 'attributes' )->orderBy( 'attrib_name', 'ASC' )->get();
		$attributesOptions = array();
		if ( ! is_null( $attributesAry ) ) {
			foreach ( $attributesAry as $attributesData ) {
				$attributesOptions[ $attributesData->id ] = DB::table( 'attribute_vals' )->where( 'attrib_id', '=', $attributesData->id )->orderBy( 'value', 'ASC' )->get();
			}
		}

		$rowsPerPage = Config::get( 'constants.PaginationRowsPerPage' );
		$products    = Product::where( 'product_type', '!=', 5 )->orderBy( 'id', 'DESC' )->paginate( $rowsPerPage );

		if ( ! empty( $products ) ) {
			foreach ( $products as $key => $productData ) {
				$products[ $key ]->attributeAry = $this->getProductAttribute( $productData->id );
			}
		}

//        dd($products);die;
		return view( 'pages.inventory.viewproducts', compact( 'products' ) )
			->with( 'attributesAry', $attributesAry )
			->with( 'attributesOptions', $attributesOptions )
			->with( 'i', ( $request->input( 'page', 1 ) - 1 ) * $rowsPerPage );
	}

	public function getProductAttribute( $idProduct = 0 ) {
		$savedAttributes = array();
		if ( $idProduct > 0 ) {
//            DB::enableQueryLog();
			$savedAttributes = DB::table( 'product_attrib_map' )
			                     ->join( 'attribute_vals', 'attribute_vals.id', '=', 'product_attrib_map.attrib_val_id' )
			                     ->join( 'attributes', 'attributes.id', '=', 'attribute_vals.attrib_id' )
			                     ->select( 'product_attrib_map.attrib_val_id', 'attribute_vals.value', 'attributes.attrib_name', 'attributes.id' )->where( 'product_attrib_map.prod_id', '=', $idProduct )
			                     ->orderBy( 'product_attrib_map.id', 'ASC' )
			                     ->get();
//            dd(DB::getQueryLog());die;
		}

		return $savedAttributes;
	}

	public function create() {
		View::share( 'title', 'Add new product' );
		View::share( 'scriptName', 'Add multiselect script' );
		$prodType   = Config::get( 'constants.productType' );
		$categories = Category::all();
		$products   = DB::table( 'products' )->select( 'id', 'name' )->orderBy( 'name', 'ASC' )->get();

		return view( 'pages.inventory.addProduct', compact( 'prodType', 'categories', 'products' ) );
	}

	public function store( Request $request ) {
		if ( $request->hasFile( 'feat_img' ) ) {
			$imageTempName = $request->feat_img->getPathname();
			$imageName     = time() . '.' . $request->feat_img->getClientOriginalExtension();
			$request->feat_img->move( public_path( '../../product_img' ), $imageName );
		} else {
			$imageName = 'comingsoon.png';
		}
		Input::merge( array_map( 'trim', Input::except( 'group_products', 'proAttrOpt', 'proAttr', 'upsell_products', 'gallery_img' ) ) );

		$allInput    = $request->input();
		$prodType    = $allInput['product_type'];
		$rules       = array(
			'name'         => 'required | max:255 | regex:/(^[A-Za-z0-9 ]+$)+/',
			'description'  => 'required',
			'prod_video'   => 'active_url',
			'sku'          => 'required | max:255 | alpha_num',
			'quantity'     => 'required | integer | min:1',
			'price'        => 'required|numeric|between:0.01,9999999.99',
			'category'     => 'required | integer | min:1',
			'product_type' => 'required | integer | min:1',
		);
		$files       = Input::file( 'gallery_img' );
		$file_count  = count( $files );
		$uploadcount = 0;
		$prodsale    = $request->sale;
		$rentalProd  = $request->rent;
		if ( $prodsale == 1 ) {
			$rules['sale_price'] = 'required|numeric|between:0.01,9999999.99';
		} else {
			$request->sale_price = 0;
		}
		if ( $rentalProd == 1 ) {
			$rules['rent_price'] = 'required|numeric|between:0.01,9999999.99';
		} else {
			$request->rent_price = 0;
		}
		if ( $prodType == '3' ) {
			$rules['group_products'] = 'required';
		}

		$upsellProductsAry = array();
		$isUpsellProduct   = (int) $request->is_upsell_product;
		$upsellProductsAry = $request->upsell_products;
		if ( $isUpsellProduct == 1 ) {
			$rules['upsell_products'] = 'required';
		}


		$validator = $this->getValidationFactory()->make( $request->only( 'name', 'description', 'prod_video', 'sku', 'quantity', 'price', 'sale_price', 'rent_price', 'category', 'product_type', 'group_products', 'upsell_products' ), $rules );

		$postAttributesAry     = $request->proAttr;
		$postAttributesOptions = $request->proAttrOpt;

		if ( $validator->fails() ) {

			if ( $request->product_type != 4 ) {
				$attributesAry     = DB::table( 'attributes' )->orderBy( 'attrib_name', 'ASC' )->get();
				$attributesOptions = array();
				if ( ! is_null( $postAttributesAry ) ) {
					foreach ( $postAttributesAry as $attributesData ) {
						$attributesOptions[ $attributesData ] = DB::table( 'attribute_vals' )->where( 'attrib_id', '=', $attributesData )->orderBy( 'value', 'ASC' )->get();
					}
				}

				session()->flash( 'attributesAry', $attributesAry ); // Store it as flash data.
				session()->flash( 'attributesOptions', $attributesOptions );
				session()->flash( 'postAttributesAry', $postAttributesAry );
				session()->flash( 'postAttributesOptions', $postAttributesOptions );
				session()->flash( 'upsell_products', $upsellProductsAry );
			}
			return redirect()->to( $this->getRedirectUrl() )
			                 ->withInput( $request->input() )
			                 ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
		} else {
			if ( ! empty( $request->gallery_img ) ) {
				foreach ( $files as $file ) {
					$rules     = array( 'gallery_img' => 'image|mimes:jpeg,bmp,png|max:2000' ); //'required|mimes:png,gif,jpeg,txt,pdf,doc'
					$messages  = [
						'gallery_img.image' => 'Product images should be images.',
						'gallery_img.mimes' => 'Product images must be in jpeg, bmp, png format',
						'gallery_img.max'   => 'Product images image files must not be greater than 2MB each'
					];
					$validator = $this->getValidationFactory()->make( array( 'gallery_img' => $file ), $rules, $messages );
					if ( $validator->passes() ) {
						$uploadcount ++;
					}
				}
			}
			if ( ( $file_count == 0 ) || ( $file_count > 0 && $uploadcount == $file_count ) ) {
				$productData = Product::create( $request->all() );
				$prodid      = $productData->id;
				if ( $request->hasFile( 'feat_img' ) ) {
					DB::table( 'products' )
					  ->where( 'feat_img', $imageTempName )
					  ->update( [ 'feat_img' => $imageName ] );
				} else {
					DB::table( 'products' )
					  ->where( 'feat_img', null )
					  ->update( [ 'feat_img' => $imageName ] );
				}
				if ( ! empty( $request->gallery_img ) ) {
					foreach ( $request->gallery_img as $photo ) {
						$filename    = $photo->store( 'public/product-gallery' );
						$fileNameArr = explode( '/', $filename );
						DB::table( $this->DBTables['Product_Gallery'] )->insert(
							[ 'product_id' => $prodid, 'gallery_img' => $fileNameArr['1'] . '/' . $fileNameArr['2'] ]
						);
					}
				}
				if ( $prodType == '3' ) {
					$GroupProds = $allInput['group_products'];
					foreach ( $GroupProds as $key => $vals ) {
						DB::table( $this->DBTables['Group_Products'] )->insert(
							[ 'parent_productid' => $prodid, 'product_id' => $vals ]
						);
					}
				}

				if ( (int) $isUpsellProduct == '1' ) {
					if ( ! empty( $upsellProductsAry ) ) {
						foreach ( $upsellProductsAry as $key => $vals ) {
							DB::table( $this->DBTables['Upsell_Products'] )->insert(
								[ 'product_id' => $prodid, 'upsell_prod_id' => $vals ]
							);
						}
					}

				}
				if ( $request->product_type == 4 ) {
					$AttribCounts = $request->totalattribs;
					for ( $i = 1; $i <= $AttribCounts; $i ++ ) {
						DB::table( $this->DBTables['Parent_Product_Attributes'] )->insert(
							[ 'product_id' => $prodid, 'attrib_id' => $allInput['attribNo' . $i] ]
						);
					}
				} else {
					if ( ! is_null( $postAttributesOptions ) ) {
						foreach ( $postAttributesOptions as $attributesOptionsAry ) {
							foreach ( $attributesOptionsAry as $optionId ) {
								$idLastOption = DB::table( 'product_attrib_map' )->insertGetId( [
									'prod_id'       => $prodid,
									'attrib_val_id' => $optionId
								] );
							}
						}
					}
				}

				return redirect()->to( '/view_products' )
				                 ->with( 'success', 'Product added successfully' );
			} else {
//                dd($this->getRedirectUrl($request));
				return redirect()->to( $this->getRedirectUrl() )
				                 ->withInput( $request->input() )
				                 ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
			}
		}
	}

/*public function getRedirectUrl(Request $request){
    return $request->url();
}*/
	public function show( $id ) {
		View::share( 'title', 'View product details' );
		$product           = Product::find( $id );
		$productAttributes = $this->getProductAttribute( $id );
		$categories        = Category::find( $product->category );
		$GroupProds        = array();
		$SetTypeAttribsArr = array();
		if ( $product->product_type == 3 ) {
			$GroupProds = DB::table( $this->DBTables['Group_Products'] )
			                ->join( $this->DBTables['Products'], $this->DBTables['Group_Products'] . '.product_id', '=', $this->DBTables['Products'] . '.id' )
			                ->where( $this->DBTables['Group_Products'] . '.parent_productid', '=', $id )
			                ->select( $this->DBTables['Products'] . '.*' )
			                ->get();
		}

		if ( $product->product_type == 4 ) {
			$SetTypeAttribsArr = DB::table( $this->DBTables['Parent_Product_Attributes'] )
			                       ->join( $this->DBTables['Attributes'], $this->DBTables['Parent_Product_Attributes'] . '.attrib_id', '=', $this->DBTables['Attributes'] . '.id' )
			                       ->where( $this->DBTables['Parent_Product_Attributes'] . '.product_id', '=', $id )
			                       ->get();
		}

		if ( $product->is_upsell_product == 1 ) {
			$UpsellProds = DB::table( $this->DBTables['Upsell_Products'] )
			                 ->join( $this->DBTables['Products'], $this->DBTables['Upsell_Products'] . '.upsell_prod_id', '=', $this->DBTables['Products'] . '.id' )
			                 ->where( $this->DBTables['Upsell_Products'] . '.product_id', '=', $id )
			                 ->select( $this->DBTables['Products'] . '.*' )
			                 ->get();
		}
		$prodType    = Config::get( 'constants.productType' );
		$ProductType = '';
		foreach ( $prodType as $key => $value ) {
			if ( $product->product_type == $key ) {
				$ProductType = $value;
			}
		}
		$ProdGalleryArr = $this->getProductGalleryImgs( $id );
//		dd($ProdGalleryArr);
		$embedVideoUrl  = '';
		if ( ! empty( $product->prod_video ) ) {
			$embedVideoUrl = $this->embedAnyVideoFromUrl( array(
				'url'    => $product->prod_video,
				'width'  => 225,
				'height' => 144
			), "" );
		}

		return view( 'pages.inventory.productDetails', compact( 'product', 'categories', 'ProductType', 'GroupProds', 'productAttributes', 'UpsellProds', 'embedVideoUrl', 'ProdGalleryArr', 'SetTypeAttribsArr' ) );
	}

	public function getProductGalleryImgs( $product_id ) {
		$ProdsGallery   = DB::table( $this->DBTables['Product_Gallery'] )->where( 'product_id', '=', $product_id )->orderBy( 'id', 'ASC' )->get();
		$ProdGalleryArr = array();
		if ( ! empty( $ProdsGallery ) ) {
			foreach ( $ProdsGallery as $ProdsGalleryData ) {


				$ProdGalleryArr[] = array($ProdsGalleryData->id,$ProdsGalleryData->gallery_img,$ProdsGalleryData->caption);
			}

		}

		return $ProdGalleryArr;
	}

	public function embedAnyVideoFromUrl( $attrs, $options ) {
		$url  = trim( $attrs['url'] );
		$type = &$attrs['type'];

		$width = $attrs['width'];
		if ( $width == null ) {
			$width = $options['video_width'];
		}
		$height = $attrs['height'];
		if ( $width == null ) {
			$width = $options['video_height'];
		}

		$buffer = '<div style="clear: both"></div>';

		// Oldish...
		if ( $type != '' ) {
			if ( $type == 'youtube' ) {
				if ( $width == '' ) {
					$width = 425;
				}
				if ( $height == '' ) {
					$height = 350 / 425 * $width;
				}
				$id     = &$attrs['id'];
				$url    = 'http://www.youtube.com/v/' . $id;
				$buffer .= '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="' .
				           $url . '"></param><param name="wmode" value="transparent"></param><embed src="' . $url .
				           '" type="application/x-shockwave-flash" wmode="transparent" width="' . $width . '" height="' .
				           $height . '"></embed></object>';
			} else if ( $type == 'google' ) {
				if ( $width == '' ) {
					$width = 400;
				}
				if ( $height == '' ) {
					$height = 326 / 400 * $width;
				}
				$url    = 'http://video.google.com/googleplayer.swf?docId="google"';
				$buffer .= '<embed style="width:' . $width . 'px; height:' . $height .'px;" id="VideoPlayback" type="application/x-shockwave-flash" src="' .
				           $url . '" flashvars=""></embed>';
			}
		} else {
			if ( strpos( $url, 'metacafe.com' ) !== false ) {
				if ( $width == '' ) {
					$width = 400;
				}
				if ( $height == '' ) {
					$height = 345 / 400 * $width;
				}

				$x   = strpos( $url, '/watch/' );
				$url = 'http://www.metacafe.com/fplayer/' . substr( $url, $x + 7, - 1 ) . '.swf';

				$buffer .= '<embed src="' . $url . '" width="' . $width . '" height="' . $height .
				           '" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" ' .
				           'type="application/x-shockwave-flash"></embed>';
			} else if ( strpos( $url, 'youtube.com' ) !== false ) {
				if ( $width == '' ) {
					$width = 425;
				}
				if ( $height == '' ) {
					$height = 350 / 425 * $width;
				}

				$x   = strpos( $url, 'watch?v=' );
				$url = 'http://www.youtube.com/v/' . substr( $url, $x + 8 );

				$buffer .= '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="' .
				           $url . '"></param><param name="wmode" value="transparent"></param><embed src="' . $url .
				           '" type="application/x-shockwave-flash" wmode="transparent" width="' . $width . '" height="' .
				           $height . '"></embed></object>';
			} else if ( strpos( $url, 'google.com' ) !== false ) {
				if ( $width == '' ) {
					$width = 400;
				}
				if ( $height == '' ) {
					$height = 326 / 400 * $width;
				}

				$x   = strpos( $url, 'docid=' );
				$url = 'http://video.google.com/googleplayer.swf?docId=' . substr( $url, $x + 6 );

				$buffer .= '<embed style="width:' . $width . 'px; height:' . $height .
				           'px;" id="VideoPlayback" type="application/x-shockwave-flash" src="' .
				           $url . '" flashvars=""></embed>';
			} else if ( strpos( $url, 'mashahd.net' ) !== false ) {
				if ( $width == '' ) {
					$width = 425;
				}
				if ( $height == '' ) {
					$height = 350 / 425 * $width;
				}

				$x   = strpos( $url, 'viewkey=' );
				$url = 'http://www.mashahd.net/player/vPlayer.swf?f=http://www.mashahd.net/player/vConfig.php?vkey=' . substr( $url, $x + 8 );

				$buffer .= '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="' .
				           $url . '"></param><param name="wmode" value="transparent"></param><embed src="' . $url .
				           '" type="application/x-shockwave-flash" wmode="transparent" width="' . $width . '" height="' .
				           $height . '" name="main" id="main" allowfullscreen="true" ></embed></object>';
			} else if ( strpos( $url, 'vimeo.com' ) !== false ) {
				if ( $width == '' ) {
					$width = 425;
				}
				if ( $height == '' ) {
					$height = 350 / 425 * $width;
				}

				$x   = strpos( $url, 'vimeo.com/' );
				$url = 'http://vimeo.com/moogaloop.swf?clip_id=' . substr( $url, $x + 10 ) . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1';

				$buffer .= '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="' .
				           $url . '"></param><param name="wmode" value="transparent"></param><embed src="' . $url .
				           '" type="application/x-shockwave-flash" wmode="transparent" width="' . $width . '" height="' .
				           $height . '" name="main" id="main" allowfullscreen="true" ></embed></object>';
			}

		}

		return $buffer . '<div style="clear: both"></div>';
	}

	public function edit( $id ) {
		View::share( 'title', 'Edit product' );
		View::share( 'scriptName', 'Edit multiselect script' );
		$product           = Product::find( $id );
		$postAttributesAry = array();

		if ( $product->product_type == 4 ) {
			$SetTypeAttribsArr = DB::table( $this->DBTables['Parent_Product_Attributes'] )
			                       ->join( $this->DBTables['Attributes'], $this->DBTables['Parent_Product_Attributes'] . '.attrib_id', '=', $this->DBTables['Attributes'] . '.id' )
			                       ->where( $this->DBTables['Parent_Product_Attributes'] . '.product_id', '=', $id )
			                       ->get();
		} else {
			$postAttributesMappingAry = DB::table( 'product_attrib_map' )
			                              ->join( 'attribute_vals', 'attribute_vals.id', '=', 'product_attrib_map.attrib_val_id' )
			                              ->join( 'attributes', 'attributes.id', '=', 'attribute_vals.attrib_id' )
			                              ->where( 'product_attrib_map.prod_id', '=', $id )
			                              ->select( 'product_attrib_map.attrib_val_id', 'attribute_vals.value', 'attributes.attrib_name', 'attributes.id' )
			                              ->groupBy( 'attributes.id' )
			                              ->orderBy( 'product_attrib_map.id', 'ASC' )
			                              ->get();
		}
		if ( ! empty( $postAttributesMappingAry ) ) {

			$attributesAry = DB::table( 'attributes' )->orderBy( 'attrib_name', 'ASC' )->get();

			foreach ( $postAttributesMappingAry as $i => $postAttributesMappingData ) {
				$count                                       = $i + 1;
				$postAttributesAry[ 'attribName_' . $count ] = $postAttributesMappingData->id;
				$savedOptionAry                              = DB::table( 'product_attrib_map' )
				                                                 ->join( 'attribute_vals', 'attribute_vals.id', '=', 'product_attrib_map.attrib_val_id' )
				                                                 ->join( 'attributes', 'attributes.id', '=', 'attribute_vals.attrib_id' )
				                                                 ->select( 'product_attrib_map.attrib_val_id', 'attribute_vals.value', 'attributes.attrib_name', 'attributes.id' )->where( 'product_attrib_map.prod_id', '=', $id )->where( 'attributes.id', '=', $postAttributesMappingData->id )
				                                                 ->orderBy( 'product_attrib_map.id', 'ASC' )
				                                                 ->get();

				if ( ! empty( $savedOptionAry ) ) {
					foreach ( $savedOptionAry as $savedOptionData ) {
						$postAttributesOptions[ 'attribOptions_' . $count ][] = $savedOptionData->attrib_val_id;
					}
				}
			}


			$attributesOptions = array();
			if ( ! is_null( $postAttributesAry ) ) {
				foreach ( $postAttributesAry as $attributesData ) {
					$attributesOptions[ $attributesData ] = DB::table( 'attribute_vals' )->where( 'attrib_id', '=', $attributesData )->orderBy( 'value', 'ASC' )->get();
				}
			}

			session()->flash( 'attributesAry', $attributesAry ); // Store it as flash data.
			session()->flash( 'attributesOptions', $attributesOptions );
			session()->flash( 'postAttributesAry', $postAttributesAry );
			session()->flash( 'postAttributesOptions', ( isset( $postAttributesOptions ) && ! empty( $postAttributesOptions ) ? $postAttributesOptions : array() ) );
		}
		$productsAry = DB::table( 'products' )
		                 ->where( 'id', '<>', $id )
		                 ->select( 'id', 'name' )
		                 ->orderBy( 'name', 'ASC' )
		                 ->get();
		if ( $product->is_upsell_product == 1 ) {
			$postUpsellPro = array();
			$UpsellProds   = DB::table( $this->DBTables['Upsell_Products'] )->where( 'product_id', '=', $id )->orderBy( 'id', 'ASC' )->get();
			if ( ! empty( $UpsellProds ) ) {
				foreach ( $UpsellProds as $UpsellProdsData ) {
					$postUpsellPro[] = $UpsellProdsData->upsell_prod_id;
				}

			}

			session()->flash( 'upsell_products', $postUpsellPro );
		}

		$ProdGalleryArr = $this->getProductGalleryImgs( $id );
		$prodType       = Config::get( 'constants.productType' );
		$categories     = Category::all();
		$GroupProds     = DB::table( $this->DBTables['Group_Products'] )->where( 'parent_productid', '=', $id )->get();
		$SelectedProds  = array();
		if ( ! empty( $GroupProds ) ) {
			foreach ( $GroupProds as $Prods ) {
				array_push( $SelectedProds, $Prods->product_id );
			}
		}

		return view( 'pages.inventory.editProduct', compact( 'product', 'prodType', 'categories', 'SelectedProds', 'productsAry', 'ProdGalleryArr', 'SetTypeAttribsArr' ) );
	}

	public function update( Request $request ) {
		Input::merge( array_map( 'trim', Input::except( 'group_products', 'proAttrOpt', 'proAttr', 'upsell_products', 'gallery_img' ) ) );

		$id       = $request->id;
		$allInput = $request->input();
		$prodType = $allInput['product_type'];

		if ( ! $request->hasFile( 'feat_img' ) ) {
			$request->feat_img = $request->old_prod_img;
		}
		$rules       = array(
			'name'        => 'required | max:255 | regex:/(^[A-Za-z0-9 ]+$)+/',
			'description' => 'required',
			'prod_video'  => 'active_url',
			'sku'         => 'required | max:255 | alpha_num',
			'quantity'    => 'required | integer | min:1',
			'price'       => 'required|numeric|between:0.01,9999999.99',
		);
		$files       = Input::file( 'gallery_img' );
		$file_count  = count( $files );
		$uploadcount = 0;
		$prodsale    = $request->sale;
		$rentalProd  = $request->rent;
		if ( ! $prodsale ) {
			$request->sale = 0;
		}
		if ( ! $rentalProd ) {
			$request->rent = 0;
		}
		if ( $prodsale == 1 ) {
			$rules['sale_price'] = 'required|numeric|between:0.01,9999999.99';
		} else {
			$request->sale_price = 0;
		}
		if ( $rentalProd == 1 ) {
			$rules['rent_price'] = 'required|numeric|between:0.01,9999999.99';
		} else {
			$request->rent_price = 0;
		}
		if ( $prodType == '3' ) {
			$rules['group_products'] = 'required';
		}

		$upsellProductsAry = array();
		$isUpsellProduct   = (int) $request->is_upsell_product;
		$upsellProductsAry = $request->upsell_products;
		if ( $isUpsellProduct == 1 ) {
			$rules['upsell_products'] = 'required';
		}

		$validator             = $this->getValidationFactory()->make( $request->only( 'name', 'description', 'prod_video', 'sku', 'quantity', 'price', 'sale_price', 'rent_price', 'group_products', 'upsell_products' ), $rules );
		$postAttributesAry     = $request->proAttr;
		$postAttributesOptions = $request->proAttrOpt;
		if ( $validator->fails() ) {
			if ( $request->product_type != 4 ) {
				$attributesAry     = $attributesAry = DB::table( 'attributes' )->orderBy( 'attrib_name', 'ASC' )->get();
				$attributesOptions = array();
				if ( ! is_null( $postAttributesAry ) ) {
					foreach ( $postAttributesAry as $attributesData ) {
						$attributesOptions[ $attributesData ] = DB::table( 'attribute_vals' )->where( 'attrib_id', '=', $attributesData )->orderBy( 'value', 'ASC' )->get();
					}
				}

				session()->flash( 'attributesAry', $attributesAry ); // Store it as flash data.
				session()->flash( 'attributesOptions', $attributesOptions );
				session()->flash( 'postAttributesAry', $postAttributesAry );
				session()->flash( 'postAttributesOptions', $postAttributesOptions );
				session()->flash( 'upsell_products', $upsellProductsAry );
			}

			return redirect()->to( $this->getRedirectUrl() )
			                 ->withInput( $request->input() )
			                 ->withErrors( $this->formatValidationErrors( $validator ), $this->errorBag() );
		} else {
			if ( ! empty( $request->gallery_img ) ) {
				foreach ( $files as $file ) {
					$rules     = array( 'gallery_img' => 'image|mimes:jpeg,bmp,png,gif|max:6000' ); //'required|mimes:png,gif,jpeg,txt,pdf,doc'
					$messages  = [
						'gallery_img.image' => 'Product images should be images.',
						'gallery_img.mimes' => 'Product images must be in jpeg, bmp, png format',
						'gallery_img.max'   => 'Product images image files must not be greater than 2MB each'
					];
					$validator = $this->getValidationFactory()->make( array( 'gallery_img' => $file ), $rules, $messages );
					if ( $validator->passes() ) {
						$uploadcount ++;
					}
				}
			}
			if ( ( $file_count == 0 ) || ( $file_count > 0 && $uploadcount == $file_count ) ) {
				Product::find( $id )->update( $request->all() );
				DB::table( $this->DBTables['Products'] )
				  ->where( 'id', $id )
				  ->update( [
					  'sale'       => $request->sale,
					  'rent'       => $request->rent,
					  'sale_price' => $request->sale_price,
					  'rent_price' => $request->rent_price
				  ] );
				if ( $request->product_type == 4 ) {
					DB::table( $this->DBTables['Parent_Product_Attributes'] )->where( 'product_id', '=', $id )->delete();
					$AttribCounts = $request->totalattribs;
					for ( $i = 1; $i <= $AttribCounts; $i ++ ) {
						DB::table( $this->DBTables['Parent_Product_Attributes'] )->insert(
							[ 'product_id' => $id, 'attrib_id' => $allInput['attribNo' . $i] ]
						);
					}
				} else {
					DB::table( 'product_attrib_map' )->where( 'prod_id', '=', $id )->delete();
					if ( ! is_null( $postAttributesOptions ) ) {
						foreach ( $postAttributesOptions as $attributesOptionsAry ) {
							foreach ( $attributesOptionsAry as $optionId ) {
								$idLastOption = DB::table( 'product_attrib_map' )->insertGetId( [
									'prod_id'       => $id,
									'attrib_val_id' => $optionId
								] );
							}
						}
					}
				}
				if ( $request->hasFile( 'feat_img' ) ) {
					$imageTempName = $request->feat_img->getPathname();
					$imageName     = time() . '.' . $request->feat_img->getClientOriginalExtension();
					$request->feat_img->move( public_path( '../../product_img' ), $imageName );
					DB::table( 'products' )
					  ->where( 'feat_img', $imageTempName )
					  ->update( [ 'feat_img' => $imageName ] );
				}
				if ( ! empty( $request->gallery_img ) ) {
					DB::table( $this->DBTables['Product_Gallery'] )->where( 'product_id', '=', $id )->delete();
					foreach ( $request->gallery_img as $photo ) {
						$filename    = $photo->store( 'public/product-gallery' );
						$fileNameArr = explode( '/', $filename );
						DB::table( $this->DBTables['Product_Gallery'] )->insert(
							[ 'product_id' => $id, 'gallery_img' => $fileNameArr['1'] . '/' . $fileNameArr['2'] ]
						);
					}
				}
				if ( $prodType == '3' ) {
					$GroupProds = $allInput['group_products'];
					if ( ! empty( $GroupProds ) ) {
						DB::table( $this->DBTables['Group_Products'] )->where( 'parent_productid', '=', $id )->delete();
						if ( $prodType == '3' ) {
							foreach ( $GroupProds as $key => $vals ) {
								DB::table( $this->DBTables['Group_Products'] )->insert(
									[ 'parent_productid' => $id, 'product_id' => $vals ]
								);
							}
						}
					}
				}
				DB::table( 'products' )
				  ->where( 'id', $id )
				  ->update( [ 'is_upsell_product' => (int) $isUpsellProduct ] );

				DB::table( $this->DBTables['Upsell_Products'] )->where( 'product_id', '=', $id )->delete();
				if ( (int) $isUpsellProduct == 1 ) {
					if ( ! empty( $upsellProductsAry ) ) {
						foreach ( $upsellProductsAry as $key => $vals ) {
							DB::table( $this->DBTables['Upsell_Products'] )->insert(
								[ 'product_id' => $id, 'upsell_prod_id' => $vals ]
							);
						}
					}

				}

				return redirect()->route( 'productCRUD.index' )
				                 ->with( 'success', 'Product updated successfully' );
			}

		}
	}

//    Add Attributes in Product

	public function destroy( Request $request ) {
		$id = $request->delProdid;
		Product::find( $id )->delete();

		return redirect()->to( '/view_products' )
		                 ->with( 'success', 'Product deleted successfully' );
	}

	public function addProductAttribute( Request $request ) {
		View::share( 'title', 'Add attributes' );
		$attributesAry      = DB::table( 'attributes' )->orderBy( 'attrib_name', 'ASC' )->get();
		$attributes_options = "";
		if ( ! empty( $attributesAry ) ) {
			foreach ( $attributesAry as $attribute ) {
				$attributes_options .= "<option value='" . $attribute->id . "'>" . $attribute->attrib_name . "</option>";
			}

			$attributesOptions      = DB::table( 'attribute_vals' )->where( 'attrib_id', '=', $attributesAry[0]->id )->orderBy( 'value', 'ASC' )->get();
			$attributes_options_val = "";
			if ( ! empty( $attributesOptions ) ) {
				foreach ( $attributesOptions as $attributeValues ) {
					$attributes_options_val .= "<option value='" . $attributeValues->id . "'>" . $attributeValues->value . "</option>";
				}
			}
		}

		$iAttributeCount     = trim( $request->iAttributeCount );
		$iAttributeNextCount = $iAttributeCount + 1;

		$attributeDiv = "<div class='col-lg-6'>
                            <h4 class='pull-left'>Attributes Name " . $iAttributeCount . "</h4>
                            <select class='select2-single form-control pull-left' name='proAttr[attribName_" . $iAttributeCount . "]' id='attribName_" . $iAttributeCount . "' onchange='getAttributeOptions(" . $iAttributeCount . ")'>" . $attributes_options . "</select>
                        </div>
                        <div class='col-lg-5' id='attributeOptionsDiv_" . $iAttributeCount . "'>
                            <h4 class='pull-left'>Options</h4>
                            <select class='select2-multiple form-control select-primary pull-left' multiple='multiple' name='proAttrOpt[attribOptions_" . $iAttributeCount . "][]' id='attribOptions_" . $iAttributeCount . "'>" . $attributes_options_val . "</select>
                        </div>";
		$attributeDiv .= "||||";
		$attributeDiv .= "<h2></h2><div id='attributeDiv_" . $iAttributeNextCount . "' class='row'>
                            <div class='col-lg-4'>
                                <a href='javascript:void(0)' class='button form-control btn-dark' onclick='addProAttribute(" . $iAttributeNextCount . ")'><i class='fa fa-plus'></i> Add Attribute</a>
                            </div>
                        </div>";
		echo $attributeDiv;
		die;
	}

//    Get Attributes Options for product

	public function getCategoryProds( Request $request ) {
		$data     = $request->all();
		$CatProds = Product::where( [
			[ 'category', '=', $data['categoryId'] ],
			[ 'id', '!=', $data['prodid'] ],
		] )->get();

		return $CatProds;
	}

	public function getAttributes() {
		$attributesArr = DB::table( $this->DBTables['Attributes'] )->get();

		return $attributesArr;
	}

	public function getAttrbtOptions( Request $request ) {

		$iAttributeCount = $request->iAttributeCount;
		$idAttribute     = $request->idAttribute;

		$attributesOptions      = DB::table( 'attribute_vals' )->where( 'attrib_id', '=', $idAttribute )->orderBy( 'value', 'ASC' )->get();
		$attributes_options_val = "";
		if ( ! empty( $attributesOptions ) ) {
			foreach ( $attributesOptions as $attributeValues ) {
				$attributes_options_val .= "<option value='" . $attributeValues->id . "'>" . $attributeValues->value . "</option>";
			}
		}

		$attributeOptionDiv = "<h4 class='pull-left'>Options</h4>
                                <select class='select2-multiple form-control select-primary pull-left' multiple='multiple' name='proAttrOpt[attribOptions_" . $iAttributeCount . "][]' id='attribOptions_" . $iAttributeCount . "'>" . $attributes_options_val . "</select>";
		echo $attributeOptionDiv;
		die;
	}

	public function searchInventory( Request $request ) {

		$attributesAry     = $attributesAry = DB::table( 'attributes' )->orderBy( 'attrib_name', 'ASC' )->get();
		$attributesOptions = array();


		if ( ! is_null( $attributesAry ) ) {
			foreach ( $attributesAry as $attributesData ) {
				$attributesOptions[ $attributesData->id ] = DB::table( 'attribute_vals' )->where( 'attrib_id', '=', $attributesData->id )->orderBy( 'value', 'ASC' )->get();
			}
		}

		$rowsPerPage             = Config::get( 'constants.PaginationRowsPerPage' );
		$optionAry               = array();
		$previousSearchAttribute = session( 'searchAttribute' );
		if ( $previousSearchAttribute && ! $request->searchAttribute ) {
			$request->searchAttribute = $previousSearchAttribute;
		}
		if ( $request->searchAttribute ) {
			$searchAttribute = $request->searchAttribute;
			session()->flash( 'searchAttribute', $request->searchAttribute ); // Store it as flash data.

			foreach ( $searchAttribute as $optionId ) {
				if ( $optionId > 0 ) {
					$optionAry[] = $optionId;
				}
			}

			if ( ! empty( $optionAry ) ) {
				$products = DB::table( 'products' )
				              ->join( 'product_attrib_map', 'products.id', '=', 'product_attrib_map.prod_id' )
				              ->whereIn( 'product_attrib_map.attrib_val_id', $optionAry )
				              ->select( 'products.*', 'product_attrib_map.prod_id' )
				              ->groupBy( 'product_attrib_map.prod_id' )
				              ->orderBy( 'products.id', 'DESC' )
				              ->paginate( $rowsPerPage );

				if ( ! empty( $products ) ) {
					foreach ( $products as $key => $productData ) {

						$products[ $key ]->attributeAry = $this->getProductAttribute( $productData->id );
					}
				}
			} else {
				$products = Product::orderBy( 'id', 'DESC' )->paginate( $rowsPerPage );
				if ( ! empty( $products ) ) {
					foreach ( $products as $key => $productData ) {
						$products[ $key ]->attributeAry = $this->getProductAttribute( $productData->id );
					}
				}
			}
		} else {
			$products = Product::orderBy( 'id', 'DESC' )->paginate( $rowsPerPage );
			if ( ! empty( $products ) ) {
				foreach ( $products as $key => $productData ) {
					$products[ $key ]->attributeAry = $this->getProductAttribute( $productData->id );
				}
			}
		}


		return view( 'pages.inventory.viewproducts', compact( 'products' ) )
			->with( 'attributesAry', $attributesAry )
			->with( 'attributesOptions', $attributesOptions )
			->with( 'i', ( $request->input( 'page', 1 ) - 1 ) * $rowsPerPage );
	}

	public function viewSets( $prodid ) {
		View::share( 'title', 'Manage golf sets' );
		$resultArr   = array(
			'sku'     => '',
			'name'    => '',
			'attribs' => '',
            'disable' => ''
		);
		$GroupProds = DB::table( $this->DBTables['Group_Products'] )
		                ->join( $this->DBTables['Products'], $this->DBTables['Group_Products'] . '.product_id', '=', $this->DBTables['Products'] . '.id' )
		                ->where( $this->DBTables['Group_Products'] . '.parent_productid', '=', $prodid )
		                ->select( $this->DBTables['Products'] . '.id as prodid', $this->DBTables['Products'] . '.sku',$this->DBTables['Products'] . '.name',$this->DBTables['Products'].'.disable' )
		                ->get();
		if ( ! empty($GroupProds) ) {
			$data = array();
			foreach ( $GroupProds as $golfSet ) {

				$resultArr['sku']  = $golfSet->sku;
				$resultArr['name'] = $golfSet->name;
				$resultArr['productid'] = $golfSet->prodid;
                $resultArr['disable'] = $golfSet->disable;
				$AttribValsArr     = DB::table( $this->DBTables['Products_Attribute_Mapping'] )
				                       ->join( $this->DBTables['Attributes_Values'], $this->DBTables['Attributes_Values'] . '.id', '=', $this->DBTables['Products_Attribute_Mapping'] . '.attrib_val_id' )
				                       ->join( $this->DBTables['Attributes'], $this->DBTables['Attributes'] . '.id', '=', $this->DBTables['Attributes_Values'] . '.attrib_id' )
				                       ->where( $this->DBTables['Products_Attribute_Mapping'] . '.prod_id', '=', $golfSet->prodid )
				                       ->select( $this->DBTables['Attributes'] . '.attrib_name', $this->DBTables['Attributes_Values'] . '.value' )
				                       ->get();

				$attributesVals    = '';
				if ( ! empty( $AttribValsArr ) ) {
					foreach ( $AttribValsArr as $attribVal ) {
						$attributesVals .= $attribVal->attrib_name . ' : ' . $attribVal->value.', ';
					}
				}
				$attributesVals = substr(trim($attributesVals),0,-1);
				$resultArr['attribs'] = $attributesVals;
				array_push($data,$resultArr);
			}
		}
		$i = 0;
		return view( 'pages.inventory.viewSets', compact( 'data','prodid', 'i' ) );
	}

	public function addSets($prodid){
		View::share( 'title', 'Add golf set' );
		$resultArr   = array(
			'attrib_name'     => '',
			'attrib_vals'    => ''
		);
		$getParentProdAttribsArr = DB::table( $this->DBTables['Attributes'] )
		                             ->join( $this->DBTables['Parent_Product_Attributes'], $this->DBTables['Attributes'] . '.id', '=', $this->DBTables['Parent_Product_Attributes'] . '.attrib_id' )
		                             ->where( $this->DBTables['Parent_Product_Attributes'] . '.product_id', '=', $prodid )
		                             ->select( $this->DBTables['Attributes'] . '.attrib_name', $this->DBTables['Attributes'] . '.id')
		                             ->get();

		$AttribsArr = array();
		if(!empty($getParentProdAttribsArr)){
			foreach ($getParentProdAttribsArr as $parentProdAttrib){
				$AttribValsArr = DB::table( $this->DBTables['Attributes_Values'] )
				                   ->where( $this->DBTables['Attributes_Values'] . '.attrib_id', '=', $parentProdAttrib->id )
				                   ->select($this->DBTables['Attributes_Values'] . '.value', $this->DBTables['Attributes_Values'] . '.id' )
				                   ->get();
				$resultArr['attrib_name'] = $parentProdAttrib->attrib_name;

				if(!empty($AttribValsArr)){
					$resultArr['attrib_vals'] = $AttribValsArr;
				}
				array_push($AttribsArr,$resultArr);
			}
		}
$counter = 0;
		return view( 'pages.inventory.addSet', compact( 'AttribsArr','prodid', 'counter' ) );
	}

	public function storeSets(Request $request){
		Input::merge( array_map( 'trim', Input::all() ) );
		$allInput    = $request->input();
		$rules = array();
		$rules['name'] = 'bail|required|unique:posts|max:255';
		$rules['sku'] = 'required';
		for($i=1;$i<=$request->totalattribs;$i++){
			$rules['attribvals'.$i] = 'required';
		}

		$product           = Product::find( $request->productid );
//		$this->validate($request, $rules);

		$prodid = DB::table( $this->DBTables['Products'] )->insertGetId(
			[ 'name' => $request->name, 'sku' => $request->sku, 'quantity' => $product->quantity, 'product_type' => 5 ]
		);
		if($prodid>0){
			DB::table( $this->DBTables['Group_Products'] )->insert(
				[ 'parent_productid' => $request->productid, 'product_id' => $prodid ]
			);

			for($j=1;$j<=$request->totalattribs;$j++){
				DB::table( $this->DBTables['Products_Attribute_Mapping'] )->insert(
					[ 'prod_id' => $prodid, 'attrib_val_id' => $allInput['attribvals'.$j] ]
				);
			}
		}

		return redirect('/manage_sets/'.$request->productid)
		                 ->with( 'success', 'Set added successfully' );
	}

	public function deleteSet( Request $request ) {
		$id = $request->delProdid;
		DB::table( $this->DBTables['Products_Attribute_Mapping'] )
		            ->where('prod_id', '=', $id)
					->delete();
		DB::table( $this->DBTables['Group_Products'] )
		  ->where('product_id', '=', $id)
		  ->delete();
		Product::find( $id )->delete();

		return redirect('/manage_sets/'.$request->productid)
			->with( 'success', 'Set deleted successfully' );
	}

	public function editSets($prodid,$parentid){
		View::share( 'title', 'Edit golf set' );
		$product           = Product::find( $prodid );
		$ParentProdArr     = DB::table( $this->DBTables['Group_Products'] )
		                       ->where('product_id', '=', $prodid )
		                       ->select('parent_productid')
		                       ->get();
		$resultArr   = array(
			'attrib_name'     => '',
			'attrib_vals'    => ''
		);
		$AttribsArr = array();
		if(!empty($ParentProdArr)){

			foreach ($ParentProdArr as $parentProd){
				$getParentProdAttribsArr = DB::table( $this->DBTables['Attributes'] )
				                             ->join( $this->DBTables['Parent_Product_Attributes'], $this->DBTables['Attributes'] . '.id', '=', $this->DBTables['Parent_Product_Attributes'] . '.attrib_id' )
				                             ->where( $this->DBTables['Parent_Product_Attributes'] . '.product_id', '=', $parentProd->parent_productid)
				                             ->select( $this->DBTables['Attributes'] . '.attrib_name', $this->DBTables['Attributes'] . '.id')
				                             ->get();

				if(!empty($getParentProdAttribsArr)){
					foreach ($getParentProdAttribsArr as $parentProdAttrib){
						$AttribValsArr = DB::table( $this->DBTables['Attributes_Values'] )
						                   ->where( $this->DBTables['Attributes_Values'] . '.attrib_id', '=', $parentProdAttrib->id )
						                   ->select($this->DBTables['Attributes_Values'] . '.value', $this->DBTables['Attributes_Values'] . '.id' )
						                   ->get();
						$resultArr['attrib_name'] = $parentProdAttrib->attrib_name;

						if(!empty($AttribValsArr)){
							$resultArr['attrib_vals'] = $AttribValsArr;
						}
						array_push($AttribsArr,$resultArr);
					}
				}
			}
		}
		$AttribValsArr     = DB::table( $this->DBTables['Products_Attribute_Mapping'] )
		                       ->join( $this->DBTables['Attributes_Values'], $this->DBTables['Attributes_Values'] . '.id', '=', $this->DBTables['Products_Attribute_Mapping'] . '.attrib_val_id' )
		                       ->join( $this->DBTables['Attributes'], $this->DBTables['Attributes'] . '.id', '=', $this->DBTables['Attributes_Values'] . '.attrib_id' )
		                       ->where( $this->DBTables['Products_Attribute_Mapping'] . '.prod_id', '=', $prodid )
		                       ->select( $this->DBTables['Attributes'] . '.attrib_name', $this->DBTables['Attributes_Values'] . '.id' )
		                       ->get();
//		print_r($AttribValsArr);
		$counter = 0;
		return view( 'pages.inventory.editSets', compact( 'product', 'AttribValsArr', 'prodid', 'AttribsArr', 'counter', 'parentid' ) );
	}

	public function updateSets(Request $request){
		Input::merge( array_map( 'trim', Input::all() ) );

		$id       = $request->productid;
		$allInput = $request->input();
		DB::table( $this->DBTables['Products'] )
		  ->where( 'id', $id )
		  ->update( [
			  'name'       => $request->name
		  ] );
		DB::table( $this->DBTables['Products_Attribute_Mapping'] )->where( 'prod_id', '=', $id )->delete();
		for($j=1;$j<=$request->totalattribs;$j++){
			DB::table( $this->DBTables['Products_Attribute_Mapping'] )->insert(
				[ 'prod_id' => $id, 'attrib_val_id' => $allInput['attribvals'.$j] ]
			);
		}
		return redirect()->back()
			->with( 'success', 'Set updated successfully' );
	}

    public function enableDisableProd(Request $request){
        Input::merge( array_map( 'trim', Input::all() ) );

        $id       = $request->prodid;
        $prodType = $request->prodtype;

        $result = 0;
        if($prodType == '1'){
            $GroupProds = DB::table( $this->DBTables['Group_Products'] )
                ->join( $this->DBTables['Products'], $this->DBTables['Group_Products'] . '.product_id', '=', $this->DBTables['Products'] . '.id' )
                ->where( $this->DBTables['Group_Products'] . '.parent_productid', '=', $id )
                ->select( $this->DBTables['Products'] . '.id as prodid', $this->DBTables['Products'] . '.sku',$this->DBTables['Products'] . '.name',$this->DBTables['Products'].'.disable' )
                ->get();
            if ( ! empty($GroupProds) ) {
                foreach ($GroupProds as $golfSet) {
                    $result = DB::table( $this->DBTables['Products'] )
                        ->where( 'id', $golfSet->prodid )
                        ->update( [
                            'disable'       => $request->flag
                        ] );
                }
            }
            $result = DB::table( $this->DBTables['Products'] )
                ->where( 'id', $id )
                ->update( [
                    'disable'       => $request->flag
                ] );
        }else{
            $result = DB::table( $this->DBTables['Products'] )
                ->where( 'id', $id )
                ->update( [
                    'disable'       => $request->flag
                ] );
        }
        return $result;
    }

	public function savecaption(Request $request){
		Input::merge( array_map( 'trim', Input::all() ) );

		$galid       = $request->imgid;
		$caption = $request->imgcaption;
		DB::table( $this->DBTables['Product_Gallery'] )
		  ->where( 'id', $galid )
		  ->update( [
			  'caption'       => $caption
		  ] );
		$response['result'] = 'True';
		return $response;
	}
}