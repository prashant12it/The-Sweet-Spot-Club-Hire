<?php

return [
    'BaseURL' => (getenv('APP_ENV') == 'local' ? 'http://localhost:8000' :(getenv('APP_ENV') == 'live'?'https://www.tssclubhire.com/shop': 'http://booking.tssclubhire.com')),
    'PaginationRowsPerPage' => 5,
    'supportEmailLocal' => 'info@tssclubhire.com',
    'customerSupportEmail' => 'info@tssclubhire.com',
    'CCcustomerSupportEmail' => 'info@tssclubcourier.com',
    'supportEmailProduction' => 'fiachra.mccloskey@gmail.com',
    'InsurancePrice' => 10.00,
    'TssDiscount' => 0,
    'FreightPrice' => 35,
    'PaymentSwitch' => 'ON',// change it to ON to enable payment
    'PromotionProductId' => (getenv('APP_ENV') == 'local' ? 92 : 92),
    'SiteUrl' => (getenv('APP_ENV') == 'local' ? 'http://localhost:8000' :(getenv('APP_ENV') == 'live'?'https://www.tssclubhire.com/shop': 'http://booking.tssclubhire.com')),
    'productType' => [
        '1' => 'Simple',
        '2' => 'Variable',
        '4' => 'Set Type'
    ],
    'stateServicingDays' => [
        '1' => 3,
        '2' => 4,
        '3' => 4,
        '4' => 6,
        '5' => 5,
        '6' => 3,
        '7' => 4,
        '8' => 4
    ],
    'stateEstimatedShipping' => [
        '1' => 20,
        '2' => 25,
        '3' => 25,
        '4' => 25,
        '5' => 30,
        '6' => 0,
        '7' => 15,
        '8' => 15
    ],
    'XeroDetails' => [
        'consumerKey' => 'GVMIBXNWCWZD3LDXDDLGL3OS35GKGP',
        'consumerSecret' => 'XHUJ0JZ8JYRXHASLXTVDVBK6RP6QFV'
    ],
    /*'Default_Filter'         => ( getenv( 'APP_ENV' ) == 'local' ? array( 4, 7, 8, 11, 12 ) : array(
        55,
        53,
        28,
        58,
        59
    ) ),*/
    'Default_Filter' => (getenv('APP_ENV') == 'local' ? array(59, 55, 28, 58, 0) : array(59, 55, 28, 58, 0)),
    'DbTables' => [
        'Attributes' => 'attributes',
        'Attributes_Values' => 'attribute_vals',
        'Booked_Products' => 'booked_product_inventory_map',
        'Banners' => 'banners',
        'Categories' => 'categories',
        'Countries' => 'tbl_countries',
        'Email_CMS' => 'tbl_email_cms',
        'Email_Log' => 'tbl_email_log',
        'Group_Products' => 'group_products',
        'Migrations' => 'migrations',
        'Newsletter' => 'news_letter_subscription',
        'Orders' => 'orders',
        'Orders_Products' => 'orders_products_map',
        'Order_Status' => 'order_status',
        'Offers' => 'offers',
        'Partners' => 'partners',
        'Partner_Clicks' => 'tbl_partner_clicks',
        'Products' => 'products',
        'Products_Attribute_Mapping' => 'product_attrib_map',
        'Pre_Orders' => 'pre_orders',
        'Pre_Orders_Products' => 'pre_orders_products_map',
        'Product_Gallery' => 'product_gallery',
        'Parent_Product_Attributes' => 'parent_prod_attrib',
        'Regions' => 'regions',
        'Reset_Password' => 'password_resets',
        'Rent' => 'rent',
        'Rent_Set' => 'rent_set',
        'Set_Pro_Map' => 'set_prod_mapping',
        'Shipping' => 'shipping',
        'States' => 'tbl_states',
        'TSS' => 'tss',
        'Users' => 'users',
        'Upsell_Products' => 'upsell_products',
        'CCVouchers' => 'clubcourier_vouchers',
        'CCOrders' => 'clubcourier_orders',
        'CCOrders_Products' => 'clubcourier_orders_products_map',
        'CCCost' => 'cc_cost',
        'CCRegion' => 'cc_region'
    ],
    'HireDaysPricing' => [
        '1' => 70,
        '2' => 95,
        '3' => 115,
        '4' => 135,
        '5' => 155,
        '6' => 175,
        '7' => 195,
        '8' => 205,
        '9' => 215,
        '10' => 225
    ],
    'AdditionalPrice' => 10,
    'Discount' => [
        '1' => 0,
        '2' => 5,
        '3' => 5,
        '4' => 10,
        '5' => 10,
        '6' => 10,
        '7' => 15,
        '8' => 15,
        '9' => 15,
        '10' => 15,
        '11' => 15
    ],
    'CCDiscount' => [
        '1' => 0,
        '2' => 10,
        '3' => 10,
        '4' => 10,
        '5' => 20,
        '6' => 20,
        '7' => 20,
        '8' => 30,
        '9' => 30,
        '10' => 30,
        '11' => 30
    ],
    'PaydollarConfig' => [
        'PostUrl' => 'https://www.paydollar.com/b2c2/',
        'merchantId' => '16000287',
        'currCode' => '036',
    ],
    'Gift' => [
        //Days => Product id
        '7' => 92,
        '3' => 94,
        '2' => 95
    ],
    'NAB-Transact-Config' => [
//        'PostUrl' => "https://demo.transact.nab.com.au",
        'PostUrl' => "https://transact.nab.com.au",
        'vendorid' => '4S30010'
    ],
    'PartnerConfig' => [
        'CommissionPercentage' => 5,
        'DiscountPercentage' => 10
    ]
];