<?php
/**
 * electro engine room
 * ACABO DE AGREGAR ESTA LINEA xd
 *
 * @package electro
 */

/**
 * Initialize all the things.
 */
require get_template_directory() . '/inc/init.php';

/**
 * Note: Do not add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * http://codex.wordpress.org/Child_Themes
 */
 
 /**
 * Note: Do not add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * http://codex.wordpress.org/Child_Themes
 */
/*checked on the checkout page*/
 function apply_default_check() 
{
    return 1;
}
add_filter( 'woocommerce_terms_is_checked_default', 'apply_default_check' );
/*add_filter('woocommerce_create_account_default_checked' , function ($checked){
    return true;
});*/

/*wc_braintree_credit_card_default_tokenize_payment_method_checkbox_to_checked
  wc-authorize-net-cim-credit-card-tokenize-payment-method_checkbox_to_checked*/
add_filter( 'wc_authorize_net_cim_credit_card_default_tokenize_payment_method_checkbox_to_checked', '__return_true' );
  /** * Enqueue theme styles (parent first, child second) *  */
function custom_theme_styles() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/custom-lios-css.css' );
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/assets/css/custom-lios-css.css' );
	wp_enqueue_style( 'elegant-font', get_template_directory_uri() . '/assets/fonts/elegant-icons/style.css' );
	wp_enqueue_script( 'script', get_template_directory_uri() . '/custom-lios-js.js', array ( 'jquery' ), 1.1, true);
	}
add_action( 'wp_enqueue_scripts', 'custom_theme_styles' );

/*
 * Disable Rest API
 */
// Disable REST API link tag
remove_action('wp_head', 'rest_output_link_wp_head', 10);

// Disable oEmbed Discovery Links
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

// Disable REST API link in HTTP headers
remove_action('template_redirect', 'rest_output_link_header', 11, 0);

/*adding sku to the thankyou pages*/
/**
 * Adds product SKU to the WooCommerce order details page
 * Uses WooCommerce 2.5 or newer
 */
add_action( 'woocommerce_add_order_item_meta', 'lb_add_item_sku', 10, 3 );
function lb_add_item_sku( $item_id, $values, $cart_item_key ) {

  $item_sku = get_post_meta( $values[ 'product_id' ], '_sku', true );

  wc_add_order_item_meta( $item_id, 'sku', $item_sku , false );

}
/*adding coupon on url*/
add_action('wp_footer', 'get_custom_coupon_code_to_session');
function get_custom_coupon_code_to_session(){
    if( isset($_GET['coupon_code']) ){
        $coupon_code = WC()->session->get('coupon_code');
        if(empty($coupon_code)){
            $coupon_code = esc_attr( $_GET['coupon_code'] );
            WC()->session->set( 'coupon_code', $coupon_code ); // Set the coupon code in session
        }
    }
}

// test
// Change post preview button url 
// Change www.cyberciti.biz/faq/?p=124&preview=true
// To server1.cyberciti.biz/faq/?p=124&preview=true
/*function nixcraft_preview_link() {
    $slug = basename(get_permalink());
    $mydomain = 'https://bookstore.accessthedr.com/';
    $mydir = '/faq/'; 
    $mynewpurl = "$mydomain$slug&amp;preview=true";
    return "$mynewpurl";
}
add_filter( 'preview_post_link', 'nixcraft_preview_link' );*/

/*********************************/
//*******************************************

add_action( 'woocommerce_before_checkout_form', 'add_discout_to_checkout', 10, 0 );
function add_discout_to_checkout( ) {

    $coupon_code = WC()->session->get('coupon_code');
    if ( ! empty( $coupon_code ) && ! WC()->cart->has_discount( $coupon_code ) ){
        WC()->cart->add_discount( $coupon_code ); // apply the coupon discount
        WC()->session->__unset('coupon_code'); // remove coupon code from session
    }
}

# Automatically clear autoptimizeCache if it goes beyond 256MB
/**if (class_exists('autoptimizeCache')) {
    *$myMaxSize = 256000; # You may change this value to lower like 100000 for 100MB if you have limited server space
    *$statArr=autoptimizeCache::stats(); 
    *$cacheSize=round($statArr[1]/1024);
    *
    *if ($cacheSize>$myMaxSize){
    *   autoptimizeCache::clearall();
    *   header("Refresh:0"); # Refresh the page so that autoptimize can create new cache files and it does breaks the page after clearall.
    *}
}*/
/***************************/
/**
 * Remove password strength check.
 */
/**function iconic_remove_password_strength() {
*    wp_dequeue_script( 'wc-password-strength-meter' );
*}
*add_action( 'wp_print_scripts', 'iconic_remove_password_strength', 10 );
*/

/*--------------All of this is just masking wp-login.php----------------*/
/*********************************/
/******forced /login/ */
/*********************************/

/*function custom_login()
{
    if( $GLOBALS['pagenow'] === 'wp-login.php') {
        wp_redirect('https://bookstore.accessthedr.com/my-account/');
        exit();
    }
}

add_action('init','custom_login');
*/

/******forced /logout/ */

