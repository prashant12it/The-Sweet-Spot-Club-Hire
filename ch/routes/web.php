<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get( 'logout', '\App\Http\Controllers\Auth\LoginController@logout' );
Route::get( 'admin', [ 'uses' => 'AdminController@index' ] );
Route::get( 'admin/create', [ 'uses' => 'AdminController@create' ] );
Route::post( 'admin', [ 'uses' => 'AdminController@store' ] );
Route::auth();

Route::get( '/home', 'HomeController@index' );
Route::get( '/changepassword', function () {
	if ( Auth::guest() ) {
		return view( 'auth.login' );
	} else {
		return view( 'pages.changepass' );
	}

} );
Route::post( '/filterProducts', [ 'uses' => 'HireController@filterProducts' ] );

Route::post( '/changepassword', [ 'uses' => 'AdminController@savepass' ] );

Route::any( '/', array( 'as' => '/', 'uses' => 'ClubSearchController@index' ) );
Route::any( '/clubsearch/{lang?}', [ 'uses' => 'HireController@index' ] );
Route::any( '/courier-mail', [ 'uses' => 'HireController@SendCronMail' ] );
Route::any( '/pickup-courier-mail', [ 'uses' => 'HireController@PickupMailCron' ] );
Route::any( '/club-courier-mail', [ 'uses' => 'AdminCCOrdersController@PickupMailCron' ] );
Route::any( '/disputed_order_notification', [ 'uses' => 'HireController@DisputedOrderNotification' ] );
Route::any( '/sendmandrilmail', [ 'uses' => 'HireController@sendMandrilMail' ] );
Route::any( '/second-purchase-email', [ 'uses' => 'HireController@sendMandrilSecondMail' ] );
Route::any( '/third-purchase-email', [ 'uses' => 'HireController@sendMandrilThirdMail' ] );
Route::post( '/callmethodbyrequest', [ 'uses' => 'HireController@callMethodByRequest' ] );
Route::post( '/addremoveinsurance', [ 'uses' => 'HireController@addremoveInsuranceToOrder' ] );
Route::post( '/calculateshipping', [ 'uses' => 'HireController@calculateshipping' ] );
Route::any( '/insurance', [ 'uses' => 'HireController@insurance' ] );
Route::post( '/remove-order', [ 'uses' => 'HireController@removeOrder' ] );
Route::post( '/clearfilter', [ 'uses' => 'HireController@clearfilter' ] );
Route::post( '/getcartprodids', [ 'uses' => 'HireController@getCartProdIdsByRefId' ] );
Route::post( '/remove-from-cart', [ 'uses' => 'CutomerOrderController@removeProductFromCart' ] );
Route::post( '/decrease-prod-qty', [ 'uses' => 'CutomerOrderController@decreaseQtyFromCart' ] );
Route::post( '/increase-prod-qty', [ 'uses' => 'CutomerOrderController@increaseQtyToCart' ] );
Route::post('/addtocart',['uses'=>'CutomerOrderController@addProductToCart']);
Route::get('/shipping',['uses'=>'CutomerOrderController@shipping']);
Route::get('/order-preview',['uses'=>'CutomerOrderController@orderPreview']);
Route::post('/add_shipping',['uses'=>'CutomerOrderController@updateShippingAndPayment']);
//Route::get('/thankyou',['uses'=>'CutomerOrderController@thank_you']);
Route::get('manageMailChimp', 'MailChimpController@manageMailChimp');
Route::post('subscribe',['as'=>'subscribe','uses'=>'MailChimpController@subscribe']);
Route::post( '/getfooter', [ 'uses' => 'HireController@getSiteFooter' ] );
Route::post( '/updatepaymentopt', [ 'uses' => 'HireController@updatePaymentOpt' ] );
Route::post( '/getAjaxPreorderDetails', [ 'uses' => 'HireController@getAjaxPreorderDetails' ] );
Route::post('/getunorderdprods',['uses'=>'AdminOrdersController@getUnorderedProds']);
Route::post('/changeOrderItem',['uses'=>'AdminOrdersController@changeOrderItem']);
Route::post('thankyou','CutomerOrderController@thankyounew');
Route::any('importccdata','HireController@importcccost');
/*
 * ClubCourier start
 */
