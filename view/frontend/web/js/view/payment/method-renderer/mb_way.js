/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Eupago_MbWay/payment/form',
                phoneNumber: ''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'phoneNumber'
                    ]);
                return this;
            },

            getCode: function () {
                return 'mb_way';
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'phone_number': this.phoneNumber()
                    }
                };
            },
            getPhoneNumber: function () {
                return window.checkoutConfig.payment.mb_way.phoneNumber
            }
        });
    }
);