<?php
//
// Recommended way to include parent theme styles.
//  (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
//  
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
	wp_enqueue_style( 'bs_new-style',
        get_stylesheet_directory_uri() . '/bootstrap.min.css',
        array('parent-style')
    );
	wp_enqueue_style( 'new-style',
        get_stylesheet_directory_uri() . '/bootstrapdatepicker.css',
        array('parent-style')
    );
	wp_enqueue_style( 'custom-style',
		get_stylesheet_directory_uri() . '/custom.css',
		array('parent-style')
	);
    /*wp_enqueue_script('pp_bs_script', get_stylesheet_directory_uri().'/jquery.min.js');
	wp_enqueue_script('pp_bs_script', get_stylesheet_directory_uri().'/bootstrap.min.js');
	wp_enqueue_script('pp_script', get_stylesheet_directory_uri().'/bootstrap-datepicker.min.js');
	wp_enqueue_script('custom_script', get_stylesheet_directory_uri().'/custom.js');*/
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
function theme_enqueue_scripts() {
//    wp_enqueue_script( 'my-great-script', get_template_directory_uri() . '/js/my-great-script.js', array( 'jquery' ), '1.0.0', true );
//    wp_enqueue_script('pp_bs_script', get_stylesheet_directory_uri().'/jquery.min.js');
    wp_enqueue_script('pp_bs_script', get_stylesheet_directory_uri().'/bootstrap.min.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script('pp_script', get_stylesheet_directory_uri().'/bootstrap-datepicker.min.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script('custom_script', get_stylesheet_directory_uri().'/custom.js', array( 'jquery' ), '1.0.0', true );

}
//
// Your code goes below
//