Route::any( '/clubcourier/booking', [ 'uses' => 'ClubCourier@index' ] );
Route::any( '/clubcourier/preview-booking', [ 'uses' => 'ClubCourier@previewBooking' ] );
Route::any( '/clubcourier/courier_booking', [ 'uses' => 'ClubCourier@addBooking' ] );
Route::post( '/clubcourier/thankyou', [ 'uses' => 'ClubCourier@thankyouCC' ] );
Route::get( '/clubcourier/thankyou', [ 'uses' => 'ClubCourier@thank_you' ] );
/*
 * ClubCourier End
 */
/*
 * Front End
 * Partner
 */
Route::get( '/partner', [ 'uses' => 'PartnerController@index' ] );
Route::get( '/partner/login', [ 'uses' => 'PartnerController@login' ] );
Route::get( '/partner/signup', [ 'uses' => 'PartnerController@signup' ] );
Route::get( '/partner/logout', [ 'uses' => 'PartnerController@logout' ] );
Route::post( '/partner/validate_login', [ 'uses' => 'PartnerController@validate_accnt' ] );
Route::get( '/partner/dashboard', [ 'uses' => 'PartnerController@dashboard' ] );
Route::get( '/partner/forgotPassword', [ 'uses' => 'PartnerController@forgot_password' ] );
Route::post( '/partner/send_forgot_password', [ 'uses' => 'PartnerController@sendforgotPassword' ] );
Route::get( '/partner/reset_pass/{reset_key}', [ 'uses' => 'PartnerController@resetPassword' ] );
Route::post( '/partner/saveNewPassword', [ 'uses' => 'PartnerController@updatePassword' ] );
Route::post( '/partner/signup_partner', [ 'uses' => 'PartnerController@signup_partner' ] );
Route::get( '/partner/profile', [ 'uses' => 'PartnerController@show_profile' ] );
Route::post( '/partner/updateProfile', [ 'uses' => 'PartnerController@updateProdileDetails' ] );
Route::get( '/partner/change_password', [ 'uses' => 'PartnerController@changePassword' ] );
Route::post( '/partner/update_password', [ 'uses' => 'PartnerController@updateOldPassword' ] );
Route::get( '/partner/banners_list', [ 'uses' => 'PartnerController@banners' ] );

Route::get( '/partner_pro/{partner_ref}/{banner_ref}', [ 'uses' => 'PartnerController@clicks_track' ] );


