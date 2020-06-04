define([
    'jquery',
    'Magento_Vault/js/view/payment/method-renderer/vault',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, VaultComponent, globalMessageList, fullScreenLoader) {
    'use strict';

    return VaultComponent.extend({
        defaults: {
            template: 'Magento_Vault/payment/form',
            modules: {
                hostedFields: '${ $.parentName }.easypaydirect_gateway'
            }
        },

        /**
         * Get last 4 digits of card
         * @returns {String}
         */
        getMaskedCard: function () {
            return this.details.maskedCC;
        },

        /**
         * Get expiration date
         * @returns {String}
         */
        getExpirationDate: function () {
            return this.details.expirationDate;
        },

        /**
         * Get card type
         * @returns {String}
         */
        getCardType: function () {
            return this.details.type;
        },

        /**
         * Place order
         */
        placeOrder: function () {
            var self = this;

            self.getGatewayToken();
        },

        /**
         * Send request to get payment method nonce
         */
        getGatewayToken: function () {
            return self.gatewayToken;
        }
    });
});