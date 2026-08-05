define([
    'Magento_Checkout/js/view/payment/default'
], function (
    Component
) {
    'use strict';

    return Component.extend({

        defaults: {
            template:
                'BeautyBop_Payments/payment/paypal'
        },

        /**
         * Payment method code.
         */
        getCode: function () {
            return 'beautybop_paypal';
        },

        /**
         * Continue to PayPal.
         */
        placeOrder: function () {

            this.selectPaymentMethod();

            window.location =
                '/beautybop_payments/checkout/start';
        }
    });
});