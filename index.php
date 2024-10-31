<?php

/**
 * Plugin Name: senangPay
 * Plugin URI: http://senangpay.my
 * Description: Enable online payments using credit or debit cards and online banking. Currently, senangPay service is only available to businesses that reside in Malaysia.
 * Version: 3.3.5
 * Author: senangPay
 * Author URI: http://senangpay.my
 * WC requires at least: 4.3
 * WC tested up to: 6.5.5
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('plugins_loaded', 'senangpay_init', 0);

function senangpay_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    include_once('src/class-gateway.php');

    add_filter('woocommerce_payment_gateways', 'add_senangpay_gateway');

    function add_senangpay_gateway($methods)
    {
        $methods[] = 'Senangpay_Gateway';
        return $methods;
    }

}

// Add custom action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'senangpay_links');

function senangpay_links($links)
{
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=senangpay') . '">' . __('Settings', 'senangpay') . '</a>',
    );

    return array_merge($plugin_links, $links);
}

add_action( 'init', 'senangpay_check_response', 15 );
	
function senangpay_check_response() {
    # If the parent WC_Payment_Gateway class doesn't exist it means WooCommerce is not installed on the site, so do nothing
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
            return;
    }

    include_once( 'src/class-gateway.php' );

    $senangpay = new Senangpay_Gateway();
    $senangpay->check_senangpay_response();
}

function senangpay_hash_error_msg( $content ) {
    return '<div class="woocommerce-error">The data that we received is invalid. Thank you.</div>' . $content;
}

function senangpay_payment_declined_msg( $content ) {
   return '<div class="woocommerce-error">The payment was declined. Please check with your bank. Thank you.</div>' . $content;
}

function senangpay_success_msg( $content ) {
    return '<div class="woocommerce-info">The payment was successful. Thank you.</div>' . $content;
}

add_action('before_woocommerce_init', 'before_woocommerce_hpos');

function before_woocommerce_hpos()
{

    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {

        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
}

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature 
 */
function declare_cart_checkout_blocks_compatibility()
{
    // Check if the required class exists
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        // Declare compatibility for 'cart_checkout_blocks'
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
}

// Hook the custom function to the 'before_woocommerce_init' action
add_action('before_woocommerce_init', 'declare_cart_checkout_blocks_compatibility');

// Hook the custom function to the 'woocommerce_blocks_loaded' action
add_action('woocommerce_blocks_loaded', 'oawoo_register_order_approval_payment_method_type');

/**
 * Custom function to register a payment method type
 */
function oawoo_register_order_approval_payment_method_type()
{
    // Check if the required class exists
    if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        return;
    }
    // Include the custom Blocks Checkout class
    include_once('src/class-block.php');
    // Hook the registration function to the 'woocommerce_blocks_payment_method_type_registration' action
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
            // Register an instance of senangPay_Gateway_Blocks
            $payment_method_registry->register(new senangpay_Gateway_Blocks);
        }
    );
}
