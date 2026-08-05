define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (
    Component,
    rendererList
) {
    'use strict';

    rendererList.push({
        type: 'beautybop_paypal',
        component:
            'BeautyBop_Payments/js/view/payment/method-renderer/paypal-method'
    });

    return Component.extend({});
});