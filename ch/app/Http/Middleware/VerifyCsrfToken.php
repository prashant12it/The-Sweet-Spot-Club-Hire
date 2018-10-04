<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin',
        'filterProducts',
        'clubsearch',
        'changepassword',
        'callmethodbyrequest',
        'addremoveinsurance',
        'calculateshipping',
        'insurance',
        'remove-order',
        'clearfilter',
        'getcartprodids',
        'remove-from-cart',
        'decrease-prod-qty',
        'increase-prod-qty',
        'addtocart',
        'add_shipping',
        'subscribe',
        'getfooter',
        'updatepaymentopt',
        'getAjaxPreorderDetails',
        'partner/validate_login',
        'partner/send_forgot_password',
        'partner/saveNewPassword',
        'partner/signup_partner',
        'partner/updateProfile',
        'partner/update_password',
        'dashboard',
        'add_product',
        'update_product',
        'delete_product',
        'getCategoryProds',
        'savecaption',
        'add_attribute',
        'update_attribute',
        'delete_attribute',
        'option_add/*',
        'option_edit/*',
        'delete_attribute_option',
        'add_sets',
        'delete_set',
        'edit_set',
        'search_inventory',
        'search_order',
        'club_courier_search_order',
        'update_order_status',
        'ccupdate_order_status',
        'create_cc_order_by_admin',
        'create_order_by_admin',
        'search_offer',
        'add_offer',
        'edit_offer',
        'delete_offer',
        'search_partner',
        'add_partner',
        'edit_partner',
        'updatePartnerStatus',
        'deleteBanner',
        'search_banner',
        'add_banner',
        'edit_banner',
        'updateBannerStatus',
        'delete-region',
        'update-region',
        'search-region',
        'getregions',
        'update-postcode',
        'delete-postcode',
        'search-postcode',
        'login',
        'thankyou',
        '/clubcourier/courier_booking',
        '/clubcourier/thankyou'
    ];
}
