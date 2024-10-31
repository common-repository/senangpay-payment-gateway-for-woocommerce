const settings = window.wc.wcSettings.getSetting( 'senangPay_data', {} );
const label = settings.title || 'senangPay'; // Default to 'senangPay' if title is undefined
const description = settings.description || 'Pay securely using your credit card or online banking through senangPay.';
const imageUrl = settings.imageUrl; //|| senangPayGatewayParams.pluginUrl + '/img/payment_method.png';

const Content = () => {
    return window.wp.element.createElement('div', null,
        window.wp.element.createElement('p', null, description) // Add the description here
    );
};

const OptionLayout = () => {
    return window.wp.element.createElement('div', { style: { width: '100%' } },
        window.wp.element.createElement('img', {
            src: imageUrl,
            alt: label,
            style: { 
                float: 'right',  // Adjust width as needed
                marginRight: '20px',   
            }
        }),
        window.wp.element.createElement('span', null, label) // Display the label here
    );
};

const Block_Gateway = {
    name: 'senangpay',
    label: window.wp.element.createElement(OptionLayout, null), 
    content: window.wp.element.createElement(Content, null),
    edit: window.wp.element.createElement(Content, null),
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};

// Register the senangPay payment method block
if (window.wc && window.wc.wcBlocksRegistry) {
    window.wc.wcBlocksRegistry.registerPaymentMethod(Block_Gateway);
} else {
    console.error('WooCommerce Blocks not found or wcBlocksRegistry not available.');
}

jQuery(document).ready(function($) {
    // Adjust the selector based on your actual admin page structure
    var radioButtons = $('input[type="radio"][name="woocommerce_senangpay_icon"]');

    radioButtons.each(function() {
        var $this = $(this);
        var imageUrl = $this.val();
        var $label = $this.closest('li').find('label');
        var $image = $label.find('img');

        if ($image.length === 0) {
            // If image element doesn't exist, create it
            $image = $('<img>', {
                src: imageUrl,
                alt: 'Payment Icon',
                style: 'width: auto; height: 35px; vertical-align: middle;'
            });

            // Append image and radio button to the label
            $label.append($this).append($image);
        }
    });

    $('select[name="woocommerce_senangpay_icon"]').closest('tr').hide();
});

jQuery(document).ready(function($) {
    $('.woocommerce-checkout img').css({
        'max-height': '25px',
        'width': 'auto'
    });
});

