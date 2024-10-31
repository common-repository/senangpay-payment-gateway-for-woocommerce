<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class senangpay_Gateway_Blocks extends AbstractPaymentMethodType {
    private $gateway;

    protected $name = 'senangPay'; // your payment gateway name

    public function initialize() {
        $this->settings = get_option('woocommerce_senangpay_gateway_settings', []);
        $this->gateway = new Senangpay_Gateway();
    }

    public function is_active() {
        return $this->gateway->is_available();
    }

    public function get_payment_method_script_handles() {
        wp_register_script(
            'senangpay-gateway-blocks-integration',
            plugin_dir_url(__FILE__) . 'js/checkout.js',
            [
                'wc-blocks-registry',
                'wc-settings',
                'wp-element',
                'wp-html-entities',
                'wp-i18n',
            ],
            null,
            true
        );

        if (function_exists('wp_set_script_translations')) {
            wp_set_script_translations('senangpay-gateway-blocks-integration');
        }

        // Localize script with necessary data
        wp_localize_script(
            'senangpay-gateway-blocks-integration', // Script handle
            'senangPayGatewayParams', // Object name
            [
                'pluginUrl' => plugin_dir_url(__FILE__), // Plugin URL for dynamic resource loading
            ]
        );

        // Enqueue the script
        wp_enqueue_script('senangpay-gateway-blocks-integration');

        return ['senangpay-gateway-blocks-integration'];
    }

    public function get_payment_method_data() {
        return [
            'title' => $this->gateway->title,
            'description' => $this->gateway->description,
            'imageUrl' => $this->gateway->icon,
        ];
    }

}
?>
