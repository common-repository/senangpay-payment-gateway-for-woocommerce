<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Senangpay_Gateway extends WC_Payment_Gateway {

    public function __construct() {
        $this->id = 'senangpay';
        $this->method_title = __('senangPay', 'senangpay');
        $this->method_description = __('Enable payments using senangPay.', 'senangpay');
        $this->has_fields = true;
		$this->title = __( "senangPay", 'senangPay' );
		$this->hash_type = 'md5';
		$this->environment_mode = 'live';

        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        // Define user settings
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->merchant_id = $this->get_option('merchant_id');
        $this->secret_key = $this->get_option('secret_key');
        $this->hash_type = $this->get_option('hash_type', 'md5');
        $this->environment_mode = $this->get_option('environment_mode', 'live');
        foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
		}

        // Save settings
		if ( is_admin() ) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
    
        // Hook into WooCommerce settings only once
        add_filter('woocommerce_get_settings_checkout', array($this, 'add_custom_payment_gateway_setting'), 10, 2);
	}

	public function add_custom_payment_gateway_setting($settings, $gateway_id) {
		if ($gateway_id === $this->id) { // Replace with your gateway ID
			// Check if the custom section already exists to avoid adding it multiple times
			foreach ($settings as $setting) {
				if (isset($setting['id']) && $setting['id'] === 'custom_payment_icons_options') {
					return $settings; // Return early if the section already exists
				}
			}
	
			// Add a new section
			$settings[] = array(
				'type'     => 'title',
				'id'       => 'custom_payment_icons_options',
			);
	
			// Add custom payment icon field
			$settings[] = array(
				'title'       => __('Payment Icons', 'senangpay'),
				'desc'        => __('Select the payment icon to display.', 'senangpay'),
				'id'          => 'woocommerce_senangpay_icon',
				'type'        => 'radio',
				'options'     => array(
					plugin_dir_url(__FILE__) . 'img/minimal.png' =>'',
					plugin_dir_url(__FILE__) . 'img/e-wallet.png' => '',
					plugin_dir_url(__FILE__) . 'img/default.png' => '',
					plugin_dir_url(__FILE__) . 'img/bnpl.png' => '',
					plugin_dir_url(__FILE__) . 'img/basic.png' => '',
				),
				'desc_tip'    => true,
			);
	
			// Add a section end
			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'custom_payment_icons_options',
			);
		}
		return $settings;
	}

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'       => __('Enable/Disable', 'senangpay'),
                'label'       => __('Enable senangPay', 'senangpay'),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no',
            ),
            'title' => array(
                'title'       => __('Title', 'senangpay'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'senangpay'),
                'default'     => __('senangPay', 'senangpay'),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __('Description', 'senangpay'),
                'type'        => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', 'senangpay'),
                'default'     => __('Pay securely using your credit card or online banking through senangPay.', 'senangpay'),
            ),
            'merchant_id' => array(
                'title'       => __('Merchant ID', 'senangpay'),
                'type'        => 'text',
                'description' => __('This is the Merchant ID provided by senangPay.', 'senangpay'),
            ),
            'secret_key' => array(
                'title'       => __('Secret Key', 'senangpay'),
                'type'        => 'password',
                'description' => __('This is the Secret Key provided by senangPay.', 'senangpay'),
            ),
            'hash_type' => array(
                'title' => __('Hash Type', 'senangPay'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __('Choose whether you wish to use encryption md5 or sha256.', 'senangPay'),
                'default' => 'md5',
                'desc_tip' => true,
                'options' => array(
                    'md5' => __('md5', 'senangPay'),
                    'sha256' => __('sha256', 'senangPay'),
                ),
            ),
            'environment_mode' => array(
                'title' => __('Environment Mode', 'senangPay'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __('Choose whether you wish to use sandbox or production mode.', 'senangPay'),
                'default' => 'live',
                'desc_tip' => true,
                'options' => array(
                    'live' => __('Live', 'senangPay'),
                    'sandbox' => __('Sandbox', 'senangPay'),
                ),
            ),
			'icon' => array(
                'title' => __('Payment Icons', 'senangpay'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
				'description'        => __('Select the payment icon to display.', 'senangpay'),
                'options' => array(
                    plugin_dir_url(__FILE__) . 'img/minimal.png' => 'Minimal',
                    plugin_dir_url(__FILE__) . 'img/e-wallet.png' => 'e-Wallet',
                    plugin_dir_url(__FILE__) . 'img/default.png' => 'Default',
                    plugin_dir_url(__FILE__) . 'img/bnpl.png' => 'Bnpl',
                    plugin_dir_url(__FILE__) . 'img/basic.png' => 'Basic',
                ),
                'default' => plugin_dir_url(__FILE__) . 'img/default.png',
                'desc_tip' => true,
            ),
        );
    }

    # Submit payment
	public function process_payment( $order_id ) {
		# Get this order's information so that we know who to charge and how much
		$customer_order = wc_get_order( $order_id );

		# Prepare the data to send to senangPay
		$detail = "Payment_for_order_" . $order_id;

		$old_wc = version_compare( WC_VERSION, '3.0', '<' );

		if ( $old_wc ) {
			$order_id = $customer_order->id;
			$amount   = $customer_order->order_total;
			$name     = $customer_order->billing_first_name . ' ' . $customer_order->billing_last_name;
			$email    = $customer_order->billing_email;
			$phone    = $customer_order->billing_phone;
		} else {
			$order_id = $customer_order->get_id();
			$amount   = $customer_order->get_total();
			$name     = $customer_order->get_billing_first_name() . ' ' . $customer_order->get_billing_last_name();
			$email    = $customer_order->get_billing_email();
			$phone    = $customer_order->get_billing_phone();
		}

		if ($this->hash_type == 'md5') 
			$hash_value = md5( $this->secret_key . $detail . $amount . $order_id );
		else
			$hash_value = hash_hmac('sha256', $this->secret_key . $detail . $amount . $order_id, $this->secret_key );

		$post_args = array(
			'detail'   => $detail,
			'amount'   => $amount,
			'order_id' => $order_id,
			'hash'     => $hash_value,
			'name'     => $name,
			'email'    => $email,
			'phone'    => $phone
		);

		# Format it properly using get
		$senangpay_args = '';
		foreach ( $post_args as $key => $value ) {
			if ( $senangpay_args != '' ) {
				$senangpay_args .= '&';
			}
			$senangpay_args .= $key . "=" . $value;
		}

		if ($this->environment_mode == 'sandbox') 
			$environment_mode_url = 'https://sandbox.senangpay.my/payment/';
		else
			$environment_mode_url = 'https://app.senangpay.my/payment/';

		return array(
			'result'   => 'success',
			'redirect' =>  $environment_mode_url . $this->merchant_id . '?' . $senangpay_args
		);
	}

    public function check_senangpay_response() {
		if ( isset( $_REQUEST['status_id'] ) && isset( $_REQUEST['order_id'] ) && isset( $_REQUEST['msg'] ) && isset( $_REQUEST['transaction_id'] ) && isset( $_REQUEST['hash'] ) ) {
			global $woocommerce;

			$is_callback = isset( $_POST['order_id'] ) ? true : false;

			$order = wc_get_order( $_REQUEST['order_id'] );

			$old_wc = version_compare( WC_VERSION, '3.0', '<' );

			$order_id = $old_wc ? $order->id : $order->get_id();

			if ( $order && $order_id != 0 ) {
				# Check if the data sent is valid based on the hash value

				if ($this->hash_type == 'md5') 
					$hash_value = md5( $this->secret_key . $_REQUEST['status_id'] . $_REQUEST['order_id'] . $_REQUEST['transaction_id'] . $_REQUEST['msg'] );
				else
					$hash_value = hash_hmac('sha256',  $this->secret_key . $_REQUEST['status_id'] . $_REQUEST['order_id'] . $_REQUEST['transaction_id'] . $_REQUEST['msg'] , $this->secret_key);				
				
				if ( $hash_value == $_REQUEST['hash'] ) {
					if ( $_REQUEST['status_id'] == 1 || $_REQUEST['status_id'] == '1' ) {
						if ( strtolower( $order->get_status() ) == 'pending' || strtolower( $order->get_status() ) == 'processing' ) {
							# only update if order is pending
							if ( strtolower( $order->get_status() ) == 'pending' ) {
								$order->payment_complete();

								$order->add_order_note( 'Payment successfully made through senangPay. Transaction reference is ' . $_REQUEST['transaction_id'] );
							}

							if ( $is_callback ) {
								echo 'OK';
							} else {
								# redirect to order receive page
								wp_redirect( $order->get_checkout_order_received_url() );
							}

							exit();
						}
					} else {
						if ( strtolower( $order->get_status() ) == 'pending' ) {
							if ( ! $is_callback ) {
								$order->add_order_note( 'Payment was unsuccessful' );
								add_filter( 'the_content', 'senangpay_payment_declined_msg' );
							}
						}
					}
				} else {
					add_filter( 'the_content', 'senangpay_hash_error_msg' );
				}
			}

			if ( $is_callback ) {
				echo 'OK';

				exit();
			}
		}
	}

	# Validate fields, do nothing for the moment
	public function validate_fields() {
        // Validate if required fields are set
        if (empty($this->merchant_id) || empty($this->secret_key)) {
            wc_add_notice(__('senangPay settings are incomplete.', 'senangpay'), 'error');
            return false;
        }
        return true;
	}

	# Check if we are forcing SSL on checkout pages, Custom function not required by the Gateway for now
	public function do_ssl_check() {
		if ( $this->enabled == "yes" ) {
			if ( get_option( 'woocommerce_force_ssl_checkout' ) == "no" ) {
				echo "<div class=\"error\"><p>" . sprintf( __( "<strong>%s</strong> is enabled and WooCommerce is not forcing the SSL certificate on your checkout page. Please ensure that you have a valid SSL certificate and that you are <a href=\"%s\">forcing the checkout pages to be secured.</a>" ), $this->method_title, admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) . "</p></div>";
			}
		}
	}

    public function is_available() {
        if ('yes' === $this->get_option('enabled')) {
            if ('MYR' !== get_woocommerce_currency()) {
                return false;
            }
            return true;
        }
        return false;
    }
}
?>