Route::group( [ 'middleware' => 'auth' ], function () {
	/*
	 * Admin Dashboard
	 */
	Route::get( '/dashboard', [ 'uses' => 'AdminController@dashboard' ] );
	Route::post( '/dashboard', [ 'uses' => 'AdminController@dashboard' ] );
	/*
	 * Admin Inventory Management
	 *
	 */
	Route::get( '/view_products', [ 'uses' => 'ProductCRUDController@index' ] );

	Route::get( '/add_product', [ 'uses' => 'ProductCRUDController@create' ] );
	Route::post( '/add_product', [ 'uses' => 'ProductCRUDController@store' ] );

	Route::resource( 'productCRUD', 'ProductCRUDController' );

	Route::post( '/update_product', [ 'uses' => 'ProductCRUDController@update' ] );

	Route::post( '/delete_product', [ 'uses' => 'ProductCRUDController@destroy' ] );

	Route::post( '/getCategoryProds', [ 'uses' => 'ProductCRUDController@getCategoryProds' ] );
	Route::post( '/savecaption', [ 'uses' => 'ProductCRUDController@savecaption' ] );
    Route::post( '/enableDisableProd', [ 'uses' => 'ProductCRUDController@enableDisableProd' ] );
	/*
	 * Attribute Module Routing
	 */
	Route::resource( 'attributes', 'ProductAttributeController' );

	Route::get( '/pro_attributes', [ 'uses' => 'ProductAttributeController@index' ] );

	Route::get( '/add_attribute', [ 'uses' => 'ProductAttributeController@create' ] );

	Route::post( '/add_attribute', [ 'uses' => 'ProductAttributeController@store' ] );

	Route::post( '/update_attribute', [ 'uses' => 'ProductAttributeController@update' ] );

	Route::post( '/delete_attribute', [ 'uses' => 'ProductAttributeController@destroy' ] );

	Route::get( '/attribute_options/{id}', [ 'uses' => 'ProductAttributeController@options' ] );

	Route::get( '/option_add/{id}', [ 'uses' => 'ProductAttributeController@optionsAdd' ] );

	Route::post( '/option_add/{id}', [ 'uses' => 'ProductAttributeController@optionsSave' ] );

	Route::get( '/option_edit/{id}/{optionId}', [ 'uses' => 'ProductAttributeController@optionsEdit' ] );

	Route::post( '/option_edit/{idAttribute}/{optionId}', [ 'uses' => 'ProductAttributeController@optionsUpdate' ] );

	Route::post( '/delete_attribute_option', [ 'uses' => 'ProductAttributeController@optionsDestroy' ] );

	Route::get( '/getAttributes', [ 'uses' => 'ProductCRUDController@getAttributes' ] );
	/*
	 * Add/Update Product Attributes Routing
	 */
	Route::get( '/addProductAttr', [ 'uses' => 'ProductCRUDController@addProductAttribute' ] );

	Route::get( '/getAttrOptions', [ 'uses' => 'ProductCRUDController@getAttrbtOptions' ] );

    /*
     * Inventory Attributes Filtering Routing
     */
	Route::get('/manage_sets/{prodid}',['uses'=>'ProductCRUDController@viewSets']);
	Route::get('/add_sets/{prodid}',['uses'=>'ProductCRUDController@addSets']);
	Route::post('/add_sets',['uses'=>'ProductCRUDController@storeSets']);
	Route::post('/delete_set',['uses'=>'ProductCRUDController@deleteSet']);
	Route::get('/edit_set/{prodid}/{parentid}',['uses'=>'ProductCRUDController@editSets']);
	Route::post('/edit_set',['uses'=>'ProductCRUDController@updateSets']);
    Route::get('/search_inventory',['uses'=>'ProductCRUDController@searchInventory']);
    Route::post('/search_inventory',['uses'=>'ProductCRUDController@searchInventory']);
    
    /*
     * Admin Order Management
     */
    
    Route::get('/admin_orders',['uses'=>'AdminOrdersController@index']);
    Route::get('/disputed_orders',['uses'=>'AdminOrdersController@disputedOrders']);
    Route::post('/create_order_by_admin',['uses'=>'CutomerOrderController@thank_you']);
    Route::get('/courier',['uses'=>'AdminOrdersController@courier']);
    
    Route::get('/search_order',['uses'=>'AdminOrdersController@searchOrder']);
    Route::post('/search_order',['uses'=>'AdminOrdersController@searchOrder']);
    
    Route::post('/update_order_status',['uses'=>'AdminOrdersController@updateOrderStatus']);
    
    Route::get('/view_orders/{idOrder}',['uses'=>'AdminOrdersController@show']);
    Route::get('/view_disputed_orders/{idOrder}',['uses'=>'AdminOrdersController@viewDisputedOrders']);

    /*
     * Club Courier Admin Order Management
     */

    Route::get('/club_courier_orders',['uses'=>'AdminCCOrdersController@index']);
    Route::get('/club_courier_disputed_orders',['uses'=>'AdminCCOrdersController@disputedOrders']);
    Route::post('/create_cc_order_by_admin',['uses'=>'ClubCourier@thank_you']);
    Route::get('/ccorder_courier',['uses'=>'AdminCCOrdersController@courier']);

    Route::get('/club_courier_search_order',['uses'=>'AdminCCOrdersController@searchOrder']);
    Route::post('/club_courier_search_order',['uses'=>'AdminCCOrdersController@searchOrder']);

    Route::post('/ccupdate_order_status',['uses'=>'AdminCCOrdersController@updateOrderStatus']);

    Route::get('/view_club_courier_orders/{idOrder}',['uses'=>'AdminCCOrdersController@show']);
    Route::get('/view_club_courier_disputed_orders/{idOrder}',['uses'=>'AdminCCOrdersController@viewDisputedOrders']);

    /*
     * Admin Offer Management
     */
    
    Route::get('/offers_mang',['uses'=>'AdminOffersController@index']);
    
    Route::get('/search_offer',['uses'=>'AdminOffersController@searchOffer']);
    Route::post('/search_offer',['uses'=>'AdminOffersController@searchOffer']);
    
    Route::get('/view_offer/{idOffer}',['uses'=>'AdminOffersController@show']);
    
    Route::get('/add_offer',['uses'=>'AdminOffersController@create']);
    Route::post('/add_offer',['uses'=>'AdminOffersController@store']);
    
    Route::get('/edit_offer/{idOffer}',['uses'=>'AdminOffersController@edit']);
    Route::post('/edit_offer',['uses'=>'AdminOffersController@update']);
    
    Route::post('/delete_offer',['uses'=>'AdminOffersController@destroy']);

    /*
     * Club Courier Voucher Management
     */

    Route::get('/voucher_management',['uses'=>'AdminCCVouchersController@index']);

    Route::get('/search_voucher',['uses'=>'AdminCCVouchersController@searchOffer']);
    Route::post('/search_voucher',['uses'=>'AdminCCVouchersController@searchOffer']);

    Route::get('/view_voucher/{idOffer}',['uses'=>'AdminCCVouchersController@show']);

    Route::get('/add_voucher',['uses'=>'AdminCCVouchersController@create']);
    Route::post('/add_voucher',['uses'=>'AdminCCVouchersController@store']);

    Route::get('/edit_voucher/{idOffer}',['uses'=>'AdminCCVouchersController@edit']);
    Route::post('/edit_voucher',['uses'=>'AdminCCVouchersController@update']);

    Route::post('/delete_voucher',['uses'=>'AdminCCVouchersController@destroy']);
    
    /*
     * Customer Order
     * Management
     */
    Route::get('/products',['uses'=>'CutomerOrderController@getAvailableHireProducts']);

    Route::get('/qnty_update',['uses'=>'CutomerOrderController@updateProductQuantity']);
    Route::get('/orderInsrnc',['uses'=>'CutomerOrderController@addRemoveOrderInsurance']);
    Route::get('/addOffer',['uses'=>'CutomerOrderController@applyOrderOffer']);


    /*
     * Partners
     * Routing
     */
    Route::get('/partners',['uses'=>'AdminPartnersController@index']);

    Route::get('/search_partner',['uses'=>'AdminPartnersController@searchPartner']);
    Route::post('/search_partner',['uses'=>'AdminPartnersController@searchPartner']);

    Route::get('/view_partner/{idPartner}',['uses'=>'AdminPartnersController@show']);

    Route::get('/add_partner',['uses'=>'AdminPartnersController@create']);
    Route::post('/add_partner',['uses'=>'AdminPartnersController@store']);

    Route::get('/edit_partner/{idPartner}',['uses'=>'AdminPartnersController@edit']);
    Route::post('/edit_partner',['uses'=>'AdminPartnersController@update']);

    Route::post('/updatePartnerStatus',['uses'=>'AdminPartnersController@status_update']);

    /*
     * Banners
     * Routing
     */
    Route::get('/banners',['uses'=>'AdminBannerController@index']);

    Route::get('/search_banner',['uses'=>'AdminBannerController@searchBanner']);
    Route::post('/search_banner',['uses'=>'AdminBannerController@searchBanner']);
    Route::post('/deleteBanner',['uses'=>'PartnerController@deleteBanner']);
    Route::get('/view_banner/{idBanner}',['uses'=>'AdminBannerController@show']);

    Route::get('/add_banner',['uses'=>'AdminBannerController@create']);
    Route::post('/add_banner',['uses'=>'AdminBannerController@store']);
    Route::get('/add_text_banner',['uses'=>'AdminBannerController@createTextBanner']);
    Route::post('/add_text_banner',['uses'=>'AdminBannerController@storeTextBanner']);
    Route::get('/edit_banner/{idBanner}',['uses'=>'AdminBannerController@edit']);
    Route::post('/edit_banner',['uses'=>'AdminBannerController@update']);
    Route::get('/edit_text_banner/{idBanner}',['uses'=>'AdminBannerController@editTextBanner']);
    Route::post('/edit_text_banner',['uses'=>'AdminBannerController@updateTextBanner']);
    Route::post('/updateBannerStatus',['uses'=>'AdminBannerController@status_update']);
    Route::resource('regions', 'RegionController');
    Route::post( '/delete-region', [ 'uses' => 'RegionController@destroy' ] );
	Route::post( '/update-region', [ 'uses' => 'RegionController@update' ] );
    Route::any( '/search-region', [ 'uses' => 'RegionController@search' ] );
    Route::resource('postcodes', 'ShippingController');
    Route::post('/getregions', [ 'uses' => 'RegionController@getregions' ]);
    Route::post( '/update-postcode', [ 'uses' => 'ShippingController@update' ] );
    Route::post( '/delete-postcode', [ 'uses' => 'ShippingController@destroy' ] );
    Route::any( '/search-postcode', [ 'uses' => 'ShippingController@search' ] );
});