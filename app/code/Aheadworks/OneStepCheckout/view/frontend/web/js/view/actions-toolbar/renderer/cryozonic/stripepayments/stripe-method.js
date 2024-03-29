/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * Place order
         *
         * @param {Object} data
         * @param {Object} event
         */
        placeOrder: function (data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }
            this._beforeAction().done(function () {
                self._getMethodRenderComponent().stripeJsPlaceOrder();
            });
        },
    });
});