function custom_logout() 
{
    // set your URL here
    return 'https://bookstore.accessthedr.com/my-account/customer-logout/';
}

add_filter( 'wp_logout_url', 'custom_logout' );

/**
 * Replace default log-out URL.
 *
 * @wp-hook logout_url
 * @param   string $logout_url
 * @param   string $redirect
 * @return  string
 */

function t5_custom_logout_url( $logout_url, $redirect )
{
    $url = add_query_arg( 'logout', 1, home_url( '/' ) );

    if ( ! empty ( $redirect ) )
    $url = add_query_arg( 'redirect', $redirect, $url );

    return $url;
}

add_filter( 'logout_url', 't5_custom_logout_url', 10, 2);

/**
 * Log the user out.
 *
 * @wp-hook wp_loaded
 * @return  void
 */

function t5_custom_logout_action()
{
    if ( ! isset ( $_GET['logout'] ) )
        return;

    wp_logout();

    $loc = isset ( $_GET['redirect'] ) ? $_GET['redirect'] : home_url( '/' );
    wp_redirect( $loc );
    exit;
}

add_action( 'wp_loaded', 't5_custom_logout_action' );

/***********/

function logout_function() {
   
	setcookie('wp_user_logged_in', '', time()-3600, '/my-library/', 'bookstore.accessthedr.com');
}
add_action('woocommerce_before_customer_login_form', 'logout_function');

/***********/


function login_function() {
     	
    setcookie('wp_user_logged_in', 'my-library', 0, '/my-library/', 'bookstore.accessthedr.com');
    //$_COOKIE['wp_user_logged_in'] =  'my-library';
}
add_action('woocommerce_account_dashboard', 'login_function');
//add_action('wp_login', 'login_function');
//add_action('woocommerce_checkout_after_customer_details', 'login_function');
add_action('woocommerce_after_checkout_form', 'login_function');



function bartag_func() {
	 $current_user = wp_get_current_user();
   
    
  $current_user->user_email;
   $current_user->user_firstname;
    $current_user->user_lastname;
    $current_user->display_name;
   $current_user->ID;
	
	
	return "https://bookstore.accessthedr.com/my-library/publishers-circle.php?email=".$current_user->user_email;
}
add_shortcode( 'bartag', 'bartag_func' );



add_action('woocommerce_after_checkout_form','restict_registration_for_some_products');
function restict_registration_for_some_products() {
    if(!is_user_logged_in()){ //Check if the user is logged in
    $retricted_ids = array(300708, 300517);
        if( isset( $retricted_ids ) && $retricted_ids != null ) {
            /*$product_cart_id = WC()->cart->generate_cart_id( $retricted_ids );
            $in_cart = WC()->cart->find_product_in_cart( $product_cart_id );

            if ( $in_cart ) {
                
                add_filter('woocommerce_create_account_default_checked', '__return_true');
                echo "<script language='javascript'>
                        document.getElementById('createaccount').checked=true
                        document.getElementById('createaccount').disabled=true
                    </script>";
            }*/
            if ( ! WC()->cart->is_empty() ) {
                // Loop though cart items
                foreach(WC()->cart->get_cart() as $cart_item ) {
                    // Handling also variable products and their products variations
                    $cart_item_ids = array($cart_item['product_id'], $cart_item['variation_id']);

                    // Handle a simple product Id or an array of product Ids 
                    if( ( is_array($retricted_ids) && array_intersect($retricted_ids, $cart_item_ids) ) 
                    || ( !is_array($retricted_ids) && in_array($retricted_ids, $cart_item_ids))){
                        echo "<script language='javascript'>
                        document.getElementById('createaccount').checked=true
                        document.getElementById('createaccount').disabled=true
                        </script>";
                        wc_add_notice( __("A product in the cart requires creating an account", "woocommerce"), 'error' );
                    }
                }
            }
        }
    }
}






/*--------------All of this WAS just masking wp-login.php----------------*/


#function wc_custom_user_redirect( $redirect, $user ) {
	// Get the first of all the roles assigned to the user
#	$role = $user->roles[0];
#	$dashboard = admin_url();
#   $specificurl = $_SERVER['HTTP_REFERER'];
#	$myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );
#	if( $role == 'administrator' ) {
		//Redirect administrators to the dashboard
#		$redirect = $dashboard;
#	} elseif ( $role == 'shop-manager' ) {
		//Redirect shop managers to the dashboard
#		$redirect = $dashboard;
#	} elseif ( $role == 'editor' ) {
		//Redirect editors to the dashboard
#		$redirect = $dashboard;
#	} elseif ( $role == 'author' ) {
		//Redirect authors to the dashboard
#		$redirect = $dashboard;
#	} elseif ( $role == 'customer' || $role == 'subscriber' ) && $specificurl == 'https://bookstore.accessthedr.com/my-account/' {
		//Redirect customers and subscribers to the "My Account" page
#		$redirect = 'https://bookstore.accessthedr.com/my-account/orders/';
#	} else {
		//Redirect any other role to the previous visited page or, if not available, to the home
#		$redirect = wp_get_referer() ? wp_get_referer() : home_url();
#	}
#	return $redirect;
#}
#add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 10, 2 );