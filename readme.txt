=== senangpay ===
Contributors: senangpay
Tags: payment gateway, Malaysia, online banking
Requires at least: 4.3
Tested up to: 6.6
Stable tag: 3.3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
	
senangPay payment gateway plugin for WooCommerce.

== Description ==
	
senangPay payment gateway plugin for WooCommerce. This plugin enable online payment using credit or debit cards (Visa and Mastercard only) and online banking (for Malaysian banks only). Currently senangPay is only available for businesses that reside in Malaysia.
	
== Installation ==
	
1. Make sure that you already have WooCommerce plugin installed and activated.
2. From your Wordpress admin dashboard, go to menu 'Plugins' and 'Add New'.
3. Key in 'senangPay' in the 'Search Plugins' field and press enter.
4. It will display the plugin and press intall.
5. Activate the plugin through the 'Plugins' screen in WordPress.
6. Go to menu WooCommerce, settings, Checkout, senangPay and fill in your merchant id and secret key. You can retrieve the merchant id and secret key from senangPay Dashboard at https://app.senangpay.my.
7. By default the hash type will be md5, but you may change it later to sha256 which is strongly recommended by senangPay.You may refer to the hash type defined in senangPay dashboard.
8. The environment mode by default is live. However if your website is not ready for production but for testing, you may choose sandbox (This required you to have sandbox account). 
9. Select your preferred payment icon, which will display different checkout image options based on the payment method chosen.
10. Make sure the 'Enable this payment gateway' is ticked. Click on 'Save changes' button.
11. In senangPay Dashboard make sure you key in your return URL and callback URL as http://your_domain/checkout/ and choose 'Read response and send email if error' for callback response, finally press Save. Please leave the 'Return URL Parameters' field empty.
	
== Frequently Asked Questions ==
	
= Do I need to sign up with senangPay in order to use this plugin? =
	
Yes, we require info such as merchant id and secret key that is only available after you sign up with senangPay.
	
= Can I use this plugin without using WooCommerce? =
	
No.
	
= What currency does it support? =
	
Currently senangPay only support Malaysian Ringgit (RM).
	
= What if I have some other question related to senangPay? =
	
Please open a ticket by log in to senangPay Dashboard and look for menu support.
	
== Changelog ==

= 3.3.5 =
* Bug Fixes: Change UI payment icons

= 3.3.4 =
* Bug Fixes: Addressed various minor bugs to enhance overall stability.
* New Feature: Added a "Choose Preferred Payment Icon" option. Admins can now select different payment icons that dynamically display corresponding checkout images based on the selected payment method, enhancing the user experience during checkout.

= 3.3.3 =
* fix issue with secret_key not properly initialized

= 3.3.2 =
* bug fixes
* update version
* update README

= 3.3.1 =
* update supported version
* supports Wordpress 6.6
* supports Woocommerce  9.1.2 

= 3.2.1 =
* bug fixes

= 3.1.3 =
* update supported version
* supports Wordpress 5.8.1
* supports Woocommerce  5.6.0 

= 3.1.2 =
* supports Wordpress 5.5
* supports Woocommerce 4.4.1

= 3.1.1 =
* update installation steps in README

= 3.1.0 =
* fix issue with hash_type not properly initialized

= 3.0.9 =
* fix issue with environment_mode not properly initialized
* supports wordpress 5.4.1

= 3.0.8 =
* set default value of Environment Mode to Live

= 3.0.7 =
* update version tagging and README
		
= 3.0.6 =
* supports SHA256 hash_hmac encryption
* to use SHA256 encryption, merchant must set the same hash type in senangPay dashboard
* added mode for Sandbox or Live

= 3.0.5 =
* update README
	
= 3.0.4 =
* fix versioning
	
= 3.0.3 =
* fix issue with plugin not initialized properly
* supports Wordpress 4.9.x
* supports Woocommerce 3.3.x
	
= 3.0.2 =
* Fix issue with orders with the same number
	
= 3.0.1 =
* Fix issue with failed payments
	
= 3.0.0 =
* initial version for Woocommerce 3.x full compatibility
* remove usage of WC deprecated functions
* wp_redirect doesn't always redirect. Add exit() to ensure redirection
* added proper callback response
	
= 2.1 =
* Send billing name, email and phone to senangPay so that customer does not have to re-enter at senangPay payment form
	
= 2.0 =
* Solve the issue where multiple emails were sent to both buyer and seller after payment is complete.
* Upon successful payment customer will see order complete page.
